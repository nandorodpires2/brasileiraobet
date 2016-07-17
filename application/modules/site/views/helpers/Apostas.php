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
    
    public function apostas($partida_id, $tipo = "numero") {
        
        if (!Plugin_Auth::check()) {
            return "";
        }
        
        $modelAposta = new Model_DbTable_Aposta();
        $apostas = $modelAposta->getApostas($partida_id, Zend_Auth::getInstance()->getIdentity()->usuario_id);
        
        $text = "";
        
        if ($tipo === 'numero') {

            if ($apostas->count() > 0) {
                $text = "Você fez {$apostas->count()} apostas nesta partida";
            } else {
                $text = "Você não apostou nesta partida";
            }
        } 
        
        if ($tipo === 'label') {
            if ($apostas->count() > 0) {
                $text = "<label>Suas apostas:</label><br />";
                foreach ($apostas as $aposta) {
                    $text .= "<label class='label label-info' style='margin: 0 5px;'>{$aposta->aposta_placar_mandante} X {$aposta->aposta_placar_visitante}</label>";
                }
            } else {
                $text = "Você ainda não apostou nesta partida";
            }
        }
        
        return $text;
        
    }
    
}
