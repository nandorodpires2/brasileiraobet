<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EmprestimoController
 *
 * @author Fernando
 */
class Site_EmprestimoController extends Zend_Controller_Action {
    
    public function init() {
        
        if (!Plugin_Auth::check()) {
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => 'Você precisa estar logado para acessar esta página!'
            ));
            $this->_redirect("/");
        }
        
        $this->_helper->flashMessenger->addMessage(array(
            'danger' => 'Página não encontrada!'
        ));
        $this->_redirect("/");
        
        /**
         * Saldo Usuario
         */                        
        $modelLancamento = new Model_DbTable_Lancamento();
        //saldo para apostas
        $saldo = $modelLancamento->getSaldoUsuario(Zend_Auth::getInstance()->getIdentity()->usuario_id);
        
        if ($saldo > Zend_Registry::get('config')->emprestimo->saldo->minimo) {
            $this->_helper->flashMessenger->addMessage(array(
                'warning' => 'Você ainda não necessita solicitar empréstimo!'
            ));
            $this->_redirect("/");
        }
        
    }
    
    public function indexAction() {
        
        $usuario_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
        
        /**
         * Busca saldo emprestimos pendentes
         */
        $modelEmprestimo = new Model_DbTable_Emprestimo();        
        $emprestimo = $modelEmprestimo->getPendenteByUsuarioId($usuario_id);    
        
        $disponivel = Zend_Registry::get('config')->emprestimo->limite - $emprestimo->emprestimo_pendente;
        $this->view->disponivel = $disponivel;
        
        /**
         * calcula os valores disponiveis
         */        
        $valores = array();
        $zendCurrency = new Zend_Currency();
        for ($i = 1; $i <= (int)$disponivel; $i++) {
            if ($i % 2 == 0) {                
                $valores[$i] = ' ' . $zendCurrency->toCurrency($i);
            }
        }
        
        /**
         * Form 
         */     
        $form = new Form_Site_Emprestimo();
        
        // setando os valores disponiveis
        $form->emprestimo_valor->setMultiOptions($valores);
        // usuario_id
        $form->usuario_id->setValue($usuario_id);    
        // taxa corrente
        $form->emprestimo_taxa->setValue(Zend_Registry::get('config')->emprestimo->taxa);    
        
        $form->submit->setLabel("SOLICITAR EMPRÉSTIMO");
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $data = $form->getValues();
                
                try {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                    
                    $emprestimo_id = $modelEmprestimo->insert($data);
                    
                    // lanca o emprestimo na conta                    
                    $lancamento = array(
                        'usuario_id' => $usuario_id,
                        'lancamento_descricao' => 'EMPRÉSTIMO ' . $zendCurrency->toCurrency($data['emprestimo_valor']) . " [#{$emprestimo_id}] ",
                        'lancamento_valor' => $data['emprestimo_valor'],
                        'lancamento_bonus' => 1
                    );
                    $modelLancamento = new Model_DbTable_Lancamento();
                    $modelLancamento->insert($lancamento);
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'success' => 'Empréstimo solicitado com sucesso'
                    ));
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                    
                    $this->_redirect("/");
                    
                } catch (Exception $ex) {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'danger' => 'Falha ao solicitar empréstimo - ' . $ex->getMessage()
                    ));
                    $this->_redirect("/emprestimo");
                }                
                
            }
        }
        
    }
    
}
