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
        // premios (R$)
        $premio1 = $modelAposta->getTotalPremio(1);
        $premio2 = $modelAposta->getTotalPremio(2);
        $premio3 = $modelAposta->getTotalPremio(3);
        $premio_total = $modelAposta->getTotalPremio();
        
        $this->view->premio1 = $premio1->premio;
        $this->view->premio2 = $premio2->premio;
        $this->view->premio3 = $premio3->premio; 
        $this->view->premio_total = $premio_total->premio;
        
        // apostas (qtde)        
        $where1 = $modelAposta->getDefaultAdapter()->quoteInto('aposta_vencedora_premio = ?', 1);
        $apostasPremio1 = $modelAposta->fetchAll($where1);
        $where2 = $modelAposta->getDefaultAdapter()->quoteInto('aposta_vencedora_premio = ?', 2);
        $apostasPremio2 = $modelAposta->fetchAll($where2);
        $where3 = $modelAposta->getDefaultAdapter()->quoteInto('aposta_vencedora_premio = ?', 3);
        $apostasPremio3 = $modelAposta->fetchAll($where3);
        
        $this->view->total_apostas_premio1 = $apostasPremio1->count();
        $this->view->total_apostas_premio2 = $apostasPremio2->count();
        $this->view->total_apostas_premio3 = $apostasPremio3->count();        
        
        $this->view->total_apostas_premio = $this->view->total_apostas_premio1 + $this->view->total_apostas_premio2 + $this->view->total_apostas_premio3;
        
        // porcentagem        
        $total_apostas = $modelAposta->getCount();        
        $this->view->porc_premio1 = number_format(($apostasPremio1->count() * 100) / $total_apostas->count, 2);
        $this->view->porc_premio2 = number_format(($apostasPremio2->count() * 100) / $total_apostas->count, 2);
        $this->view->porc_premio3 = number_format(($apostasPremio3->count() * 100) / $total_apostas->count, 2);
        
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

