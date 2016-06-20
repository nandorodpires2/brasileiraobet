<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DepositoController
 *
 * @author Fernando
 */
class Site_DepositoController extends Zend_Controller_Action {
    
    public function init() {
        if (!Plugin_Auth::check()) {
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => 'Você precisa estar logado para acessar esta página!'
            ));
            $this->_redirect("/");
        }
    }
    
    public function indexAction() {
        
        /**
         * Form de deposito
         */
        $formDeposito = new Form_Site_Deposito();
        $formDeposito->submit->setLabel("DEPOSITAR");
        
        /**
         * Busca cartao cadastrado
         */
        $modelUsuarioCartao = new Model_DbTable_UsuarioCartao();
        $where = $modelUsuarioCartao->getDefaultAdapter()->quoteInto("usuario_id = ?", Zend_Auth::getInstance()->getIdentity()->usuario_id);
        $order = "usuario_cartao_id desc";
        $cartao = $modelUsuarioCartao->fetchRow($where, $order);
        
        // popula os dados do cartao
        if ($cartao) {
            $formDeposito->populate($cartao->toArray());
        }
        
        $this->view->formDeposito = $formDeposito;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($formDeposito->isValid($data)) {
                
                $data = $formDeposito->getValues();                
                $deposito_valor = $formDeposito->getValue("deposito_valor");
                unset($data['deposito_valor']);
                
                try {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                    
                    $this->salvarCartao($data);
                    
                    $modelDeposito = new Model_DbTable_Deposito();
                    $modelDeposito->insert(array(
                        'deposito_valor' => $deposito_valor,
                        'usuario_id' => Zend_Auth::getInstance()->getIdentity()->usuario_id
                    ));
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'success' => 'Depósito realizado com sucesso!'
                    ));
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                    
                    $this->_redirect("/");
                    
                } catch (Exception $ex) {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'danger' => 'Houve um erro ao fazer o depósito'
                    ));
                    
                    $this->_redirect("/deposito");
                }
                
            }
        }            
        
    }
    
    public function resultadoAction() {
        
        $deposito_id = $this->getRequest()->getParam("id");
        
        /**
         * Dados do deposito
         */
        $modelDeposito = new Model_DbTable_Deposito();
        $deposito = $modelDeposito->getById($deposito_id);
        
        try {
            
            Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
            
            $descricao = "DEPÓSITO COD: {$deposito->deposito_id}";
            $modelLancamento = new Model_DbTable_Lancamento();
            $modelLancamento->insert(array(
                'lancamento_valor' => $deposito->deposito_valor,
                'lancamento_descricao' => $descricao,
                'usuario_id' => $deposito->usuario_id
            ));            
            
            $modelDeposito->updateById(array(
                'deposito_confirmado' => 1
            ), $deposito_id);
            
            Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
            
            $this->_redirect("/");
            
        } catch (Exception $ex) {

            Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => 'Falha ao processar o deposito'
            ));
            
            $this->_redirect("/");
            
        }
        
    }
    
    protected function salvarCartao($data) {
        
        $data['usuario_id'] = Zend_Auth::getInstance()->getIdentity()->usuario_id;
        
        $modelUsuarioCartao = new Model_DbTable_UsuarioCartao();
        try {
            $modelUsuarioCartao->insert($data);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
         
    }
    
}
