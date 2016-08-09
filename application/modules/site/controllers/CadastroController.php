<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CadastroController
 *
 * @author Fernando
 */
class Site_CadastroController extends Zend_Controller_Action {
    
    public function init() {
        
    }
    
    public function indexAction() {
        
        $formCadastro = new Form_Site_Cadastro();
        $formCadastro->removeElement('usuario_cpf');
        $formCadastro->removeElement('usuario_datanascimento');
        $this->view->formCadastro = $formCadastro;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($formCadastro->isValid($data)) {
                
                $data = $formCadastro->getValues();
                
                // criptografa a senha
                $pluginPassword = new Plugin_Password($data['usuario_senha']);
                $data['usuario_senha'] = $pluginPassword->encrypt();
                
                // hash de validacao
                $data['usuario_validar_hash'] = md5(uniqid());
                unset($data['usuario_senha_repetir']);
                
                try {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                    
                    /**
                     * Cadastra o usuario
                     */
                    $modelUsuario = new Model_DbTable_Usuario();
                    $usuario_id = $modelUsuario->insert($data);
                    
                    if (Zend_Registry::get("config")->bonus->cadastro > 0) { 
                    
                        /**
                         * Bonus
                         */
                        $bonus = array(
                            'lancamento_descricao' => 'CRÉDITO CADASTRO',
                            'usuario_id' => $usuario_id,
                            'lancamento_valor' => Zend_Registry::get("config")->bonus->cadastro
                        );
                        $modelLancamento = new Model_DbTable_Lancamento();
                        $modelLancamento->insert($bonus);
                    
                    }
                    
                    /**
                     * Dados do usuario
                     */
                    $usuario = $modelUsuario->getById($usuario_id);
                    
                    /**
                     * Envia o email para validar a conta
                     */
                    $link = Zend_Registry::get("config")->url . "/cadastro/validar/hash/" . $data['usuario_validar_hash'];
                    $pluginMail = new Plugin_Mail();
                    $pluginMail->setDataMail("usuario", $usuario);
                    $pluginMail->setDataMail("link", $link);
                    $pluginMail->send("usuario-novo.phtml", "Cadastro Recebido", $usuario->usuario_email);
                    
                    /**
                     * Autentica
                     */
                    Zend_Auth::getInstance()->getStorage()->write($usuario);
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                    
                    /**
                     * Verifica se houve alguma indicacao para o usuario
                     */
                    $this->checkIndicacao($usuario);
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'success' => 'Cadastro realizado com sucesso'
                    ));
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'info' => 'Por favor acesse o e-mail informado no cadastro para validar sua conta'
                    ));
                    
                    $this->_redirect("/");
                    
                } catch (Exception $ex) {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                    
                    die($ex->getMessage());
                }
                
            }
        }
        
    }
    
    public function validarAction() {
        
        $this->_helper->viewRenderer->setNoRender();
        
        $hash = $this->getRequest()->getParam("hash");
        
        $modelUsuario = new Model_DbTable_Usuario();
        $usuario = $modelUsuario->getByField("usuario_validar_hash", $hash);
        
        if (!$usuario) {
            
        }
        
        try {
            $modelUsuario->updateById(array(
                'usuario_validar_hash' => null,
                'usuario_validado' => 1
            ), $usuario->usuario_id);
            
            $this->_helper->flashMessenger->addMessage(array(
                'success' => 'Cadastro validado com sucesso!'
            ));
            
        } catch (Exception $ex) {
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => 'Não foi possível validar seu cadastro! Favor entre em contato com a administração do sistema.'
            ));
        }
        
        $this->_redirect('/');
        
    }
    
    protected function checkIndicacao($indicado) {
        
        // verifica se tem indicacao
        $modelIndique = new Model_DbTable_Indique();
        $indicacao = $modelIndique->hasIndicacao($indicado->usuario_email);
        
        if ($indicacao) {
            
            // busca os dados do usuario que indicou
            $modelUsuario = new Model_DbTable_Usuario();
            $usuario = $modelUsuario->getById($indicacao->usuario_id);
            
            /**
             * Verifica se o usuario ja atingiu o limite de bonus 
             * O limite de bonus é a soma:
             * -> bonus de cadastro
             * -> bonus de indicacao
             */
            $modelLancamento = new Model_DbTable_Lancamento();
            $total_bonus = $modelLancamento->getTotalBonus($usuario->usuario_id);
            
            if ($total_bonus >= Zend_Registry::get("config")->bonus->limite) {
                return;
            }
            
            // setar o bonus pra quem inicou
            $lancamento = array(
                'lancamento_valor' => Zend_Registry::get("config")->bonus->indicacao,
                'lancamento_descricao' => "BÔNUS INDICAÇÃO DE AMIGO ({$indicado->usuario_nome})",
                'usuario_id' => $usuario->usuario_id
            );            
            $modelLancamento->insert($lancamento);
            
            // notifica quem indicou
            $modelNotificacao = new Model_DbTable_Notificacao();
            $conteudo = " 
                <p class='text-success'>Parabéns!</p>
                {$indicado->usuario_nome} aceitou sua indicação.                
            ";                            
            $notificacao = array(
                'usuario_id' => $usuario->usuario_id,
                'notificacao_conteudo' => $conteudo
            );
            $modelNotificacao->insert($notificacao);            
            
            // enviar o email para quem indicou
            $subjet = $usuario->usuario_nome. " aceitou sua indicação";
            $pluginMail = new Plugin_Mail();
            $pluginMail->setDataMail('indicado', $indicado);
            $pluginMail->setDataMail('usuario', $usuario);
            $pluginMail->send("usuario-indique-aceito.phtml", $subjet, $usuario->usuario_email);
            
            // atualiza a tabela de indicacao
            $update = array(
                'indique_aceito' => 1,
                'indique_data_aceito' => date("Y-m-d H:i:s")
            );
            $modelIndique->updateById($update, $indicacao->indique_id);
            
            return true;
            
        }
        
        return;
        
    }
    
}
