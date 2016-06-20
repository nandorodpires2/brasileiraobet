<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Layout
 *
 * @author Fernando Rodrigues
 */
class Plugin_Layout extends Zend_Controller_Plugin_Abstract{
    
    public function preDispatch(\Zend_Controller_Request_Abstract $request) {
        
        $configs = array(
            'layout' => $request->getModuleName(),
            'layoutPath' => APPLICATION_PATH . '/layouts/scripts'
        );
        // inicia o componente
        Zend_Layout::startMvc($configs);
        
    }
    
}
