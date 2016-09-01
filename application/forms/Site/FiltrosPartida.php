<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FiltrosPartida
 *
 * @author Fernando
 */
class Form_Site_FiltrosPartida extends App_Forms_Form {
    
    public function init() {
        
        $this->setMethod('post');
        
        /**
         * time_id
         */       
        $modelTime = new Model_DbTable_Time();
        $time_id = new Zend_Form_Element_Select('time_id');
        $time_id->setLabel("Equipe:");
        $time_id->setAttrib('class', 'form-control');
        $time_id->setMultiOptions($modelTime->fetchPairs());
        $this->addElement($time_id);
        
        /**
         * partida_valor
         */
        $partida_valor = new Zend_Form_Element_Select('partida_valor');
        $partida_valor->setLabel("Valor: ");
        $partida_valor->setAttrib('class', 'form-control');
        $partida_valor->setMultiOptions(array(
            '' => 'Selecione...',
            1 => 'até R$1,00',
            3 => 'até R$3,00',
            5 => 'até R$5,00'
        ));
        $this->addElement($partida_valor);
        
        parent::init();
    }
    
}
