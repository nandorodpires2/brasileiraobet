<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Classificacao
 *
 * @author Fernando
 */
class Model_DbTable_Classificacao extends App_Db_Table_Abstract {
    
    protected $_name = "classificacao";    
    protected $_primary = "classificacao_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll();
        
        // columns
        $select->columns(array(
            'classificacao_saldogols' => new Zend_Db_Expr(" 
                classificacao_golspro - classificacao_golscontra
            "),
            'classificacao_aprov' => new Zend_Db_Expr(" 
                format((classificacao_pontos * 100) / (classificacao_jogos * 3), 2)
            ")
        ));     
        
        // join
        $select->joinInner(array('time'), 'classificacao.time_id = time.time_id', array('*'));     
        $select->joinLeft(array('estado'), 'time.estado_id = estado.id_estado', array('*'));     
        
        // order
        $select->order("classificacao_pontos DESC");
        $select->order("classificacao_vitorias DESC");
        $select->order("(classificacao_golspro - classificacao_golscontra) DESC");
        $select->order("classificacao_golspro DESC");
                
        return $select;
    }
    
    /**
     * 
     * @param type $divisao
     * @return type
     */
    public function getClassificacao($divisao = null) {
        $select = $this->getQueryAll();
        
        if ($divisao) {
            $select->where("time_divisao = ?", $divisao);
        }
        
        return $this->fetchAll($select);
        
    }
    
}
