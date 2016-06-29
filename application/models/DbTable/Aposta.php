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
            'time_nome_mandante' => 't1.time_nome',
            'time_escudo_mandante' => 't1.time_escudo'
        ));
        $select->joinInner(array('t2' => 'time'), 'partida.time_id_visitante = t2.time_id', array(
            'time_nome_visitante' => 't2.time_nome',
            'time_escudo_visitante' => 't2.time_escudo'
        ));
        $select->joinInner(array('usuario'), 'aposta.usuario_id = usuario.usuario_id', array('*'));
        
        return $select;
    }

    public function get($usuario_id = null) {
        $select = $this->getQueryAll()
                ->where("aposta_processada = ?", 0)
                ->where("partida.partida_data >= now()");
        
        if ($usuario_id) {
            $select->where("aposta.usuario_id = ?", $usuario_id);
        }
        
        $select->order("partida.partida_data asc");
        $select->order("aposta.aposta_data asc");
        
        return $this->fetchAll($select);
        
    }
    
    public function getApostas($partida_id = null, $usuario_id = null, $vencedora = null) {
        $select = $this->getQueryAll();
        
        if ($partida_id) {
            $select->where("aposta.partida_id = ?", $partida_id);
        }
        
        if ($usuario_id) {
            $select->where("aposta.usuario_id = ?", $usuario_id);
        }
        
        if (null !== $vencedora) {
            $select->where("aposta.aposta_vencedora = ?", $vencedora);
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
        
        return $query->montante ? $query->montante : 0;
               
    }
    
    public function getApostasVencedoras($partida_id) {
        $select = $this->getQueryAll()
                ->where("partida.partida_id = ?", $partida_id)
                ->where("partida_placar_mandante = aposta_placar_mandante and partida_placar_visitante = aposta_placar_visitante");                
        
        return $this->fetchAll($select);
    }
    
}
