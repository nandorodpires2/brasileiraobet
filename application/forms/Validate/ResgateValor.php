<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResgateValor
 *
 * @author Fernando
 */
class Form_Validate_ResgateValor extends Zend_Validate_Abstract {
    
    const NOT_MIN = 'notMax';
    const NOT_MAX = 'notMin';
    const NOT_LIMIT = 'notLimit';

    protected $_messageTemplates = array(
        self::NOT_MIN => 'Valor abaixo do mínimo!',        
        self::NOT_MAX => 'Valor acima do máximo!',
        self::NOT_LIMIT => 'Valor maior que o saldo disponível!'
    );

    public function isValid($value, $context = null) {
        
        $value = App_Helper_Currency::toCurrencyDb($value);
        
        /**
         * Saldo
         */
        $usuario_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
        $modelLancamento = new Model_DbTable_Lancamento();
        $saldo = $modelLancamento->getSaldoUsuario($usuario_id);
        
        if ($value < Plugin_Config::getValorBySlug("RESGATE_MINIMO")) {
            $this->_error(self::NOT_MIN);
            return false;
        }
        
        if ($value > Zend_Registry::get("config")->resgate->maximo) {
            $this->_error(self::NOT_MAX);
            return false;
        }
        
        if ($value > $saldo) {
            $this->_error(self::NOT_LIMIT);
            return false;
        }
        
        
        return true;
        
    }
}
