<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Deposito
 *
 * @author Fernando
 */
class Model_DbTable_Deposito extends App_Db_Table_Abstract {
    
    protected $_name = "deposito";
    protected $_primary = "deposito_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll();
        return $select;
    }

    public function getMontante() {
        $select = $this->getQueryAll()
                ->columns(array('montante' => new Zend_Db_Expr("sum(deposito_valor)")))
                ->where("deposito_confirmado = ?", 1);
        
        $query = $this->fetchRow($select);   
        return $query->montante;
    }
    
}
