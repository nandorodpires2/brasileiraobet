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
         * resgate_valor
         */
        $resgate_valor = new Zend_Form_Element_Text("resgate_valor");
        $resgate_valor->setLabel("Valor: ");
        $resgate_valor->setAttribs(array(
            'class' => 'form-control'
        ));
        $resgate_valor->setRequired();
        $this->addElement($resgate_valor);
        
        /**
         * usuario_conta_banco
         */
        $usuario_conta_banco = new Zend_Form_Element_Select("usuario_conta_banco");
        $usuario_conta_banco->setLabel("Banco: ");
        $usuario_conta_banco->setAttribs(array(
            'class' => 'form-control'
        ));
        $usuario_conta_banco->setMultiOptions(array(
            '' => 'Selecione um banco',
            'Bradesco S/A' => 'Bradesco S/A',
            'Caixa Econômica Federal' => 'Caixa Econômica Federal',
            'Banco do Brasil S/A' => 'Banco do Brasil S/A',
            'Itaú S/A' => 'Itaú S/A',
        ));
        $usuario_conta_banco->setRequired();
        $this->addElement($usuario_conta_banco);
        
        /**
         * usuario_conta_agencia
         */
        $usuario_conta_agencia = new Zend_Form_Element_Text("usuario_conta_agencia");
        $usuario_conta_agencia->setLabel("Agência: ");
        $usuario_conta_agencia->setAttribs(array(
            'class' => 'form-control'
        ));
        $usuario_conta_agencia->setRequired();
        $this->addElement($usuario_conta_agencia);
        
        /**
         * usuario_conta_numero
         */
        $usuario_conta_numero = new Zend_Form_Element_Text("usuario_conta_numero");
        $usuario_conta_numero->setLabel("Conta: ");
        $usuario_conta_numero->setAttribs(array(
            'class' => 'form-control'
        ));
        $usuario_conta_numero->setRequired();
        $this->addElement($usuario_conta_numero);
        
        parent::init();
    }
    
}
