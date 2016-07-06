<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of InciqueController
 *
 * @author Fernando
 */
class Site_IndiqueController extends Zend_Controller_Action {
    
    public function init() {
        if (!Plugin_Auth::check()) {
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => 'Você precisa estar logado para acessar esta página!'
            ));
            $this->_redirect("/");
        }
        
        /**
         * Verifica se o usuario ja atingiu o limite de bonus 
         * O limite de bonus é a soma:
         * -> bonus de cadastro
         * -> bonus de indicacao
         */
        $modelLancamento = new Model_DbTable_Lancamento();
        $total_bonus = $modelLancamento->getTotalBonus(Zend_Auth::getInstance()->getIdentity()->usuario_id);

        if ($total_bonus >= Zend_Registry::get("config")->bonus->limite) {
            $this->_helper->flashMessenger->addMessage(array(
                'warning' => 'Você já atingiu seu limite de bônus!'
            ));
            $this->_redirect("/");
        }
        
    }
    
    public function indexAction() {
        
        /**
         * Form
         */
        $form = new Form_Site_Indique();
        $form->submit->setLabel("INDICAR AMIGO");
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                
                $data = $form->getValues();
                
                try {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                    
                    /**
                     * Verifica se o indica ja esta cadastrado
                     */
                    $modelUsuario = new Model_DbTable_Usuario();
                    $usuario = $modelUsuario->getByField("usuario_email", $data['indique_email']);
                    
                    if ($usuario) {
                        $this->_helper->flashMessenger->addMessage(array(
                            'warning' => 'Desculpe, mas o email informado já está cadastrado como um usuário. Por favor indique outro email.'
                        ));
                        $this->_redirect("indique/");
                    }
                    
                    /**
                     * Verifica se ja foi feito alguma indicacao para o email
                     */
                    $modelIndique = new Model_DbTable_Indique();
                    $indicado = $modelIndique->getByField("indique_email", $data['indique_email']);
                    if ($indicado) {
                        $this->_helper->flashMessenger->addMessage(array(
                            'warning' => 'Desculpe, mas já foi realizada uma indicação para o email informado. Por favor indique outro amigo.'
                        ));
                        $this->_redirect("indique/");
                    }
                    
                    // cadastra o amigo                    
                    $modelIndique->insert($data);
                    
                    // envia email para o amigo
                    $subject = Zend_Auth::getInstance()->getIdentity()->usuario_nome . ' indicou você';
                    $pluginMail = new Plugin_Mail();
                    $pluginMail->setDataMail('indique_nome', $data['indique_nome']);
                    $pluginMail->setDataMail('usuario_nome', Zend_Auth::getInstance()->getIdentity()->usuario_nome);                    
                    $pluginMail->send("usuario-indique.phtml", $subject, $data['indique_email']);
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'success' => 'Amigo indicado com sucesso'
                    ));
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                    
                } catch (Exception $ex) {
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                    $this->_helper->flashMessenger->addMessage(array(
                        'danger' => 'Não foi possível indicar o amigo. Por favor tente mais tarde'
                    ));
                }
                
                $this->_redirect("indique/");
                
            }
        }        
        
    }
    
}
