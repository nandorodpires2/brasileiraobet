<?php

class Site_IndexController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {
        
        
        /**
         * Partidas (nao realizadas)
         */
        $modelPartida = new Model_DbTable_Partida();
        $partidas = $modelPartida->getPartidas(0);
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
            $saldo = $modelLancamento->getSaldoUsuario($usuario_id);
            $this->view->saldo = $saldo;
            
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

