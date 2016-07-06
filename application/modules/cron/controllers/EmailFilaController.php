<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EmailFilaController
 *
 * @author Fernando
 */
class Cron_EmailFilaController extends Zend_Controller_Action {
    
    public function init() {
        if (!$this->checkAccess()) {
            die('sem permissao');
        }
    }
    
    public function indexAction() {
        $this->disabled();
        
        $error = false;
        
        $where = Zend_Db_Table_Abstract::getDefaultAdapter()->quoteInto("email_enviado = ?", 0);
        $order = "email_data asc";
        $count = 10;
        
        // buscar os emails da fila
        $modelEmail = new Model_DbTable_Email();
        $emails = $modelEmail->fetchAll($where, $order, $count);
                        
        if ($emails->count() == 0) {
            die('Nenhum email a ser enviado');
        }
        
        // enviar cada email pendente
        foreach ($emails as $email) {
        
            $pluginMail = new Plugin_Mail();
            
            // setando os paramentros (se houver)
            if ($email->email_parametros) {
                $parametros = Zend_Serializer::unserialize($email->email_parametros);
                
                foreach ($parametros as $key => $value) {
                    $pluginMail->setDataMail($key, $value);
                }
                
            }
            
            try {
                
                $pluginMail->send($email->email_layout, $email->email_titulo, $email->email_destinatario);
                
                // seta o email como enviado
                $update = array(
                    'email_enviado' => 1,
                    'email_data_enviado' => date('Y-m-d H:i:s')
                );
                
                $modelEmail->updateById($update, $email->email_id);
                
            } catch (Exception $ex) {
                $error = true;
            }
            
        }
        
        if (!$error) {
            echo "Procedimento realizado com sucesso";
        } else {
            echo "Houve erros durante o processo!";
        }       
        
    }
    
    /**
     * 
     */
    private function disabled() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }
    
    private function checkAccess() {
        return true;
    }
    
}
