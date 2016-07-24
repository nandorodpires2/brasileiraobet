<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RetornoController
 *
 * @author Fernando
 */
class Site_RetornoController extends Zend_Controller_Action {
    
    protected $data = false;

    public function init() {
        if ($this->getRequest()->isPost()) {
            $this->data = json_decode($this->getRequest()->getPost("data", false), true);
        }
    }
    
    public function indexAction() {
        
        $this->_helper->layout->disableLayout();
        //$this->_helper->viewRenderer->setNoRender();
            
        if (!$this->data) {
            return;
        }

        $modelDeposito = new Model_DbTable_Deposito();  
        $deposito = $modelDeposito->getByField("deposito_controle", $this->data['payment']['id']);

        if ($deposito) {            
        
            try {

                $update = array(
                    'deposito_status' => $this->data['event']
                );
                $where = "deposito_controle = '{$this->data['payment']['id']}'";
                $modelDeposito->update($update, $where);
                $this->checkPagamento();

            } catch (Exception $ex) {
                $pluginMail = new Plugin_Mail();
                $pluginMail->setDataMail('error', $ex->getTraceAsString());
                $pluginMail->send('error.phtml', 'Erro Retorno Asaas', 'nandorodpires@gmail.com');
            }        
        
        }        
        
    }
    
    private function checkPagamento() {
        
        switch ($this->data['event']) {
            case 'PAYMENT_RECEIVED':
                $this->processaDeposito();
                break;
            default:
                break;
        }
        
    }
    
    private function processaDeposito() {
        
        try {
            
            /**
             * Dados do deposito
             */
            $modelDeposito = new Model_DbTable_Deposito();
            $deposito = $modelDeposito->getByField("deposito_controle", $this->data['payment']['id']);

            $descricao = "DEPÓSITO COD: {$deposito->deposito_id}";
            $modelLancamento = new Model_DbTable_Lancamento();
            $modelLancamento->insert(array(
                'lancamento_valor' => $deposito->deposito_valor + $deposito->deposito_valor_bonus + $deposito->deposito_cupom_valor,
                'lancamento_descricao' => $descricao,
                'usuario_id' => $deposito->usuario_id
            ));            

            /**
             * Gera uma notificacao
             */
            $zendCurrency = new Zend_Currency();
            $valor_deposito = $zendCurrency->toCurrency($deposito->deposito_valor);
            $modelNotificacao = new Model_DbTable_Notificacao();
            $conteudo = " 
                Seu depósito no valor de {$valor_deposito} foi confirmado. 
            ";
            $notificacao = array(
                'usuario_id' => $deposito->usuario_id,
                'notificacao_conteudo' => $conteudo
            );
            $modelNotificacao->insert($notificacao);            
            
            //$this->checkPromocao($deposito->deposito_cupom);

            $modelDeposito->updateById(array(
                'deposito_confirmado' => 1
            ), $deposito->deposito_id);
            
        } catch (Exception $ex) {
            $pluginMail = new Plugin_Mail();
            $pluginMail->setDataMail('error', $ex->getTraceAsString());
            $pluginMail->send('error.phtml', 'Erro Processo depósito Asaas', 'nandorodpires@gmail.com');
        }
        
    }
    
}
