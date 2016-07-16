<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Emprestimo
 *
 * @author Fernando
 */
class Model_DbTable_Emprestimo extends App_Db_Table_Abstract {
    
    protected $_name = "emprestimo";
    protected $_primary = "emprestimo_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll();
        return $select;
    }
    
    /**
     * 
     * @param type $usuario_id
     * @return type
     */
    public function getPendenteByUsuarioId($usuario_id) {
        
        $select = $this->getQueryAll()
                ->columns(array(
                    'emprestimo_pendente' => new Zend_Db_Expr('ifnull(sum(emprestimo_valor), 0)')
                ))
                ->where("emprestimo_pago = ?", 0)
                ->where("usuario_id = ?", $usuario_id);
        
        return $this->fetchRow($select);
        
    }
    
}
