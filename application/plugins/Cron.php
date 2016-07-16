<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cron
 *
 * @author Fernando
 */
class Plugin_Cron extends Zend_Controller_Plugin_Abstract {
    
    public static function checkAccess() {
    
        $request = Zend_Controller_Front::getInstance();        
        $server = $request->getRequest()->getServer('REMOTE_ADDR');
        
        $cron_server = Zend_Registry::get("config")->cron->server;
        
        if ($server == $cron_server) {
            return true;
        }
        
        return false;
    }
    
}
