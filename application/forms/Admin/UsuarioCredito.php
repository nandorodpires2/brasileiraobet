<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsuarioCredito
 *
 * @author nando_000
 */
class Form_Admin_UsuarioCredito extends App_Forms_Form {
    
    public function init() {
        
        /**
         * lancamento_descricao
         */
        $lancamento_descricao = new Zend_Form_Element_Text("lancamento_descricao");
        $lancamento_descricao->setLabel("Descrição:");
        $lancamento_descricao->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($lancamento_descricao);
        
        /**
         * lancamento_valor
         */
        $lancamento_valor = new Zend_Form_Element_Text("lancamento_valor");
        $lancamento_valor->setLabel("Valor:");
        $lancamento_valor->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($lancamento_valor);
        
        parent::init();
    }
    
}
