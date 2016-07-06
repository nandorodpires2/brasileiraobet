<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Indique
 *
 * @author Fernando
 */
class Form_Site_Indique extends App_Forms_Form {
    
    public function init() {
        
        /**
         * indique_nome
         */
        $indique_nome = new Zend_Form_Element_Text("indique_nome");
        $indique_nome->setLabel("Nome Completo:");
        $indique_nome->setRequired();
        $indique_nome->setDecorators(App_Forms_Decorators::$simpleElementDecorators);        
        $indique_nome->setAttribs(array(
            'class' => 'form-control',
        ));
        $this->addElement($indique_nome);
        
        /**
         * indique_email
         */
        $indique_email = new Zend_Form_Element_Text("indique_email");
        $indique_email->setLabel("E-mail:");
        $indique_email->setRequired();
        $indique_email->setDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $indique_email->addValidator(new Zend_Validate_EmailAddress());
        //$indique_email->addValidator(new Form_Validate_Email());
        $indique_email->setAttribs(array(
            'class' => 'form-control',
        ));
        $this->addElement($indique_email);
                
        /**
         * usuario_id
         */
        $usuario_id = new Zend_Form_Element_Hidden("usuario_id");
        $usuario_id->setValue(Zend_Auth::getInstance()->getIdentity()->usuario_id);
        $usuario_id->setOrder(10);
        $this->addElement($usuario_id);        
        
        parent::init();
    }
    
}
