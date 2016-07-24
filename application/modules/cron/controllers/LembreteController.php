<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LembreteController
 *
 * @author Fernando
 */
class Cron_LembreteController extends Zend_Controller_Action {
    
    public function init() {
     
        if (!Plugin_Cron::checkAccess()) {
            
            $server = $this->getRequest()->getServer('REMOTE_ADDR');
			
            $this->pluginLogFile = new Plugin_LogFile("logLembreteError.txt");
            $data = date('d/m/Y H:i:s');
            $this->pluginLogFile->write("[{$data}] Sem permissão de acesso [{$server}]; \r\n");  
            die();
            
        }
        
        // file log
        $this->pluginLogFile = new Plugin_LogFile("logLembrete.txt");
        $data = date('d/m/Y H:i:s');
        $this->pluginLogFile->write("[{$data}] Tarefa iniciada; \r\n");  
        
    }
    
    public function partidaIniciarAction() {
        $this->disabled();
        
        /**
         * Busca as partida que irao comecar em uma hora
         */
        $modelPartida = new Model_DbTable_Partida();
        $where = array(
            //'custom' => "date(partida_data) = date(now())"
            'custom' => "date_format(date_add(now(), INTERVAL 1 HOUR), '%Y-%m-%d %H:%i') = date_format(partida_data, '%Y-%m-%d %H:%i')"
        );
        
        $partidas = $modelPartida->getPartidas($where);
        
        $cont = 0;
        if ($partidas->count() > 0) { 
            /**
             * Busca osusuarios
             */
            $modelUsuario = new Model_DbTable_Usuario();
            $usuarios = $modelUsuario->fetchAll();
            
            foreach ($usuarios as $usuario) {
                
                $pluginMail = new Plugin_Mail();
                $paramns = serialize(array(
                    'usuario' => $usuario,
                    'partidas' => $partidas
                ));
                $pluginMail->inQueue('lembrete-partidas-iniciar.phtml', 'Ainda dá tempo!!!!', $paramns, $usuario->usuario_email);
                $cont++;                
            }
            
        }
        
        $log .= "[".date('d/m/Y H:i:s')."][Partida] {$cont} usuários notificados. \r\n";
        $log .= "[".date('d/m/Y H:i:s')."][Partida] Tarefa finalizada. \r\n";
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
