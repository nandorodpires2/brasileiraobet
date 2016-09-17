<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConfigController
 *
 * @author Fernando
 */
class Admin_ConfigController extends Zend_Controller_Action {
    
    public function init() {
        
    }
    
    public function indexAction() {
        
        /**
         * Busca os registros
         */
        $modelConfig = new Model_DbTable_Config();
        $configs = $modelConfig->fetchAll();
        $this->view->configs = $configs;
        
        /**
         * Form
         */
        $formConfig = new Form_Admin_Config();
        $this->view->formConfig = $formConfig;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
        }
        
    }
    
}
