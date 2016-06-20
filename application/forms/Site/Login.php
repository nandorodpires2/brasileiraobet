<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Login
 *
 * @author Fernando
 */
class Form_Site_Login extends App_Forms_Form {
    
    public function init() {
        
        $action = $this->getView()->url(array(
            'controller' => 'auth',
            'action' => 'login'
        ), null, true);
        $this->setAction($action);
        
        $this->setMethod("POST");
        
        /**
         * usuario_email
         */
        $usuario_email = new Zend_Form_Element_Text("usuario_email");
        $usuario_email->setLabel("E-mail:");
        $usuario_email->setRequired();
        $usuario_email->setAttribs(array(
            'class' => 'form-control',
        ));
        $this->addElement($usuario_email);
         
        /**
         * usuario_senha
         */
        $usuario_senha = new Zend_Form_Element_Password("usuario_senha");
        $usuario_senha->setLabel("Senha:");
        $usuario_senha->setRequired();
        $usuario_senha->setAttribs(array(
            'class' => 'form-control',
        ));
        $this->addElement($usuario_senha);
        
        parent::init();
    }
    
}
