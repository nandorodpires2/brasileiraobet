<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Message
 *
 * @author Fernando Rodrigues
 */
class Plugin_Message extends Zend_Controller_Plugin_Abstract {
    
    private $_html;

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        
        $flashmessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');  
        $messages = $flashmessenger->getMessages();
        
        if (!$messages) {
            return;
        }
        
        $this->_html = $this->getHeaderHtml();
        
        foreach ($messages as $message) {
            
            if (is_array($message)) {
                foreach ($message as $key => $value) {
                    $kind = $this->getMessageKind($key);
                    $this->_html .= " 
                        <div class='alert alert-{$key}'> 
                            <strong>{$kind}</strong>
                            {$value}
                        </div> 
                    ";
                }
            } else {
                $this->_html .= "
                    <div class='alert alert-success'> 
                        <strong>Sucesso!</strong>
                        {$message}
                    </div> 
                ";
            }
             
        }
        
        $this->_html .= $this->getFooterHtml();
        
        $view = Zend_Controller_Action_HelperBroker::getExistingHelper('viewRenderer')->view;        
        $view->messages = $this->_html;
        
    }
    
    private function getHeaderHtml() {
        return " 
            <div class='container'>
            <div class='row'>
            <div class='col-lg-12 col-sm-12' style='margin-top: 15px;'>
        ";
    }
    
    private function getFooterHtml() {
        return "</div></div></div>";
    }
    
    private function getMessageKind($key) {
        switch ($key) {            
            case 'success':
                return 'Sucesso!';
                break;            
            case 'warning':
                return 'Alerta!';
                break;            
            case 'info':
                return 'Informação!';
                break;            
            case 'danger':
                return 'Erro!';
                break;
            default:
                break;
        }
    }
    
}
