<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Notificacao
 *
 * @author Fernando
 */
class Model_DbTable_Notificacao extends App_Db_Table_Abstract {
    
    protected $_name = "notificacao";
    protected $_primary = "notificacao_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll();
        return $select;
    }
    
    /**
     * 
     * @param type $usuario_id
     * @param type $lida
     * @return type
     */
    public function getNotificacoes($usuario_id = null, $lida = null) {
        $select = $this->getQueryAll();
        
        if ($usuario_id) {
            $select->where("usuario_id = ?", $usuario_id);
        }
        
        if (null !== $lida) {
            $select->where("notificacao_lida = ?", $lida);
        }
        
        $select->order("notificacao_data desc");
        
        return $this->fetchAll($select);
        
    }
        
}
