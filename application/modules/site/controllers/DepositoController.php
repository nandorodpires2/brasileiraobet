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
        $formDeposito->submit->setLabel("CONTINUAR");        
        $this->view->formDeposito = $formDeposito;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($formDeposito->isValid($data)) {
                
                $data = $formDeposito->getValues();                
                $data['usuario_id'] = Zend_Auth::getInstance()->getIdentity()->usuario_id;
                
                try {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                    
                    /**
                     * Data vencimento boleto
                     */
                    $zendDate = new Zend_Date();
                    $zendDate->addDay(Zend_Registry::get("config")->boleto->vencimento->dias);
                    $data['deposito_vencimento'] = $zendDate->get("YYYY-MM-dd");
                    
                    /**
                     * Valor do bonus
                     */
                    $modelDepositoValor = new Model_DbTable_DepositoValor();
                    $depositoValor = $modelDepositoValor->getByField("deposito_valor", $data['deposito_valor']);
                    $data['deposito_valor_bonus'] = $depositoValor->deposito_valor_bonus;
                                        
                    $modelDeposito = new Model_DbTable_Deposito();
                    $deposito_id = $modelDeposito->insert($data);
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'success' => 'Depósito realizado com sucesso!'
                    ));
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                    
                    // grava na sessao
                    $session = Zend_Registry::get("session");
                    $session->deposito_id = $deposito_id;
                    
                    $this->_redirect("/pagamento");
                    
                } catch (Exception $ex) {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                                        
                    $this->_redirect("/deposito");
                }
                
            }
        }            
        
    }
        
}
