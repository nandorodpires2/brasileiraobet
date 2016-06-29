<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Email
 *
 * @author Fernando
 */
class Form_Validate_Email extends Zend_Validate_Abstract {
    
    const NOT_VALID = 'notValid';

    protected $_messageTemplates = array(
        self::NOT_VALID => 'Já existe um cadastro com o e-mail informado',        
    );

    public function isValid($value, $context = null) {
        
        $modelUsuario = new Model_DbTable_Usuario();
        $usuario = $modelUsuario->getByField("usuario_email", $value);
        
        if ($usuario) {
            $this->_error(self::NOT_VALID);
            return false;
        }
        return true;
        
    }
    
}
