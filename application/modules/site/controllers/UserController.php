<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserController
 *
 * @author Fernando
 */
class Site_UserController extends Zend_Controller_Action {
    
    public function init() {
        
    }
    
    public function indexAction() {
        
    }
    
    public function userMachineAction() {
        $this->_helper->viewRenderer->setNoRender();
        
        $users = 50;
        
        $modelUsuario = new Model_DbTable_Usuario();
        for ($i = 0; $i < $users; $i++) {
            $aux = $i + 1;
            $data = array(
                'usuario_nome' => 'Usuário Máquina ' . $aux,
                'usuario_email' => 'user-machine-' . $aux,
                'usuario_maquina' => 1
            );
            $modelUsuario->insert($data);
        }
        
        $this->_redirect("/");        
    }
    
}
