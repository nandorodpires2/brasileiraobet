<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResgateController
 *
 * @author Fernando
 */
class Site_ResgateController extends Zend_Controller_Action {
    
    public function init() {
        
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => 'Você precisa estar logado para acessar esta página!'
            ));
            $this->_redirect("/");
        }
        
    }
    
    public function indexAction() {
        
    }
    
}
