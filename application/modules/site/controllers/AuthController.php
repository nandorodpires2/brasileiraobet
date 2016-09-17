<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthController
 *
 * @author Fernando
 */
class Site_AuthController extends Zend_Controller_Action {
    
    public function init() {
        
    }
    
    public function indexAction() {
        
    }
    
    public function loginAction() {
        
        $formLogin = new Form_Site_Login();        
        $this->view->formLogin = $formLogin;
                
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($formLogin->isValid($data)) {
                
                $email = $formLogin->getValue('usuario_email');                
                $senha = $formLogin->getValue('usuario_senha'); 
                                
                $db = Zend_Registry::get('db');               
                $authAdapter = new Zend_Auth_Adapter_DbTable($db);
                
                $pluginPassword = new Plugin_Password($senha);
                
                $authAdapter->setTableName('usuario')
                        ->setIdentityColumn('usuario_email')
                        ->setCredentialColumn('usuario_senha')
                        ->setIdentity($email)
                        ->setCredential($pluginPassword->encrypt());
                //$authAdapter->getDbSelect()->where("autenticacao_ativo = ?", 1);

                $auth = Zend_Auth::getInstance();                
                $result = $auth->authenticate($authAdapter);
                
                if ($result->isValid()) {     
                    
                    /**
                     * Busca os dados do usuario
                     */
                    $modelUsuario = new Model_DbTable_Usuario();
                    $usuario = $modelUsuario->getByField("usuario_email", $email);
                    
                    Zend_Auth::getInstance()->getStorage()->write($usuario);
                    
                    // log
                    Plugin_Log::setLoginAcesso();
                    
                    $this->_redirect("/");
                    
                } else {
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'danger' => 'Usuário e/ou senha inválidos!'
                    ));
                    
                    $this->_redirect("/");
                    
                }
                
            } else {
            
                $this->_helper->flashMessenger->addMessage(array(
                    'warning' => "Por favor preencha os campos de usuário e senha!"
                ));

                $this->_redirect("/");
            }
        } 
        
    }
    
    public function logoutAction() {
        
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        // log
        Plugin_Log::setLogoutAcesso();
        
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        
        $this->_redirect("/");
        
    }
    
    public function esqueciAction() {
        
        /**
         * Form
         */
        $formEsqueci = new Form_Site_EsqueciSenha();
        $formEsqueci->submit->setLabel("RECUPERAR A SENHA");
        $this->view->formEsqueci = $formEsqueci;
        
         if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($formEsqueci->isValid($data)) {
                
                $usuario_email = $formEsqueci->getValue("usuario_email");
                
                // verifica se existe o usuario
                $modelUsuario = new Model_DbTable_Usuario();
                $usuario = $modelUsuario->getByField("usuario_email", $usuario_email);
                
                if (!$usuario) {
                    $this->_helper->flashMessenger->addMessage(array(
                        'danger' => 'Não há menhum cadastro com este e-mail!'
                    ));
                    $this->_redirect("/auth/esqueci");
                }
                
                try {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                    
                    $hash = md5(uniqid());
                    $url = Zend_Registry::get("config")->url . "/auth/nova-senha/hash/" . $hash;
                    $update = array(
                        'usuario_senha' => null,
                        'usuario_hash' => $hash
                    );
                    $modelUsuario->updateById($update, $usuario->usuario_id);
                    
                    /**
                     * Envia o email
                     */
                    $pluginMail = new Plugin_Mail();
                    $pluginMail->setDataMail('usuario', $usuario);
                    $pluginMail->setDataMail('url', $url);
                    $pluginMail->send("usuario-senha-recuperar.phtml", "Recuperar a senha", $usuario_email);
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'success' => 'Foi enviado um link para recuperar sua senha para o email informado!'
                    ));
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                    
                } catch (Exception $ex) {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'danger' => $ex->getMessage()
                    ));
                }
                
                $this->_redirect("/auth/esqueci");
                
            }
         }
        
    }
    
    public function novaSenhaAction() {
        
        $hash = $this->getRequest()->getParam("hash");
        
        $modelUsuario = new Model_DbTable_Usuario();
        $usuario = $modelUsuario->getByField("usuario_hash", $hash);
        
        if (!$usuario) {
            
        }
        
        /**
         * Form
         */
        $form = new Form_Site_Cadastro();
        $form->removeElement("usuario_nome");
        $form->removeElement("usuario_email");
        $form->removeElement("usuario_maioridade");
        
        $form->usuario_senha->setLabel("Digite a nova senha:");
        $form->usuario_senha_repetir->setLabel("Repita a nova senha:");
        $form->submit->setLabel("ALTERAR SENHA");
        
        $this->view->form = $form;
        
         if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                
                try {
                    
                    $pluginPassword = new Plugin_Password($form->getValue("usuario_senha"));
                    $usuario_senha = $pluginPassword->encrypt();
                    
                    $modelUsuario->updateById(array(
                        'usuario_senha' => $usuario_senha,
                        'usuario_hash' => null
                    ), $usuario->usuario_id);
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'success' => 'Senha alterada com sucesso'
                    ));
                    
                    $this->_redirect("/");
                    
                } catch (Exception $ex) {
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'danger' => 'Não foi possível alterar a senha. Por favor tente novamente.'
                    ));
                    
                    $this->_redirect("/auth/esqueci");
                    
                }
                
            }
         }
        
    }
    
}
