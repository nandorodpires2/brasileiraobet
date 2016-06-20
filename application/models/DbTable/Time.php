<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Time
 *
 * @author Fernando
 */
class Model_DbTable_Time extends App_Db_Table_Abstract {
    
    protected $_name = "time";
    protected $_primary = "time_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll();
        
        // columns
        $select->columns(array(
            'divisao' => new Zend_Db_Expr(" 
                case time_divisao 
                    when 1 then 'Série A'
                    when 2 then 'Série B'
                    when 3 then 'Série C'
                end 
            ")
        ));
        
        $select->joinLeft(array('estado'), 'time.estado_id = estado.id_estado', array('*'));
        
        return $select;
    }

    public function fetchPairs() {
        $options = array('' => 'Selecione o time...');
        
        $where = null;
        $order = "time_nome asc";
        $values = $this->fetchAll($where, $order);
        
        foreach ($values as $value) {
            $options[$value->time_id] = $value->time_nome;
        }
        
        return $options;
        
    }
    
    public function getTimes($divisao = null) {
        $select = $this->getQueryAll();
        
        if ($divisao) {
            $select->where("time_divisao = ?", $divisao);
        }
        
        $select->order("time_divisao asc");
        $select->order("time_nome asc");
        
        return $this->fetchAll($select);
        
    }
    
    public function getTimesByEstado($estado_id, $divisao = null) {
        $select = $this->getQueryAll();        
        $select->where("estado_id = ?", $estado_id);        
        
        if ($divisao) {
            $select->where("time_divisao = ?", $divisao);
        }
        
        return $this->fetchAll($select);
    }
    
}
