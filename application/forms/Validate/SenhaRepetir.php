<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SenhaRepetir
 *
 * @author Fernando
 */
class Form_Validate_SenhaRepetir extends Zend_Validate_Abstract {
    
    const NOT_VALID = 'notValid';

    protected $_messageTemplates = array(
        self::NOT_VALID => 'A confirmacao da senha é diferente da senha',        
    );

    public function isValid($value, $context = null) {
        
        // verifica se e valido
        if ($value !== $context['usuario_senha']) {
            $this->_error(self::NOT_VALID);
            return false;
        }
        return true;
        
    }
    
}