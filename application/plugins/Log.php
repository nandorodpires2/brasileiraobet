<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Log
 *
 * @author Fernando
 */
class Plugin_Log {
    
    public static function setLoginAcesso() {
        
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            throw new Exception("Usuário não logado");
        }
        
        $usuario = Zend_Auth::getInstance()->getIdentity();
        $session_id = Zend_Session::getId();
        $modelLogAcesso = new Model_DbTable_LogAcesso();    
        
        try {
            $modelLogAcesso->insert(array(
                'log_acesso_session' => $session_id,
                'usuario_id' => $usuario->usuario_id
            ));
        } catch (Exception $ex) {
            
        }        
        
    }
    
    public static function setLogoutAcesso() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            throw new Exception("Usuário não logado");
        }
        
        $usuario = Zend_Auth::getInstance()->getIdentity();
        $session_id = Zend_Session::getId();
        $modelLogAcesso = new Model_DbTable_LogAcesso();     
        
        try {
            $where = "log_acesso_session = '{$session_id}'";
            $modelLogAcesso->update(array('log_acesso_logout' => Zend_Date::now()->get('YYYY-MM-dd H:m:s')), $where);
        } catch (Exception $ex) {

        }
    }
    
}
