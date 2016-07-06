<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Indique
 *
 * @author Fernando
 */
class Model_DbTable_Indique extends App_Db_Table_Abstract {
    
    protected $_name = "indique";
    protected $_primary = "indique_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll();
        return $select;
    }

    public function hasIndicacao($email) {
        $select = $this->getQueryAll()
                ->where('indique_email = ?', $email)
                ->where('indique_aceito = ?', 0);
        
        return $this->fetchRow($select);
    }
    
}
