<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mail
 *
 * @author Fernando Rodrigues
 */
class Plugin_Mail {
    
    protected $_sendMailOn = true;

    protected $_zendMail;
    protected $_view;
    
    protected $_viewEmailPath;
    protected $_layoutMail;
    
    public function __construct() {
        
        $this->_zendMail = new Zend_Mail('utf-8');
        $this->_view = new Zend_View();
        
        // seta o caminho do layout do email
        $this->setViewEmailPath();
        
    }
    
    /**
     * 
     * @param type $layout
     * @param type $subject
     * @param type $html
     * @param type $addTo
     * @param type $from
     * @param type $reply
     */
    public function send($layout, $subject, $addTo, $from = null, $reply = null) {
        
        if (!$this->_sendMailOn) {
            return;
        }
        //Zend_Debug::dump(Zend_Registry::get('config')->mail->sender_name); die();
        
        // set layout
        $html = $this->_view->render($layout);
        $this->_zendMail->setBodyHtml($html);
        $this->_zendMail->setSubject($subject);
        $this->_zendMail->addTo($addTo);
        
        if ($from) {
            $this->_zendMail->setFrom($from);
        } else {
            $this->_zendMail->setFrom(Zend_Registry::get('config')->mail->sender_email, Zend_Registry::get('config')->mail->sender_name);
        }
        
        if ($reply) {
            $this->_zendMail->setReplyTo($reply);
        }
        
        $transport = Zend_Registry::get('mail_transport');
        
        try {
            $this->_zendMail->send($transport);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
        
    }
    
    public function setViewEmailPath($path = null) {
        if ($path) {            
            $this->_viewEmailPath = $path;
        } else {            
            $this->_viewEmailPath = Zend_Registry::get('config')->mail->path;
        }
        
        $this->_view->setScriptPath($this->_viewEmailPath);
    }
    
    public function getViewEmailPath() {
        return $this->_viewEmailPath;
    }
    
    public function setLayoutMail($layout) {
        $this->_layoutMail = $layout;
    }
    
    public function getLayoutMail() {
        return $this->_layoutMail;
    }
    
    public function setDataMail($name, $value) {
        $this->_view->$name = $value;
    }
        
}
