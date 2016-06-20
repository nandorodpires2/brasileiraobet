<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Betz
 *
 * @author Fernando
 */
class Zend_View_Helper_Apostas extends Zend_View_Helper_Abstract {
    
    public function apostas($partida_id) {
    
        $modelAposta = new Model_DbTable_Aposta();
        $apostas = $modelAposta->getApostas($partida_id);
        
        return $apostas->count();
        
    }
        
}
