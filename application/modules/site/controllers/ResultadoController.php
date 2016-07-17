<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResultadoController
 *
 * @author nando_000
 */
class Site_ResultadoController extends Zend_Controller_Action {
    
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
         * Busca as partidas em andamento
         */
        $modelPartida = new Model_DbTable_Partida();
        $parciais = $modelPartida->getPartidasParcial();
        $this->view->parciais = $parciais;  
        
        /**
         * Busca as partidas
         */
        $where = array(
            'realizada' => 1
        );
        $partidas = $modelPartida->getPartidas($where, 'partida_data desc', 10);          
        $this->view->partidas = $partidas;
        
    }
    
}
