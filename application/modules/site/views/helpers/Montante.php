<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Montante
 *
 * @author Fernando
 */
class Zend_View_Helper_Montante extends Zend_View_Helper_Abstract {
    
    public function montante($partida_id) {
        
        $pluginPremio = new Plugin_Premio($partida_id);        
        return $pluginPremio->getPrimeiroPremio();
        
    }
    
}
