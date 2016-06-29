<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Login
 *
 * @author Fernando Rodrigues
 */
class Form_Admin_Login extends App_Forms_Form {
    
    public function init() {
        
        // admin_email
        $adminEmail = new Zend_Form_Element_Text("admin_email");
        $adminEmail->setLabel("E-mail: ");
        $adminEmail->setRequired();
        $adminEmail->addDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $adminEmail->setAttribs(array(
            'class' => 'form-control',
            'placeholder' => 'Informe o e-mail'
        ));
        $this->addElement($adminEmail);
        
        // admin_senha
        $adminSenha = new Zend_Form_Element_Password("admin_senha");
        $adminSenha->setLabel("Senha: ");
        $adminSenha->setRequired();
        $adminSenha->addDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $adminSenha->setAttribs(array(
            'class' => 'form-control',
            'placeholder' => 'Informe a senha'
        ));
        $this->addElement($adminSenha);
        
        // submit
        parent::init();
        
    }
    
}
