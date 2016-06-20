<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApostaController
 *
 * @author Fernando
 */
class Site_ApostaController extends Zend_Controller_Action {
    
    public function init() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => 'Você precisa estar logado para acessar esta página!'
            ));
            $this->_redirect("/");
        }
    }
    
    public function indexAction() {
        
    }
    
    public function apostarAction() {
        
        $this->_helper->viewRenderer->setNoRender();
        
        /**
         * Form apostar
         */
        $formApostar = new Form_Site_Apostar(); 
                
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($formApostar->isValid($data)) {
                
                $data = $formApostar->getValues();
                
                try {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                    
                    /**
                     * desconta o valor da aposta do saldo
                     */        
                    $modelLancamento = new Model_DbTable_Lancamento();
                    $saldo = $modelLancamento->getSaldoUsuario($data['usuario_id']);
                    
                    /**
                     * Dados da Partida
                     */
                    $modelPartida = new Model_DbTable_Partida();
                    $partida = $modelPartida->getById($data['partida_id']);
                    
                    $this->lancarDebito($partida);
                    
                    /**
                     * cadastra a aposta
                     */
                    $modelAposta = new Model_DbTable_Aposta();
                    $modelAposta->insert($data);                    
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'success' => 'Aposta realizada com sucesso!'
                    ));
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                    
                } catch (Exception $ex) {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                    die($ex->getMessage());
                }
                
            }
            
            $this->_redirect("/");
            
        }
        
    }
    
    public function alterAction() {
        
    }
    
    public function deleteAction() {
        $this->_helper->viewRenderer->setNoRender();
        
        $aposta_id = $this->getRequest()->getParam("id");
        
        /**
         * Dados da aposta
         */
        $modelAposta = new Model_DbTable_Aposta();
        $aposta = $modelAposta->getById($aposta_id);
        
        if (!$aposta && $aposta->usuario_id !== Zend_Auth::getInstance()->getIdentity()->usuario_id) {
            
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => 'Aposta não encontrada!'
            ));
            
            $this->_redirect("/");
        }
        
        try {
            
            // apaga a aposta
            $modelAposta->delete("aposta_id = {$aposta_id}");
            
            // retira o debito 
            $modelLancamento = new Model_DbTable_Lancamento();
            $modelLancamento->delete("partida_id = {$aposta->partida_id}");
            
            $this->_helper->flashMessenger->addMessage(array(
                'success' => 'Aposta cancelada com sucesso!'
            ));
            
            $this->_redirect("/");
            
        } catch (Exception $ex) {

        }
        
    }
    
    protected function lancarDebito($partida) {
        
        $data = array(
            'usuario_id' => Zend_Auth::getInstance()->getIdentity()->usuario_id,
            'lancamento_descricao' => 'DÉBITO APOSTA PARTIDA #' . $partida->partida_id,
            'partida_id' => $partida->partida_id,
            'lancamento_valor' => $partida->partida_valor * -1
        );
        $modelLancamento = new Model_DbTable_Lancamento();
        
        try {
            $modelLancamento->insert($data);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }        
        
    }
    
}
