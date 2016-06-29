<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsuarioCartao
 *
 * @author Fernando
 */
class Model_DbTable_UsuarioCartao extends App_Db_Table_Abstract {
    
    protected $_name = "usuario_cartao";
    protected $_primary = "usuario_cartao_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll();
        return $select;
    }
    
    public function getCartaoUsuario($usuario_id) {
        
        $select = $this->getQueryAll()
                ->where("usuario_id = ?", $usuario_id);
        
        return $this->fetchRow($select);
        
    }

    public function insert(array $data) {
        
        $where = $this->getDefaultAdapter()->quoteInto("usuario_cartao_numero = ?", $data['usuario_cartao_numero']);
        $cartao = $this->fetchRow($where);
        
        if (!$cartao) {
            return parent::insert($data);
        }
        
        return true;
        
    }
    
}
