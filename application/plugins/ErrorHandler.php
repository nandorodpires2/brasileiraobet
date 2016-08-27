<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ErrorHandler
 *
 * @author Fernando Rodrigues
 */
class Plugin_ErrorHandler extends Zend_Controller_Plugin_Abstract {
    
    public function routeShutdown (Zend_Controller_Request_Abstract $request) {
        $front = Zend_Controller_Front::getInstance();
        if (!($front->getPlugin('Zend_Controller_Plugin_ErrorHandler') instanceof Zend_Controller_Plugin_ErrorHandler)) {
            return;
        }
        $error = $front->getPlugin('Zend_Controller_Plugin_ErrorHandler');
        $testRequest = new Zend_Controller_Request_Http();
        $testRequest->setModuleName($request->getModuleName())
                    ->setControllerName($error->getErrorHandlerController())
                    ->setActionName($error->getErrorHandlerAction());
        
        if ($front->getDispatcher()->isDispatchable($testRequest)) {
            $error->setErrorHandlerModule($request->getModuleName());
        }
    }
    
}
