<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Contato
 *
 * @author Fernando
 */
class Form_Site_Contato extends App_Forms_Form {
    
    public function init() {
        
        /**
         * contato_nome
         */
        $contato_nome = new Zend_Form_Element_Text("contato_nome");
        $contato_nome->setLabel("Nome: ");
        $contato_nome->setAttribs(array(
            'class' => 'form-control',
            'placeholder' => 'Informe seu nome'
        ));
        $contato_nome->setRequired();
        $contato_nome->setDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $this->addElement($contato_nome);
        
        /**
         * contato_email
         */
        $contato_email = new Zend_Form_Element_Text("contato_email");
        $contato_email->setLabel("Email: ");
        $contato_email->setAttribs(array(
            'class' => 'form-control',
            'placeholder' => 'Informe seu email'
        ));
        $contato_email->setRequired();
        $contato_email->setDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $contato_email->addValidators(array(
            'EmailAddress'
        ));
        $this->addElement($contato_email);
        
        /**
         * contato_assunto
         */
        $contato_assunto = new Zend_Form_Element_Text("contato_assunto");
        $contato_assunto->setLabel("Assunto: ");
        $contato_assunto->setAttribs(array(
            'class' => 'form-control',
            'placeholder' => 'Informe o assunto'
        ));
        $contato_assunto->setRequired();
        $contato_assunto->setDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $this->addElement($contato_assunto);
        
        /**
         * contato_mensagem
         */
        $contato_mensagem = new Zend_Form_Element_Textarea("contato_mensagem");
        $contato_mensagem->setLabel("Mensagem: ");
        $contato_mensagem->setAttribs(array(
            'class' => 'form-control',
            'placeholder' => 'Digite a mensagem',
            'rows' => 10
        ));
        $contato_mensagem->setRequired();
        $contato_mensagem->setDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $this->addElement($contato_mensagem);
                
        parent::init();
    }
    
}
