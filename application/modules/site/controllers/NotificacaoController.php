<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NotificacaoController
 *
 * @author Fernando
 */
class Site_NotificacaoController extends Zend_Controller_Action {
    
    public function init() {
        if (!Plugin_Auth::check()) {
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => 'Você precisa estar logado para acessar esta página!'
            ));
            $this->_redirect("/");
        }
    }
    
    public function todasLidasAction() {
        
        $this->_helper->viewRenderer->setNoRender();
        
        try {
            $usuario_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;        
            $modelNotificacao = new Model_DbTable_Notificacao();
            $update = array(
                'notificacao_lida' => 1,
                'notificacao_data_lida' => date("Y-m-d H:i:s")
            );
            $where = " 
                usuario_id = {$usuario_id}
                and notificacao_lida = 0
            ";
            
            $modelNotificacao->update($update, $where);
            
            $this->_helper->flashMessenger->addMessage(array(
                'success' => 'Todas as mensagem foram marcadas como lida'
            ));
            
        } catch (Exception $ex) {
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => $ex->getMessage()
            ));
        }        
        
        $this->_redirect("/");
        
    }
    
}
