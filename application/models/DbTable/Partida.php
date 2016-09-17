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
            'time_mandante_escudo' => 'time1.time_escudo',
            'time_mandante_abrv' => 'time1.time_abrv'
        ));
        $select->joinInner(array('time2' => 'time'), 'partida.time_id_visitante = time2.time_id', array(
            'time_visitante_nome' => 'time2.time_nome',
            'time_visitante_escudo' => 'time2.time_escudo',
            'time_visitante_abrv' => 'time2.time_abrv'
        ));
        
        return $select;
    }

    /**
     * Busca os registros das partidas
     * 
     * @param array $where Índices válidos (vencida, realizada, processada e custom) - custom deve ser uma string personalizada
     * @param type $order ordenar os registros
     * @param type $limit limite de registros
     * @return Zend_Db_Table_Rowset
     */
    public function getPartidas(array $where = null, $order = null, $limit = null) {
        
        $select = $this->getQueryAll();        
        
        if ($where && is_array($where)) {
            
            // vencidas
            if (isset ($where['vencida'])) {
                $select->where("partida_data >= now()");
            }
            
            // realizadas
            if (isset($where['realizada']) && null !== $where['realizada']) {
                $select->where("partida_realizada = ?", $where['realizada']);
            }
            
            // processadas
            if (isset($where['processada']) && null !== $where['processada']) {
                $select->where("partida_processada = ?", $where['processada']);
            }
            
            // string (texto personalizado para where)
            if (isset($where['custom']) && null !== $where['custom']) {
                $select->where($where['custom']);
            }
            
            // time_id
            if (isset($where['time_id']) && '' !== $where['time_id']) {
                $select->where("partida.time_id_mandante = ? or partida.time_id_visitante = ?", $where['time_id']);
            }
            
            // partida_valor
            if (isset ($where['partida_valor']) && '' !== $where['partida_valor']) {
                $select->where("partida_valor <= ?", (float)$where['partida_valor']);
            }
            
        }
        
        if ($order) {
            $select->order($order);
        } else {
            $select->order("partida_data asc");
        }
        
        if (null !== $limit) {
            $select->limit($limit);
        }
        //echo $select->__toString(); die();
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
            'parcial_vencedores_premio1',
            'parcial_vencedores_premio2',
            'parcial_vencedores_premio3',
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
                    'montante' => new Zend_Db_Expr("sum(partida_valor_premio1)")
                ))
                ->where("partida_vencedores > ?", 0)
                ->where("partida_realizada = ?", 1);
        
        $query = $this->fetchRow($select);
        
        return $query->montante ? $query->montante : 0;
        
    }
    
    public function getPartidasRodadas() {
        
        $select = $this->select()
                ->from($this->_name)
                ->columns('partida_rodada')
                ->where('partida_realizada = ?', 0)
                ->group('partida_rodada')
                ->having("count(*) = ?", 10);
        
        return $this->fetchAll($select);
        
    }
    
    public function getPartidasMandante($time_id, $realizada = null) {
        $select = $this->getQueryAll();
        $select->where("time_id_mandante = ?", $time_id);
        
        if (null !== $realizada) {
            $select->where("partida_realizada = ?", $realizada);
        }
        
        return $this->fetchAll($select);               
    }
    
    public function getPartidasVisitante($time_id, $realizada = null) {
        $select = $this->getQueryAll();
        $select->where("time_id_visitante = ?", $time_id);
        
        if (null !== $realizada) {
            $select->where("partida_realizada = ?", $realizada);
        }
        
        return $this->fetchAll($select);    
    }
    
}
