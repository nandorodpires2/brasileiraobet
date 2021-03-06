<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Resgate
 *
 * @author Fernando
 */
class Model_DbTable_Resgate extends App_Db_Table_Abstract {
    
    protected $_name = "resgate";
    protected $_primary = "resgate_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll()
                ->joinInner("usuario", "resgate.usuario_id = usuario.usuario_id", array('*'));
        return $select;
    }
    
    public function getMontante($processado = 1) {
        $select = $this->getQueryAll()
                ->columns(array('montante' => new Zend_Db_Expr("sum(resgate_valor)")))
                ->where("resgate_processado = ?", $processado);
        
        $query = $this->fetchRow($select);   
        return $query->montante ? $query->montante : 0;
    }
    
    /**
     * 
     * @param type $usuario_id
     * @return type
     */
    public function getResgatesUsuario($usuario_id) {
        
        $select = $this->getQueryAll()
                ->where("usuario.usuario_id = ?", $usuario_id);
        
        return $this->fetchAll($select);
        
    }
    
    public function getQuery($where = null, $order = null, $limit = 0) {
        $select = $this->getQueryAll();
        
        if ($where) {
            $select->where($where);
        }                
        
        if ($order) {
            $select->order($order);
        }
        
        return $select;
    }
    
}
