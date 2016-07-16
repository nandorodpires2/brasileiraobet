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
class Admin_AuthController extends Zend_Controller_Action {
    
    public function init() {
        
    }
    
    public function loginAction() {
        
        $paramns = $this->getRequest()->getParams();
        $redirect = "/" . $paramns['module'] . "/" . $paramns['controller'] . "/" . $paramns['action'];
        
        /**
         * Form 
         */
        $form = new Form_Admin_Login();
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                
                $email = $form->getValue('admin_email');                
                $senha = $form->getValue('admin_senha'); 
                                
                $db = Zend_Registry::get('db');               
                $authAdapter = new Zend_Auth_Adapter_DbTable($db);
                
                $pluginPassword = new Plugin_Password($senha);
                $authAdapter->setTableName('admin')
                        ->setIdentityColumn('admin_email')
                        ->setCredentialColumn('admin_senha')
                        ->setIdentity($email)
                        ->setCredential($pluginPassword->encrypt());
                //$authAdapter->getDbSelect()->where("autenticacao_ativo = ?", 1);

                $auth = Zend_Auth::getInstance();                
                $result = $auth->authenticate($authAdapter);
                
                if ($result->isValid()) {   
                    
                    /**
                     * Busca os dados do usuario
                     */
                    $modelAdmin = new Model_DbTable_Admin();
                    $admin = $modelAdmin->getByField("admin_email", $email);
                    
                    Zend_Auth::getInstance()->getStorage()->write($admin);
                    
                    $this->_redirect($redirect);
                    
                } else {
                    die('error');
                }
                
            }
        }
        
    }
    
    public function logoutAction() {
        
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        
        $this->_redirect("/admin");
        
    }
    
    
}
