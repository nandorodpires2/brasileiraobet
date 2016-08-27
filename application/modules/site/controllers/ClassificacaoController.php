<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ClassificacaoController
 *
 * @author Fernando
 */
class Site_ClassificacaoController extends Zend_Controller_Action {
    
    public function init() {
        
    }
    
    public function indexAction() {
        
        /**
         * Busca a classificacao
         */
        $modelClassificacao = new Model_DbTable_Classificacao();
        
        // series
        $series = array(
            'Série A' => $modelClassificacao->getClassificacao(1),
            //'Série B' => $modelClassificacao->getClassificacao(2),
        );
        $this->view->series = $series;
        
    }
        
}
