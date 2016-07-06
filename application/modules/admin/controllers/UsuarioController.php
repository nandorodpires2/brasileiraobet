<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsuarioController
 *
 * @author Fernando
 */
class Admin_UsuarioController extends Zend_Controller_Action {
    
    public function init() {
        
    }
    
    public function indexAction() {
        
        $items = 10;        
        $page = $this->getRequest()->getParam('page',1);
        
        /**
         * Busca os usuarios
         */
        $modelUsuario = new Model_DbTable_Usuario();
        $usuarios = $modelUsuario->getQuery();        
        
        $paginator = Zend_Paginator::factory($usuarios);
        $paginator->setItemCountPerPage($items);
        $paginator->setCurrentPageNumber($page);
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('partial/pagination.phtml');
        
        $this->view->usuarios = $paginator;
        $this->view->assign('paginator', $paginator);
        
    }

    public function addCreditAction() {
        
        /**
         * Busca dados do usuario
         */
        $usuario_id = $this->getRequest()->getParam('id');
        $modelUsuario = new Model_DbTable_Usuario();
        $usuario = $modelUsuario->getById($usuario_id);
        $this->view->usuario = $usuario;
        
        /**
         * Form
         */
        $form = new Form_Admin_UsuarioCredito();
        $form->addElement('hidden', 'usuario_id', array('value' => $usuario_id));
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                
                $data = $form->getValues();
                $data['lancamento_bonus'] = 1;
                
                try {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                    
                    // seta o credito
                    $modelLancamento = new Model_DbTable_Lancamento();
                    $modelLancamento->insert($data);
                    
                    // notifica o usuario
                    $modelNotificacao = new Model_DbTable_Notificacao();
                    $conteudo = " 
                        Foi lançado um crédito em sua conta.
                    ";                            
                    $notificacao = array(
                        'usuario_id' => $usuario->usuario_id,
                        'notificacao_conteudo' => $conteudo
                    );
                    $modelNotificacao->insert($notificacao);                    
                    
                    // grava fila de email
                    $pluginMail = new Plugin_Mail();
                    $paramns = Zend_Serializer::serialize(array(
                        'usuario' => $usuario,
                        'lancamento_valor' => $data['lancamento_valor'],
                        'lancamento_descricao' => $data['lancamento_descricao']
                    ));
                    $pluginMail->inQueue('usuario-credito.phtml', "Crédito em sua conta", $paramns, $usuario->usuario_email);
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'success' => 'Crédito lançado com sucesso'
                    ));
                    
                    $this->_redirect("admin/usuario");
                    
                } catch (Exception $ex) {
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'danger' => $ex->getMessage()
                    ));
                    
                    $this->_redirect("admin/usuario/add-credit/id/{$usuario_id}");
                    
                }
                
            }
        }
        
    }

    public function maquinaAction() {
        
        $this->_helper->viewRenderer->setNoRender();
        
        $qtde_usuarios = $this->getRequest()->getParam("qtde", 50);
        
        Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
        
        for ($i = 0; $i < $qtde_usuarios; $i++) {
            
            $uniqid = uniqid();
            $data = array(
                'usuario_nome' => 'USUÁRIO VIRTUAL ' . $uniqid,
                'usuario_email' => "usuario{$uniqid}@virtual.com",
                'usuario_maquina' => 1
            );
            $modelUsuario = new Model_DbTable_Usuario();
            
            try {
                
                $usuario_id = $modelUsuario->insert($data);
                
                /**
                 * Bonus
                 */
                $bonus = array(
                    'lancamento_descricao' => 'CRÉDITO CADASTRO',
                    'usuario_id' => $usuario_id,
                    'lancamento_valor' => Zend_Registry::get("config")->bonus->cadastro
                );
                $modelLancamento = new Model_DbTable_Lancamento();
                $modelLancamento->insert($bonus);
                
            } catch (Exception $ex) {
                Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                
                $this->_helper->flashMessenger->addMessage(array(
                    'danger' => $ex->getMessage()
                ));

                $this->_redirect("admin/usuario");
                
            }
            
        }
        
        Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
        
        $this->_helper->flashMessenger->addMessage(array(
            'success' => "{$qtde_usuarios} usuários cadastrados com sucesso"
        ));
            
        $this->_redirect("admin/usuario");
        
    }
    
}
