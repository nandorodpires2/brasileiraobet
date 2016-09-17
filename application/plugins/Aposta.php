<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Aposta
 *
 * @author Fernando
 */
class Plugin_Aposta {
    
    protected $_partida;

    public function __construct($partida_id) {
        $modelPartida = new Model_DbTable_Partida();
        $this->_partida = $modelPartida->getById($partida_id);
        
        if (!$this->_partida) {
            throw new Exception("Partida não encontrada!");
        }
        
    }
    
    public function allow() {
        
        $zendDateNow = new Zend_Date();
        $zendDatePartida = new Zend_Date($this->_partida->partida_data);        
        
        //Zend_Debug::dump($zendDateNow->get(Zend_Date::DATETIME_SHORT)); die();
        
        if ($zendDatePartida->isEarlier($zendDateNow)) {
            return false;
        }
        return true;
    }
    
}
