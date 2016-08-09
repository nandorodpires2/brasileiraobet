<?php

class Site_IndexController extends Zend_Controller_Action {

    public function init() {
        
    }
    
    public function mailAction() {
        $this->_helper->viewRenderer->setNoRender();
        $pluginMail = new Plugin_Mail();
        $pluginMail->send('padrao.phtml', 'Teste', 'nandorodpires@gmail.com');
    }

    public function indexAction() {
        
        /**
         * Partidas (nao realizadas)
         */
        $modelPartida = new Model_DbTable_Partida();
        $where = array(
            'vencida' => 0,
            'realizada' => 0,
            'processada' => 0
        );
        $partidas = $modelPartida->getPartidas($where);
        $this->view->partidas = $partidas;
        
        /**
         * Form apostar
         */
        $formApostar = new Form_Site_Apostar();
        $formApostar->submit->setLabel("APOSTAR");
        $formApostar->setElementDecorators(array('ViewHelper','Errors'));
        $this->view->formApostar = $formApostar;
        
        /**
         * Form editar
         */
        $formApostarAlterar = new Form_Site_Apostar();
        $formApostarAlterar->addElement("hidden", "aposta_id", array());
        $formApostarAlterar->submit->setLabel("ALTERAR APOSTA");
        $formApostarAlterar->setElementDecorators(array('ViewHelper','Errors'));
        $this->view->formApostarAlterar = $formApostarAlterar;
        
        /**
         * Form login
         */
        $formLogin = new Form_Site_Login();
        $formLogin->submit->setLabel("LOGAR");
        $this->view->formLogin = $formLogin;
        
        if (Plugin_Auth::check()) {
            
            $usuario_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
            
            /**
             * Apostas Usuario
             */
            $modelAposta = new Model_DbTable_Aposta();
            $apostas = $modelAposta->get($usuario_id);
            $this->view->apostas = $apostas;

            /**
             * Saldo Usuario
             */                        
            $modelLancamento = new Model_DbTable_Lancamento();
            //saldo para apostas
            $saldo = $modelLancamento->getSaldoUsuario($usuario_id);
            $this->view->saldo = $saldo;
            // saldo para resgate
            $saldo_resgate = $modelLancamento->getSaldoUsuario($usuario_id, 0);
            $this->view->saldo_resgate = $saldo_resgate;
            
            /**
             * Extrato
             */
            $lancamentos = $modelLancamento->getExtrato($usuario_id, 10, "lancamento_data desc");
            $this->view->lancamentos = $lancamentos;
            
            /**
             * Notificacoes
             */
            $modelNotificacao = new Model_DbTable_Notificacao();
            $notificacoes = $modelNotificacao->getNotificacoes($usuario_id, 0);
            $this->view->notificacoes = $notificacoes;
            
        }
        
        
    }

}

