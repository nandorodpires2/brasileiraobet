<?php

class Site_IndexController extends Zend_Controller_Action {

    public function init() {
        //Zend_Debug::dump(Zend_Auth::getInstance()->getIdentity()); die();
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
         * Form login
         */
        $formLogin = new Form_Site_Login();
        $formLogin->submit->setLabel("LOGAR");
        $this->view->formLogin = $formLogin;
        
        if (Zend_Auth::getInstance()->hasIdentity()) {
            
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
            
        }
        
        
    }

}

