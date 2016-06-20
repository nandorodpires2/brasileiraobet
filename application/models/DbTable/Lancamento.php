<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Lancamento
 *
 * @author Fernando
 */
class Model_DbTable_Lancamento extends App_Db_Table_Abstract {
    
    protected $_name = "lancamento";
    protected $_primary = "lancamento_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll();
        return $select;
    }
    
    public function getSaldoUsuario($usuario_id) {
        $select = $this->select()
                ->from($this->_name, array(
                    'saldo' => new Zend_Db_Expr("sum(lancamento_valor)")
                ))
                ->where("usuario_id = ?", $usuario_id);
        
        $query = $this->fetchRow($select);
        
        return $query->saldo;
    }
    
    public function getExtrato($usuario_id, $limit = null, $order = null) {
        $select = $this->getQueryAll()
                ->where("usuario_id = ?", $usuario_id);
        
        if ($limit) {
            $select->limit($limit);
        }
        
        if ($order) {
            $select->order($order);
        }
        
        return $this->fetchAll($select);
    }
    
}
