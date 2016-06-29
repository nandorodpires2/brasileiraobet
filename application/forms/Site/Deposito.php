<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Deposito
 *
 * @author Fernando
 */
class Form_Site_Deposito extends App_Forms_Form {
    
    public function init() {
        
        /**
         * deposito_valor
         */
        $deposito_valor = new Zend_Form_Element_Select("deposito_valor");
        $deposito_valor->setLabel("Selecione o valor desejado:");
        $deposito_valor->setRequired();
        $deposito_valor->setAttribs(array(
            'class' => 'form-control'
        ));
        $deposito_valor->setMultiOptions(array(
            30 => 'R$30,00',
            50 => 'R$50,00',
            100 => 'R$100,00',
            250 => 'R$250,00',
            500 => 'R$500,00',
        ));
        $this->addElement($deposito_valor);
        
        /**
         * deposito_cupom
         */
        $deposito_cupom = new Zend_Form_Element_Text("deposito_cupom");
        $deposito_cupom->setLabel("Tem Cupom Promocional?");
        $deposito_cupom->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($deposito_cupom);
        
        parent::init();
    }
    
}
