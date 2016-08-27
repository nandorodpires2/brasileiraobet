<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Classificacao
 *
 * @author Fernando
 */
class Form_Admin_Classificacao extends App_Forms_Form {
    
    public function init() {
        
        /**
         * time_id
         */
        $time_id = new Zend_Form_Element_Hidden("time_id");
        $this->addElement($time_id);
        
        /**
         * classificacao_complemento_jogos
         */
        $classificacao_complemento_jogos = new Zend_Form_Element_Text("classificacao_complemento_jogos");
        $classificacao_complemento_jogos->setLabel("Jogos: ");
        $classificacao_complemento_jogos->setRequired();
        $classificacao_complemento_jogos->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($classificacao_complemento_jogos);
        
        /**
         * classificacao_complemento_pontos
         */
        $classificacao_complemento_pontos = new Zend_Form_Element_Text("classificacao_complemento_pontos");
        $classificacao_complemento_pontos->setLabel("Pontos: ");
        $classificacao_complemento_pontos->setRequired();
        $classificacao_complemento_pontos->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($classificacao_complemento_pontos);
        
        /**
         * classificacao_complemento_vitorias
         */
        $classificacao_complemento_vitorias = new Zend_Form_Element_Text("classificacao_complemento_vitorias");
        $classificacao_complemento_vitorias->setLabel("Vitórias: ");
        $classificacao_complemento_vitorias->setRequired();
        $classificacao_complemento_vitorias->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($classificacao_complemento_vitorias);
        
        /**
         * classificacao_complemento_empates
         */
        $classificacao_complemento_empates = new Zend_Form_Element_Text("classificacao_complemento_empates");
        $classificacao_complemento_empates->setLabel("Empates: ");
        $classificacao_complemento_empates->setRequired();
        $classificacao_complemento_empates->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($classificacao_complemento_empates);
        
        /**
         * classificacao_complemento_derrotas
         */
        $classificacao_complemento_derrotas = new Zend_Form_Element_Text("classificacao_complemento_derrotas");
        $classificacao_complemento_derrotas->setLabel("Derrotas: ");
        $classificacao_complemento_derrotas->setRequired();
        $classificacao_complemento_derrotas->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($classificacao_complemento_derrotas);
        
        /**
         * classificacao_complemento_golspro
         */
        $classificacao_complemento_golspro = new Zend_Form_Element_Text("classificacao_complemento_golspro");
        $classificacao_complemento_golspro->setLabel("Gols Pro: ");
        $classificacao_complemento_golspro->setRequired();
        $classificacao_complemento_golspro->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($classificacao_complemento_golspro);
        
        /**
         * classificacao_complemento_golscontra
         */ 
        $classificacao_complemento_golscontra = new Zend_Form_Element_Text("classificacao_complemento_golscontra");
        $classificacao_complemento_golscontra->setLabel("Gols Contra: ");
        $classificacao_complemento_golscontra->setRequired();
        $classificacao_complemento_golscontra->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($classificacao_complemento_golscontra);
        
        parent::init();
    }
    
}
