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
class Admin_ClassificacaoController extends Zend_Controller_Action {
    
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
    
    public function corrigirAction() {
        $classificacao_id = $this->getRequest()->getParam('id');
        
        //busca dados da classificacao
        $modelClassificacao = new Model_DbTable_Classificacao();
        $classificacao = $modelClassificacao->getById($classificacao_id);
        
        if (!$classificacao) {
            
        }
        
        $this->view->classificacao = $classificacao;

        // form
        $formClassificacao = new Form_Admin_Classificacao();
        $formClassificacao->time_id->setValue($classificacao->time_id);
        $this->view->form = $formClassificacao;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($formClassificacao->isValid($data)) {
                
                $data = $formClassificacao->getValues();
                
                try {
                    $modelClassificacaoComplemento = new Model_DbTable_ClassificacaoComplemento();
                    $modelClassificacaoComplemento->insert($data);
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'success' => 'Classificação atualizada!'
                    ));
                    $this->_redirect("admin/classificacao");
                } catch (Exception $ex) {
                    $this->_helper->flashMessenger->addMessage(array(
                        'danger' => $ex->getMessage()
                    ));
                    $this->_redirect("admin/classificacao");
                }
                
            }
        }
    }

    public function atualizarAction() {
     
        $this->_helper->viewRenderer->setNoRender();
        
        // busca todos os times
        $modelTime = new Model_DbTable_Time();
        $divisoes = array(1,2);
        $times = $modelTime->getTimes($divisoes);
        
        try {
            Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
        
            foreach ($times as $time) {
                $pluginClassificacao = new Plugin_Classificacao($time->time_id);

                try {
                    Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                    $pluginClassificacao->atualizar();
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                } catch (Exception $ex) {
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                    die($ex->getMessage());
                }                        
            }
            Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
            
            $this->_helper->flashMessenger->addMessage(array(
                'success' => 'Classificação atualizada com sucesso'
            ));
            
            $this->_redirect("admin/classificacao");
            
        } catch (Exception $ex) {
            Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
            
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => $ex->getMessage()
            ));
            
            $this->_redirect("admin/classificacao/atualizar");
        }
        
        
    }
    
}
