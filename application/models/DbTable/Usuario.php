<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author Fernando
 */
class Model_DbTable_Usuario extends App_Db_Table_Abstract {
    
    protected $_name = "usuario";
    protected $_primary = "usuario_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll();
        return $select;
    }

    public function getUsuariosMaquina() {
        $select = $this->getQueryAll()
                ->where("usuario_maquina = ?", 1);
        
        return $this->fetchAll($select);
    }
    
}
