<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Config
 *
 * @author Fernando
 */
class Plugin_Config {
    
    public function __construct($slug) {        
        
    }
    
    public static function getValorBySlug($slug) {
        
        $modelConfig = new Model_DbTable_Config();
        $config = $modelConfig->getByField('config_slug', $slug);
        
        if ($config) {
            return $config->config_valor;
        }
        
        return null;
        
    }

}
