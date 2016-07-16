<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TesteController
 *
 * @author Fernando
 */
class Cron_TesteController extends Zend_Controller_Action {

    public function init() {

        $server = $this->getRequest()->getServer('REMOTE_ADDR');        
        $cron_server = Zend_Registry::get("config")->cron->server;
        
        Zend_Debug::dump($server);
        Zend_Debug::dump($cron_server);
        
        if (!Plugin_Cron::checkAccess()) {
            die('sem permissao');
        }
        
        echo 'passou';
        
    }

    public function indexAction() {
        $this->disabled();
    }

    private function disabled() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

}
