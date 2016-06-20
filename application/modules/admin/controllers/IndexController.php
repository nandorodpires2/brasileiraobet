<?php

class Admin_IndexController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {
                
        /**
         * Depositos
         */
        $modelDeposito = new Model_DbTable_Deposito();
        $depositos = $modelDeposito->getMontante();
        
        /**
         * Resgates
         */
        $resgates = 0;
        
        $modelAposta = new Model_DbTable_Aposta();        
        
        /**
         * Apostas
         */
        $apostas = $modelAposta->getTotalMontanteApostas();
                
        /**
         * Premios
         */
        $modelPartida = new Model_DbTable_Partida();
        $premios = $modelPartida->getTotalPremio();
        
        /**
         * Premio inicial
         */
        $inicial = $modelPartida->getTotalPremioInicial();

        /**
         * Banca
         */                
        $banca = ($apostas + $inicial) * (0.3);
        
        /**
         * Saldo (Apostas X Premiacoes)
         */
        $saldo_apostas_premiacoes = $apostas - $premios;
        $saldo_depositos_resgates = $depositos - $resgates;
        
        $this->view->depositos = $depositos;
        $this->view->resgates = $resgates;
        
        $this->view->apostas = $apostas;
        $this->view->inicial = $inicial;
        $this->view->banca = $banca;
        $this->view->premios = $premios;
        
        $this->view->saldo_apostas_premiacoes = $saldo_apostas_premiacoes;
        $this->view->saldo_depositos_resgates = $saldo_depositos_resgates;
        
    }

}

