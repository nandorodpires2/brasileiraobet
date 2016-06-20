<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Apostas
 *
 * @author Fernando
 */
class Zend_View_Helper_Apostas extends Zend_View_Helper_Abstract {
    
    public function apostas($partida_id) {
        
        if (!Plugin_Auth::check()) {
            return "";
        }
        
        $modelAposta = new Model_DbTable_Aposta();
        $apostas = $modelAposta->getApostas($partida_id, Zend_Auth::getInstance()->getIdentity()->usuario_id);
        
        $text = "";
        
        if ($apostas->count() > 0) {
            $text = "Você fez {$apostas->count()} apostas nesta partida";
        } else {
            $text = "Você ainda não apostou nesta partida";
        }
        
        return $text;
        
    }
    
}
