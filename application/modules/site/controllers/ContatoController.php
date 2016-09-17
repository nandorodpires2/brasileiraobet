<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ContatoController
 *
 * @author Fernando
 */
class Site_ContatoController extends Zend_Controller_Action {
    
    public function init() {
        
    }
    
    public function indexAction() {
        
        /**
         * Form
         */
        $formContato = new Form_Site_Contato();
        $this->view->formContato = $formContato;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($formContato->isValid($data)) {
                $data = $formContato->getValues();
                
                try {
                    $modelContato = new Model_DbTable_Contato();
                    $modelContato->insert($data);
                    
                    /**
                     * Envia email administrador
                     */
                    $pluginMailAdmin = new Plugin_Mail();                    
                    $pluginMailAdmin->inQueue('contato-admin.phtml', 'Novo Contato', serialize(array('dados' => $data)), Zend_Registry::get('config')->mail->admin);
                    
                    /**
                     * Envia email cliente
                     */
                    $pluginMailCliente = new Plugin_Mail();                    
                    $pluginMailCliente->inQueue('contato-cliente.phtml', 'Contato Recebido', serialize(array('dados' => $data)), $data['contato_email']);
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'success' => 'Seu contato foi enviado com sucesso. Em breve entraremos em contato.'
                    ));
                    
                    $this->_redirect("/");
                    
                } catch (Exception $ex) {

                    $this->_helper->flashMessenger->addMessage(array(
                        'danger' => $ex->getMessage()
                    ));
                    
                    $this->_redirect("/contato");
                    
                }
                
            }
        }
        
    }
    
}
