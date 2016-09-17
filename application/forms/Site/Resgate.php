<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Resgate
 *
 * @author Fernando
 */
class Form_Site_Resgate extends App_Forms_Form {
    
    public function init() {
        
        /**
         * usuario_nome
         */
        $usuario_nome = new Zend_Form_Element_Text("usuario_nome");
        $usuario_nome->setLabel("Titular da Conta:");
        $usuario_nome->setRequired(false);
        $usuario_nome->setDecorators(App_Forms_Decorators::$simpleElementDecorators);        
        $usuario_nome->setAttribs(array(
            'class' => 'form-control',
            'disabled' => true
        ));
        $this->addElement($usuario_nome);
        
        /**
         * usuario_cpf
         */
        $usuario_cpf = new Zend_Form_Element_Text("usuario_cpf");
        $usuario_cpf->setLabel("CPF do Titular:");
        $usuario_cpf->setRequired(false);
        $usuario_cpf->setDecorators(App_Forms_Decorators::$simpleElementDecorators);                
        $usuario_cpf->setAttribs(array(
            'class' => 'form-control',
            'disabled' => true
        ));
        $usuario_cpf->addValidator(new Form_Validate_Cpf());
        $this->addElement($usuario_cpf);
        
        /**
         * resgate_valor
         */
        $resgate_valor = new Zend_Form_Element_Text("resgate_valor");
        $resgate_valor->setLabel("Valor: ");
        $resgate_valor->setAttribs(array(
            'class' => 'form-control'
        ));
        $resgate_valor->addValidator(new Form_Validate_ResgateValor);
        $resgate_valor->setDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $resgate_valor->setRequired();
        $this->addElement($resgate_valor);
        
        /**
         * resgate_conta_banco
         */
        $resgate_conta_banco = new Zend_Form_Element_Select("resgate_conta_banco");
        $resgate_conta_banco->setLabel("Banco: ");
        $resgate_conta_banco->setAttribs(array(
            'class' => 'form-control'
        ));
        $resgate_conta_banco->setMultiOptions(array(
            '' => 'Selecione um banco',
            'Bradesco S/A' => 'Bradesco S/A',
            'Caixa Econômica Federal' => 'Caixa Econômica Federal',
            'Banco do Brasil S/A' => 'Banco do Brasil S/A',
            'Itaú S/A' => 'Itaú S/A',
        ));
        $resgate_conta_banco->setRequired();
        $resgate_conta_banco->setDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $this->addElement($resgate_conta_banco);
        
        /**
         * resgate_conta_agencia
         */
        $resgate_conta_agencia = new Zend_Form_Element_Text("resgate_conta_agencia");
        $resgate_conta_agencia->setLabel("Agência: ");
        $resgate_conta_agencia->setAttribs(array(
            'class' => 'form-control',
            'placeholder' => 'Agência sem dígito'
        ));
        $resgate_conta_agencia->setRequired();
        $resgate_conta_agencia->setDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $this->addElement($resgate_conta_agencia);
        
        /**
         * resgate_conta_numero
         */
        $resgate_conta_numero = new Zend_Form_Element_Text("resgate_conta_numero");
        $resgate_conta_numero->setLabel("Conta: ");
        $resgate_conta_numero->setAttribs(array(
            'class' => 'form-control',
            'placeholder' => 'Conta com dígito'
        ));
        $resgate_conta_numero->setRequired();
        $resgate_conta_numero->setDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $this->addElement($resgate_conta_numero);
        
        parent::init();
    }
    
}
