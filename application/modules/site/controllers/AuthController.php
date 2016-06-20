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
                
                $authAdapter->setTableName('usuario')
                        ->setIdentityColumn('usuario_email')
                        ->setCredentialColumn('usuario_senha')
                        ->setIdentity($email)
                        ->setCredential(md5($senha));
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
                    
                    $this->_redirect("/");
                    
                } else {
                    die('invalid');
                }
                
            }
        }
        
    }
    
    public function logoutAction() {
        
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        
        $this->_redirect("/");
        
    }
    
}
