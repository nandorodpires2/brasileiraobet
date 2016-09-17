<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Config
 *
 * @author Fernando
 */
class Form_Admin_Config extends App_Forms_Form {
    
    public function init() {
        
        /**
         * config_slug
         */
        $config_slug = new Zend_Form_Element_Text("config_slug");
        $config_slug->setLabel("Slug:");
        $config_slug->setRequired();
        $config_slug->setAttribs(array(
            'class' => 'form-control',
            'placeholder' => 'EX: NOME_DO_SLUG'
        ));
        $this->addElement($config_slug);
        
        /**
         * config_descricao
         */
        $config_descricao = new Zend_Form_Element_TextArea("config_descricao");
        $config_descricao->setLabel("Descrição:");
        $config_descricao->setRequired();
        $config_descricao->setAttribs(array(
            'class' => 'form-control',
            'placeholder' => '',
            'rows' => 5 
        ));
        $this->addElement($config_descricao);
        
        /**
         * config_valor
         */
        $config_valor = new Zend_Form_Element_Text("config_valor");
        $config_valor->setLabel("Valor:");
        $config_valor->setRequired();
        $config_valor->setAttribs(array(
            'class' => 'form-control',
            'placeholder' => ''
        ));
        $this->addElement($config_valor);
        
        parent::init();
    }
    
}
