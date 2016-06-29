<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Partida
 *
 * @author Fernando
 */
class Form_Admin_Partida extends App_Forms_Form {
    
    public function init() {
        
        $modelTime = new Model_DbTable_Time();
        
        /**
         * partida_serie
         */        
        $partida_serie = new Zend_Form_Element_Text("partida_serie");
        $partida_serie->setLabel("Série: ");
        $partida_serie->setRequired();
        $partida_serie->setValue(1);
        $partida_serie->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($partida_serie);
        
        /**
         * partida_rodada
         */        
        $partida_rodada = new Zend_Form_Element_Text("partida_rodada");
        $partida_rodada->setLabel("Rodada: ");
        $partida_rodada->setRequired();
        $partida_rodada->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($partida_rodada);
        
        /**
         * partida_data
         */        
        $partida_data = new Zend_Form_Element_Text("partida_data");
        $partida_data->setLabel("Data: ");
        $partida_data->setRequired();
        $partida_data->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($partida_data);
        
        /**
         * partida_horario
         */        
        $partida_horario = new Zend_Form_Element_Text("partida_horario");
        $partida_horario->setLabel("Horário: ");
        $partida_horario->setRequired();
        $partida_horario->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($partida_horario); 
        
        /**
         * partida_valor
         */        
        $partida_valor = new Zend_Form_Element_Text("partida_valor");
        $partida_valor->setLabel("Valor (R$): ");
        $partida_valor->setRequired();
        $partida_valor->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($partida_valor); 
        
        /**
         * time_id_mandante
         */
        $time_id_mandante = new Zend_Form_Element_Select("time_id_mandante");
        $time_id_mandante->setLabel("Time Mandante: ");
        $time_id_mandante->setRequired();
        $time_id_mandante->setAttribs(array(
            'class' => 'form-control'
        ));
        $time_id_mandante->setMultiOptions($modelTime->fetchPairs());
        $this->addElement($time_id_mandante);
        
        /**
         * time_id_visitante
         */
        $time_id_visitante = new Zend_Form_Element_Select("time_id_visitante");
        $time_id_visitante->setLabel("Time Visitante: ");
        $time_id_visitante->setRequired();
        $time_id_visitante->setAttribs(array(
            'class' => 'form-control'
        ));
        $time_id_visitante->setMultiOptions($modelTime->fetchPairs());
        $this->addElement($time_id_visitante);
        
        
        parent::init();
    }
    
}
