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

    protected $pluginLogFile;

    public function init() {     
        
        if (!Plugin_Cron::checkAccess()) {
            die('sem permissao');
        }
        
        // file log
        $this->pluginLogFile = new Plugin_LogFile("logEmailFila.txt");
        $data = date('d/m/Y H:i:s');
        $this->pluginLogFile->write("[{$data}] Tarefa iniciada; \r\n");        
        
    }

    public function indexAction() {
        
        $log = "";
        
        $this->disabled();

        $where = Zend_Db_Table_Abstract::getDefaultAdapter()->quoteInto("email_enviado = ?", 0);
        $order = "email_data asc";
        $count = 20;

        // buscar os emails da fila
        $modelEmail = new Model_DbTable_Email();
        $emails = $modelEmail->fetchAll($where, $order, $count);

        if ($emails->count() == 0) {
            $log .= "[".date('d/m/Y H:i:s')."] Nenhum email a enviar. \r\n";
        }
        
        // enviar cada email pendente        
        foreach ($emails as $email) {

            $pluginMail = new Plugin_Mail();

            // setando os paramentros (se houver)
            if ($email->email_parametros) {
                $parametros = unserialize($email->email_parametros);

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
                
                $log .= "[".date('d/m/Y H:i:s')."] email {$email->email_id} - {$email->email_destinatario} enviado; \r\n";
                
            } catch (Exception $ex) {
                $update = array(
                    'email_enviado' => 2,
                    'email_error_log' => $ex->getTraceAsString()
                );

                $modelEmail->updateById($update, $email->email_id);
                $log .= "[".date('d/m/Y H:i:s')."] Erro email {$email->email_id} - {$email->email_destinatario} | " . $ex->getMessage() . "; \r\n";
            }
        }

        $log .= "[".date('d/m/Y H:i:s')."] Tarefa finalizada. \r\n";
        $this->pluginLogFile->write($log);    
        
    }

    /**
     * 
     */
    private function disabled() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

}
