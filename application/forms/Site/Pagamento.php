<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pagamento
 *
 * @author Fernando
 */
class Form_Site_Pagamento extends App_Forms_Form {
    
    public function init() {
        
        /**
         * Numero do cartao
         */
        $cartaoNumero = new Zend_Form_Element_Text("usuario_cartao_numero");
        $cartaoNumero->setLabel("Número do Cartão: ");
        $cartaoNumero->setRequired();
        $cartaoNumero->setAttribs(array(
            'class' => 'form-control'
        ));        
        $this->addElement($cartaoNumero);
        
        /**
         * Nome no cartao
         
        $cartaoNome = new Zend_Form_Element_Text("usuario_cartao_nome");
        $cartaoNome->setLabel("Nome no Cartão: ");
        $cartaoNome->setAttribs(array(
            'class' => 'form-control'
        ));        
        $this->addElement($cartaoNome);
        */
        
        /**
         * Codigo de seguranca
         */
        $cartaoSeguranca = new Zend_Form_Element_Text("usuario_cartao_codigo");
        $cartaoSeguranca->setLabel("Código de Segurança: ");
        $cartaoSeguranca->setRequired();
        $cartaoSeguranca->setAttribs(array(
            'class' => 'form-control'
        ));        
        $this->addElement($cartaoSeguranca);
        
        /**
         * Mes validade cartao
         */
        $cartaoValidadeMes = new Zend_Form_Element_Select("usuario_cartao_mes");
        $cartaoValidadeMes->setLabel("Mês de Validade:");
        $cartaoValidadeMes->setRequired();
        $cartaoValidadeMes->setAttribs(array(
            'class' => 'form-control'
        ));
        $cartaoValidadeMes->setMultiOptions(array(
            1 => '01',
            2 => '02',
            3 => '03',
            4 => '04',
            5 => '05',
            6 => '06',
            7 => '07',
            8 => '08',
            9 => '09',
            10 => '10',
            11 => '11',
            12 => '12',
        ));
        $this->addElement($cartaoValidadeMes);        
        
        /**
         * Ano de validade do cartao
         */
        $cartaoValidadeAno = new Zend_Form_Element_Select("usuario_cartao_ano");
        $cartaoValidadeAno->setLabel("Ano de Validade:");
        $cartaoValidadeAno->setRequired();
        $cartaoValidadeAno->setAttribs(array(
            'class' => 'form-control'
        ));
        $cartaoValidadeAno->setMultiOptions($this->getValidadeAno());
        $this->addElement($cartaoValidadeAno);
        
        parent::init();
    }

    protected function getValidadeAno() {
        
        $validade_anos = null;
        
        $ano = (int)date("Y");
        
        for ($i = 0; $i <= 10; $i++) {
            $validade_anos[$ano + $i] = $ano + $i;
        }
                
        return $validade_anos;
    }
    
}
