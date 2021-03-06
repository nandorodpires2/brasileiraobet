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
class Site_UsuarioController extends Zend_Controller_Action {
    
    public function init() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => 'Você precisa estar logado para acessar esta página!'
            ));
            $this->_redirect("/");
        }
    }
    
    public function indexAction() {
        $this->_redirect("usuario/dados");
    }
    
    public function dadosAction() {
        
        /**
         * Busca os dados do usuario
         */
        $usuario_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
        $modelUsuario = new Model_DbTable_Usuario();
        $usuario = $modelUsuario->getById($usuario_id);
        
        /**
         * Form
         */
        $form = new Form_Site_Cadastro();
        
        $form->removeElement('usuario_senha');
        $form->removeElement('usuario_senha_repetir');
        $form->removeElement("usuario_maioridade");
        $form->usuario_email->removeValidator('email');
        
        $usuario->usuario_datanascimento = $usuario->usuario_datanascimento ? App_Helper_Date::getDate($usuario->usuario_datanascimento, Zend_Date::DATE_MEDIUM) : null;
                
        $form->populate($usuario->toArray());
        $form->submit->setLabel("ALTERAR");
        
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $data = $form->getValues();
                
                // formating date value
                $data['usuario_datanascimento'] = App_Helper_Date::getDateDb($data['usuario_datanascimento']);
                                
                try {
                    $modelUsuario->updateById($data, $usuario_id);
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'success' => 'Dados alterados com sucesso'
                    ));
                    
                } catch (Exception $ex) {
                    $this->_helper->flashMessenger->addMessage(array(
                        'danger' => $ex->getMessage()
                    ));
                }
                
                $this->_redirect("usuario/dados");
                
            }
        }
        
    }
    
    public function depositosAction() {
        
        /**
         * Busca os depositos do usuario
         */
        $usuario_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
        $modelDeposito = new Model_DbTable_Deposito();
        $depositos = $modelDeposito->getDepositosUsuario($usuario_id);
        $this->view->depositos = $depositos;
        
    }
    
    public function resgatesAction() {
        
        /**
         * 
         */
        $usuario_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
        $modelResgate = new Model_DbTable_Resgate();
        $resgates = $modelResgate->getResgatesUsuario($usuario_id);
        $this->view->resgates = $resgates;
        
    }
    
    public function premiosAction() {
        
        /**
         * Premios
         */
        $usuario_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
        $modelAposta = new Model_DbTable_Aposta();
        $apostas = $modelAposta->getApostas(null, $usuario_id, 1);
        $this->view->apostas = $apostas;
        
        // somando a premiacao
        $total_premiacao = 0;
        foreach ($apostas as $aposta) {
            $total_premiacao += $aposta->aposta_premio;
        }
        $this->view->total_premiacao = $total_premiacao;
        
    }
    
}
