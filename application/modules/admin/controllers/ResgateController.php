<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResgateController
 *
 * @author Fernando
 */
class Admin_ResgateController extends Zend_Controller_Action {
    
    public function init() {
        
    }
    
    public function indexAction() {
        
        $items = 10;        
        $page = $this->getRequest()->getParam('page',1);
        
        $modelResgate = new Model_DbTable_Resgate();
        
        /**
         * Busca os resgates pendentes
         */
        $pendentes = $modelResgate->getQuery("resgate_processado = 0", "resgate_data_limite asc");
        
        $paginatorPendentes = Zend_Paginator::factory($pendentes);
        $paginatorPendentes->setItemCountPerPage($items);
        $paginatorPendentes->setCurrentPageNumber($page);
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('partial/pagination.phtml');
        
        $this->view->pendentes = $paginatorPendentes;
        $this->view->assign('paginatorPendentes', $paginatorPendentes);
        
        /**
         * Busca os resgates realizados
         */
        $realizados = $modelResgate->getQuery("resgate_processado = 1");
        $paginatorRealizados = Zend_Paginator::factory($realizados);
        $paginatorRealizados->setItemCountPerPage($items);
        $paginatorRealizados->setCurrentPageNumber($page);
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('partial/pagination.phtml');
        
        $this->view->realizados = $paginatorRealizados;
        $this->view->assign('paginatorRealizados', $paginatorRealizados);
        
    }
    
    public function processadoAction() {
        
        $this->_helper->viewRenderer->setNoRender();
        
        $resgate_id = $this->getRequest()->getParam("id");
        
        $modelResgate = new Model_DbTable_Resgate();
        $resgate = $modelResgate->getById($resgate_id);
        
        try {
            
            Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
            
            /**
             * Setar como processdo
             */
            $modelResgate->updateById(array(
                'resgate_processado' => 1,
                'resgate_processado_data' => date("Y-m-d H:i:s")
            ), $resgate_id);
            
            /**
             * Notificar usuario
             */
            $modelNotificacao = new Model_DbTable_Notificacao();
            $valor = App_Helper_Currency::toCurrency($resgate->resgate_valor);
            $conteudo = " 
                Sua solicitação de resgate no valor de {$valor} foi processada.
            ";
            $notificacao = array(
                'usuario_id' => $resgate->usuario_id,
                'notificacao_conteudo' => $conteudo
            );
            $modelNotificacao->insert($notificacao);
            
            /**
             * Enfileirar email
             */
            $pluginMail = new Plugin_Mail();
            $paramns = serialize(array(
                'resgate' => $resgate
            ));
            $pluginMail->inQueue('usuario-resgate-processado', 'Resgate processado', $paramns, $resgate->usuario_email);
            
            Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
            
            $this->_helper->flashMessenger->addMessage(array(
                'success' => 'Resgate processado com sucesso!'
            ));
            $this->_redirect("resgate/");
            
        } catch (Exception $ex) {
            Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
            
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => 'Falha ao processar resgate - ' . $ex->getMessage()
            ));
            $this->_redirect("resgate/");
            
        }
        
    }
    
}
