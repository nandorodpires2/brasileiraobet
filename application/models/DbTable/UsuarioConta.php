<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsuarioConta
 *
 * @author Fernando
 */
class Model_DbTable_UsuarioConta extends App_Db_Table_Abstract {
    
    protected $_name = "usuario_conta";
    protected $_primary = "usuario_conta_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll();
        return $select;
    }

    public function getContaUsuario($usuario_id) {
        $select = $this->getQueryAll()
                ->where("usuario_id = ?", $usuario_id)
                ->order("1 desc");
        
        return $this->fetchRow($select);        
    }
    
}
