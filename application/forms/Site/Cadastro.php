<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cadastro
 *
 * @author Fernando
 */
class Form_Site_Cadastro extends App_Forms_Form {
    
    public function init() {
        
        /**
         * usuario_nome
         */
        $usuario_nome = new Zend_Form_Element_Text("usuario_nome");
        $usuario_nome->setLabel("Nome Completo:");
        $usuario_nome->setRequired();
        $usuario_nome->setAttribs(array(
            'class' => 'form-control',
        ));
        $this->addElement($usuario_nome);
        
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
