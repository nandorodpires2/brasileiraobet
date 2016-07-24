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
        $modelDepositoValor = new Model_DbTable_DepositoValor();
        $deposito_valor = new Zend_Form_Element_Select("deposito_valor");
        $deposito_valor->setLabel("Selecione o valor desejado:");
        $deposito_valor->setRequired();
        $deposito_valor->setDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $deposito_valor->setAttribs(array(
            'class' => 'form-control bolder'
        ));
        $deposito_valor->setMultiOptions($modelDepositoValor->fetchPairs());
        $this->addElement($deposito_valor);
        
        /**
         * deposito_cupom
         */
        $deposito_cupom = new Zend_Form_Element_Text("deposito_cupom");
        $deposito_cupom->setLabel("Tem Cupom Promocional?");
        $deposito_cupom->setDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $deposito_cupom->setAttribs(array(
            'class' => 'form-control'
        ));
        $this->addElement($deposito_cupom);
        
        parent::init();
    }
    
}
