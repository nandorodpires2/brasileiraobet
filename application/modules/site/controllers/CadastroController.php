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
        $this->view->formCadastro = $formCadastro;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($formCadastro->isValid($data)) {
                
                $data = $formCadastro->getValues();
                $data['usuario_senha'] = md5($data['usuario_senha']);
                                
                try {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                    
                    /**
                     * Cadastra o usuario
                     */
                    $modelUsuario = new Model_DbTable_Usuario();
                    $usuario_id = $modelUsuario->insert($data);
                    
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
                    
                    /**
                     * Dados do usuario
                     */
                    $usuario = $modelUsuario->getById($usuario_id);
                    
                    /**
                     * Autentica
                     */
                    Zend_Auth::getInstance()->getStorage()->write($usuario);
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                    
                    $this->_redirect("/");
                    
                } catch (Exception $ex) {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                    
                    die('error');
                }
                
            }
        }
        
    }
    
}
