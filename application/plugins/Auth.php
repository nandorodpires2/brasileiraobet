<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Auth
 *
 * @author Fernando
 */
class Plugin_Auth extends Zend_Controller_Plugin_Abstract {
    
    public function preDispatch(\Zend_Controller_Request_Abstract $request) {
    
        $module = $request->getModuleName();       
        
        if ($module === 'admin') {
            $this->checkAdmin();
        }
        
    }
    
    public static function check() {
        if (Zend_Auth::getInstance()->hasIdentity() && isset(Zend_Auth::getInstance()->getIdentity()->usuario_id)) {
            return true;
        }
        return false;
    }
    
    public function checkAdmin() {
        
        if (!Zend_Auth::getInstance()->hasIdentity() && !isset(Zend_Auth::getInstance()->getIdentity()->admin_id)) {
            $this->getRequest()->setModuleName("admin")
                    ->setControllerName("auth")
                    ->setActionName("login");
        }
        
    }
    
    public static function isAdmin() {
        if (Zend_Auth::getInstance()->hasIdentity() && isset(Zend_Auth::getInstance()->getIdentity()->admin_id)) {
            return true;
        }
        return false;
    }
    
}
