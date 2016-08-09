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
        $usuario_nome->setDecorators(App_Forms_Decorators::$simpleElementDecorators);        
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
        $usuario_email->setDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $usuario_email->addValidator(new Zend_Validate_EmailAddress());
        $usuario_email->addValidator(new Form_Validate_Email());
        $usuario_email->setAttribs(array(
            'class' => 'form-control',
        ));
        $this->addElement($usuario_email);
        
        /**
         * usuario_cpf
         */
        $usuario_cpf = new Zend_Form_Element_Text("usuario_cpf");
        $usuario_cpf->setLabel("CPF:");
        $usuario_cpf->setRequired(false);
        $usuario_cpf->setDecorators(App_Forms_Decorators::$simpleElementDecorators);                
        $usuario_cpf->setAttribs(array(
            'class' => 'form-control',
        ));
        $this->addElement($usuario_cpf);
        
        /**
         * usuario_datanascimento
         */
        $usuario_datanascimento = new Zend_Form_Element_Text("usuario_datanascimento");
        $usuario_datanascimento->setLabel("Data de nascimento:");
        $usuario_datanascimento->setRequired(false);
        $usuario_datanascimento->setDecorators(App_Forms_Decorators::$simpleElementDecorators);        
        $usuario_datanascimento->setAttribs(array(
            'class' => 'form-control',
        ));
        $this->addElement($usuario_datanascimento);
         
        /**
         * usuario_senha
         */
        $usuario_senha = new Zend_Form_Element_Password("usuario_senha");
        $usuario_senha->setLabel("Senha:");
        $usuario_senha->setRequired();
        $usuario_senha->setDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $usuario_senha->setAttribs(array(
            'class' => 'form-control',
        ));
        $this->addElement($usuario_senha);
        
        /**
         * usuario_senha_repetir_repetir
         */
        $usuario_senha_repetir = new Zend_Form_Element_Password("usuario_senha_repetir");
        $usuario_senha_repetir->setLabel("Repita a senha:");
        $usuario_senha_repetir->setRequired();
        $usuario_senha_repetir->setDecorators(App_Forms_Decorators::$simpleElementDecorators);
        $usuario_senha_repetir->addValidator(new Form_Validate_SenhaRepetir());
        $usuario_senha_repetir->setAttribs(array(
            'class' => 'form-control',
        ));
        $this->addElement($usuario_senha_repetir);
        
        /**
         * usuario_maioridade
         */
        $usuario_maioridadade = new Zend_Form_Element_Checkbox("usuario_maioridade");
        $usuario_maioridadade->setLabel("Declaro ter 18 anos de idade ou mais");
        $usuario_maioridadade->setAttrib('checked', true);
        $usuario_maioridadade->setRequired();
        $usuario_maioridadade->setDecorators(App_Forms_Decorators::$checkboxElementDecorators_termo);
        $this->addElement($usuario_maioridadade);
        
        parent::init();
    }
    
}
