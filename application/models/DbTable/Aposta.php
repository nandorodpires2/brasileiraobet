<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Aposta
 *
 * @author Fernando
 */
class Model_DbTable_Aposta extends App_Db_Table_Abstract {
    
    protected $_name = "aposta";
    protected $_primary = "aposta_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll();
        $select->joinInner(array('partida'), 'aposta.partida_id = partida.partida_id', array('*'));
        $select->joinInner(array('t1' => 'time'), 'partida.time_id_mandante = t1.time_id', array(
            'time_escudo_mandante' => 't1.time_escudo'
        ));
        $select->joinInner(array('t2' => 'time'), 'partida.time_id_visitante = t2.time_id', array(
            'time_escudo_visitante' => 't2.time_escudo'
        ));
        return $select;
    }

    public function get($usuario_id = null) {
        $select = $this->getQueryAll()
                ->where("aposta_processada = ?", 0);
        
        if ($usuario_id) {
            $select->where("usuario_id = ?", $usuario_id);
        }
        
        $select->order("partida.partida_data asc");
        $select->order("aposta.aposta_data asc");
        
        return $this->fetchAll($select);
        
    }
    
    public function getApostas($partida_id = null, $usuario_id = null) {
        $select = $this->getQueryAll();
        
        if ($partida_id) {
            $select->where("aposta.partida_id = ?", $partida_id);
        }
        
        if ($usuario_id) {
            $select->where("usuario_id = ?", $usuario_id);
        }
        
        return $this->fetchAll($select);
    }

    public function getMontante($partida_id) {
        
        $montante = 0;
        
        $select = $this->getQueryAll()
                ->columns(array('montante' => new Zend_Db_Expr("sum(partida_valor)")))
                ->where("partida.partida_id = ?", $partida_id);
        
        return $this->fetchRow($select);        
               
    }
    
    public function getTotalMontanteApostas() {
                
        $select = $this->getQueryAll()
                ->columns(array('montante' => new Zend_Db_Expr("sum(partida_valor)")));
                //->where("partida.partida_realizada = ?", 1);
        
        $query = $this->fetchRow($select);        
        
        return $query->montante;
               
    }
    
}
