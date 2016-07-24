<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DepositoValor
 *
 * @author Fernando
 */
class Model_DbTable_DepositoValor extends App_Db_Table_Abstract {
    
    protected $_name = "deposito_valor";
    protected $_primary = "deposito_valor_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll();
        return $select;
    }
    
    public function fetchPairs() {
        $options = array();
        
        $valores = $this->fetchAll("deposito_valor_ativo = 1");
        
        $zendCurrency = new Zend_Currency();
        foreach ($valores as $valor) {                       
            $options[$valor->deposito_valor] = $zendCurrency->toCurrency($valor->deposito_valor) . " + " . $zendCurrency->toCurrency($valor->deposito_valor_bonus) . " BÔNUS";
        }
        
        return $options;
    }
    
}
