<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Partida
 *
 * @author Fernando
 */
class Model_DbTable_Partida extends App_Db_Table_Abstract {
    
    protected $_name = "partida";
    protected $_primary = "partida_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll();
        
        $select->joinInner(array('time1' => 'time'), 'partida.time_id_mandante = time1.time_id', array(
            'time_mandante_nome' => 'time1.time_nome',
            'time_mandante_escudo' => 'time1.time_escudo'
        ));
        $select->joinInner(array('time2' => 'time'), 'partida.time_id_visitante = time2.time_id', array(
            'time_visitante_nome' => 'time2.time_nome',
            'time_visitante_escudo' => 'time2.time_escudo'
        ));
        
        return $select;
    }

    /**
     * 
     * @param type $realizada
     * @return type
     */
    public function getPartidas($realizada = null, $vencida = false, $order = null, $limit = null) {
        $select = $this->getQueryAll();        
        
        if (null !== $realizada) {
            $select->where("partida_realizada = ?", $realizada);
        }
        
        if (!$vencida) {
            $select->where("partida_data >= now()");
        }
        
        //$select->order("partida_serie asc");
        //$select->order("partida_rodada asc");                
        if ($order) {
            $select->order($order);
        } else {
            $select->order("partida_data asc");
        }
        
        if (null !== $limit) {
            $select->limit($limit);
        }
        
        //echo $select->__toString();
        return $this->fetchAll($select);               
    }
    
    /**
     * 
     */
    public function getPartidasParcial() {
        $select = $this->getQueryAll();
        $select->joinLeft("parcial", "partida.partida_id = parcial.partida_id", array(
            'parcial_id',
            'parcial_placar_mandante',
            'parcial_placar_visitante',
            'parcial_vencedores',
            'parcial_atualizacao'
        ));
        $select->where("partida_realizada = ?", 0);
        $select->where("now() between partida_data and date_add(partida_data, INTERVAL 2 HOUR)");
        
        return $this->fetchAll($select);
    }

    /**
     * 
     * @return type
     */
    public function getTotalPremio() {
        
        $select = $this->select()
                ->from($this->_name, array(
                    'montante' => new Zend_Db_Expr("sum(partida_montante)")
                ))
                ->where("partida_vencedores > ?", 0)
                ->where("partida_realizada = ?", 1);
        
        $query = $this->fetchRow($select);
        
        return $query->montante ? $query->montante : 0;
        
    }
    
    /**
     * 
     */
    public function getTotalPremioInicial() {
        
        $select = $this->select()
                ->from($this->_name, array(
                    'montante' => new Zend_Db_Expr("sum(partida_valor * partida_fator_inicial)")
                ));
        
        $query = $this->fetchRow($select);
        
        return $query->montante ? $query->montante : 0;
        
    }
    
}
