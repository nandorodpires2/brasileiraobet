<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsuarioController
 *
 * @author Fernando
 */
class Admin_UsuarioController extends Zend_Controller_Action {
    
    public function init() {
        
    }
    
    public function indexAction() {
        
    }

    public function maquinaAction() {
        
        $this->_helper->viewRenderer->setNoRender();
        
        $qtde_usuarios = $this->getRequest()->getParam("qtde", 50);
        
        Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
        
        for ($i = 0; $i < $qtde_usuarios; $i++) {
            
            $uniqid = uniqid();
            $data = array(
                'usuario_nome' => 'USUÁRIO MAQUINA ' . $uniqid,
                'usuario_email' => "usuario{$uniqid}@maquina.com",
                'usuario_maquina' => 1
            );
            $modelUsuario = new Model_DbTable_Usuario();
            
            try {
                
                $modelUsuario->insert($data);
                
            } catch (Exception $ex) {
                Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                
                $this->_helper->flashMessenger->addMessage(array(
                    'danger' => $ex->getMessage()
                ));

                $this->_redirect("admin/usuario");
                
            }
            
        }
        
        Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
        
        $this->_helper->flashMessenger->addMessage(array(
            'success' => "{$qtde_usuarios} usuários cadastrados com sucesso"
        ));
            
        $this->_redirect("admin/usuario");
        
    }
    
}
