<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EsqueciSenha
 *
 * @author Fernando
 */
class Form_Site_EsqueciSenha extends App_Forms_Form {
    
    public function init() {
        
        /**
         * usuario_email
         */
        $usuario_email = new Zend_Form_Element_Text("usuario_email");
        $usuario_email->setLabel("E-mail:");
        $usuario_email->setRequired();
        $usuario_email->setDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $usuario_email->addValidator(new Zend_Validate_EmailAddress());
        //$usuario_email->addValidator(new Form_Validate_Email());
        $usuario_email->setAttribs(array(
            'class' => 'form-control',
        ));
        $this->addElement($usuario_email);
        
        parent::init();
    }
    
}
