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
        $premio1 = 0;
        $premio2 = 0;
        $premio3 = 0;
        $premio_total = 0;
        
        foreach ($apostas as $aposta) {
            
            // montante das apostas
            $montante_apostas += $aposta->partida_valor;
            
            // calculando as premiacoes
            if ($aposta->aposta_vencedora) {
                if ($aposta->aposta_vencedora_premio == 1) {
                    $premio1 += $aposta->partida_valor;
                }
                if ($aposta->aposta_vencedora_premio == 2) {
                    $premio2 += $aposta->partida_valor;
                }
                if ($aposta->aposta_vencedora_premio == 3) {
                    $premio3 += $aposta->partida_valor;
                }
                
                $premio_total += $aposta->partida_valor;
                
            }
            
        }
        $this->view->montante_apostas = $montante_apostas;
        
        /**
         * Premiacoes
         */
        $this->view->premio1 = $premio1;
        $this->view->premio2 = $premio2;
        $this->view->premio3 = $premio3; 
        $this->view->premio_total = $premio_total;
        
        /**
         * Depositos
         */
        $modelDeposito = new Model_DbTable_Deposito();
        $depositos = $modelDeposito->getMontante();
        $this->view->depositos = $depositos;
        
        /**
         * Resgates
         */
        $modelResgate = new Model_DbTable_Resgate();
        $resgates = $modelResgate->getMontante();
        $this->view->resgates = $resgates;        
        
        $this->view->saldo_financeiro = $depositos - $resgates;
        
        /**
         * Maiores vencedores
         */
        $apostasVencedoras = $modelAposta->getMaioresVencedores();
        $this->view->apostasVencedoras = $apostasVencedoras;
        
    }

}

