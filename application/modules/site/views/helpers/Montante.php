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
        
        /**
         * Dados da partida
         */
        $modelPartida = new Model_DbTable_Partida();
        $partida = $modelPartida->getById($partida_id);
        
        /**
         * Busca as apostas
         */
        $modelAposta = new Model_DbTable_Aposta();
        $montante = $modelAposta->getMontante($partida_id);
        
        $porcentagem_banca = (int)$partida->partida_porcentagem;
        
        $premio = ($partida->partida_valor * (int)$partida->partida_fator_inicial) + $montante->montante;
        
        $banca = ($premio / 100) * $porcentagem_banca;
        
        return $premio - $banca;
        
    }
    
}
