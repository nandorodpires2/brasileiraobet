<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResgateController
 *
 * @author Fernando
 */
class Site_ResgateController extends Zend_Controller_Action {
    
    public function init() {
        
        if (!Plugin_Auth::check()) {
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => 'Você precisa estar logado para acessar esta página!'
            ));
            $this->_redirect("/");
        }
        
        /**
         * Saldo
         */
        $usuario_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
        $modelLancamento = new Model_DbTable_Lancamento();
        $saldo = $modelLancamento->getSaldoUsuario($usuario_id, 0);
        
        if ($saldo < Zend_Registry::get("config")->resgate->minimo) {
            $this->_helper->flashMessenger->addMessage(array(
                'warning' => 'Saldo inferior ao mínimo necessário!'
            ));
            $this->_redirect("/");
        }
        $this->view->saldo = $saldo;        
        
    }
    
    public function indexAction() {
        
        /**
         * Form
         */
        $formResgate = new Form_Site_Resgate();
        $formResgate->submit->setLabel("RESGATAR");
        
        /**
         * verifica se ja possui conta
         */
        $modelUsuarioConta = new Model_DbTable_UsuarioConta();
        $conta = $modelUsuarioConta->getContaUsuario(Zend_Auth::getInstance()->getIdentity()->usuario_id);
        
        if ($conta) {
            $formResgate->populate($conta->toArray());
        }
        
        $this->view->formResgate = $formResgate;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($formResgate->isValid($data)) {
                
                $data = $formResgate->getValues();
                $data['usuario_id'] = Zend_Auth::getInstance()->getIdentity()->usuario_id;
                $data['resgate_data_limite'] = $this->calculaDataLimite();
                
                try {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                    
                    // insere a conta (caso nao tenha)
                    $data['usuario_conta_id'] = $this->salvaConta($data);
                    
                    $data['resgate_taxa'] = ($data['resgate_valor'] / 100) * Zend_Registry::get("config")->resgate->porcentagem;
                    $resgate_valor = $data['resgate_valor'] - $data['resgate_taxa'];                    
                    unset($data['usuario_conta_banco']);
                    unset($data['usuario_conta_agencia']);
                    unset($data['usuario_conta_numero']);
                    
                    $modelResgate = new Model_DbTable_Resgate();
                    $resgate_id = $modelResgate->insert($data);
                    
                    // lanca a movimentacao
                    $lancamento = array(
                        'usuario_id' => Zend_Auth::getInstance()->getIdentity()->usuario_id,
                        'lancamento_valor' => $data['resgate_valor'] * -1,
                        'lancamento_descricao' => 'RESGATE - COD: ' . $resgate_id
                    );
                    $modelLancamento = new Model_DbTable_Lancamento();
                    $modelLancamento->insert($lancamento);
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'success' => 'Resgate cadastrado com sucesso'
                    ));
                                        
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'info' => 'Em breve o valor será creditado na conta informada'
                    ));
                    
                    $this->_redirect("/");
                    
                } catch (Exception $ex) {
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'danger' => $ex->getMessage()
                    ));
                    
                    $this->_redirect("/resgate");
                }
                
            }
        }
        
    }
    
    protected function calculaDataLimite() {
        $zendDate = new Zend_Date();
        $zendDate->addDay(Zend_Registry::get("config")->resgate->dias);
        
        return $zendDate->get("YYYY-MM-dd");        
    }
    
    protected function salvaConta($data) {
        
        $usuario_conta_banco = $data['usuario_conta_banco'];
        $usuario_conta_agencia = $data['usuario_conta_agencia'];
        $usuario_conta_numero = $data['usuario_conta_numero'];
        
        $modelUsuarioConta = new Model_DbTable_UsuarioConta();
        $usuario_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
        $conta = $modelUsuarioConta->fetchRow("usuario_id = {$usuario_id} and usuario_conta_numero = '{$usuario_conta_numero}'");
        
        if (!$conta) {
            $insert = array(
                'usuario_conta_banco' => $usuario_conta_banco,
                'usuario_conta_agencia' => $usuario_conta_agencia,
                'usuario_conta_numero' => $usuario_conta_numero,
                'usuario_id' => $usuario_id
            );
            $usuario_conta_id = $modelUsuarioConta->insert($insert);
            return $usuario_conta_id;
        } else {
            return $conta->usuario_conta_id;
        }
        
    }
    
}
