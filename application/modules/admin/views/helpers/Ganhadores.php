<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ganhadores
 *
 * @author Fernando
 */
class Zend_View_Helper_Ganhadores extends Zend_View_Helper_Abstract {
    
    public function ganhadores($partida_id) {
        $modelAposta = new Model_DbTable_Aposta();
        $vencedores = $modelAposta->getApostasVencedoras($partida_id);
        
        return $vencedores;
    }
    
}
