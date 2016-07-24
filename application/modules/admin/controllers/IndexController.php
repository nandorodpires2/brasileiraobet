<?php

class Admin_IndexController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {
        
        /**
         * Montante das apostas
         */
        $modelAposta = new Model_DbTable_Aposta();
        $apostas = $modelAposta->getApostas();
        $this->view->apostas = $apostas;
        
        $montante_apostas = 0;        
        foreach ($apostas as $aposta) {            
            // montante das apostas
            $montante_apostas += $aposta->partida_valor;
        }
        
        $this->view->montante_apostas = $montante_apostas;
        
        /**
         * Premiacoes
         */
        $premio1 = $modelAposta->getTotalPremio(1);
        $premio2 = $modelAposta->getTotalPremio(2);
        $premio3 = $modelAposta->getTotalPremio(3);
        $premio_total = $modelAposta->getTotalPremio();
        
        $this->view->premio1 = $premio1->premio;
        $this->view->premio2 = $premio2->premio;
        $this->view->premio3 = $premio3->premio; 
        $this->view->premio_total = $premio_total->premio;
        
        /**
         * Depositos
         */
        //realizado
        $modelDeposito = new Model_DbTable_Deposito();
        $depositos = $modelDeposito->getMontante();
        $this->view->depositos = $depositos;
        // previsto
        $depositos_previsto = $modelDeposito->getMontante(0);
        $this->view->depositos_previsto = $depositos_previsto + $depositos;
        
        /**
         * Resgates
         */
        // realizado
        $modelResgate = new Model_DbTable_Resgate();
        $resgates = $modelResgate->getMontante();
        $this->view->resgates = $resgates;        
        // previsto
        $resgates_previsto = $modelResgate->getMontante(0);
        $this->view->resgates_previsto = $resgates_previsto + $resgates;
        
        $this->view->saldo_financeiro = $depositos - $resgates;
        $this->view->saldo_financeiro_previsto = $this->view->depositos_previsto - $this->view->resgates_previsto;
        
        /**
         * Maiores vencedores
         */
        $apostasVencedoras = $modelAposta->getMaioresVencedores();
        $this->view->apostasVencedoras = $apostasVencedoras;
        
    }

}

