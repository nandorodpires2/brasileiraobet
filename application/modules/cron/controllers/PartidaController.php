<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PartidaController
 *
 * @author Fernando
 */
class Cron_PartidaController extends Zend_Controller_Action {
    
    public function init() {
        if (!Plugin_Cron::checkAccess()) {
            
            $server = $this->getRequest()->getServer('REMOTE_ADDR');
			
            $this->pluginLogFile = new Plugin_LogFile("logPartidaError.txt");
            $data = date('d/m/Y H:i:s');
            $this->pluginLogFile->write("[{$data}]Sem permissão de acesso [{$server}]; \r\n");  
            die();
            
        }
        
        // file log
        $this->pluginLogFile = new Plugin_LogFile("logPartida.txt");
        $data = date('d/m/Y H:i:s');
        $this->pluginLogFile->write("[{$data}] Tarefa iniciada; \r\n");  
    }
    
    public function processarAction() {
        
        $this->disabled(); 
        
        $log = "";
        
        /**
         * Busca as partidas nao processadas
         */
        $modelPartida = new Model_DbTable_Partida();
        $where = array(
            'realizada' => 1,
            'processada' => 0
        );
        $partidas = $modelPartida->getPartidas($where);
        
        foreach ($partidas as $partida) {
            
            try {
                
                Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                
                $this->primeiroPremio($partida);
                $this->segundoPremio($partida);
                $this->terceiroPremio($partida);
                
                // processa as apostas que nao foram vencedoras
                $this->naoVencedororas($partida);
                
                // seta a partida como processada
                $modelPartida->updateById(array('partida_processada' => 1), $partida->partida_id);
                
                Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                
                $log .= "[".date('d/m/Y H:i:s')."][Porcessar] Partida [{$partida->partida_id}] processada. \r\n";
                $this->pluginLogFile->write($log);
                
            } catch (Exception $ex) {
                Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                die($ex->getMessage());
            }            
            
        }        
        
        $log .= "[".date('d/m/Y H:i:s')."][Porcessar] Tarefa finalizada. \r\n";
        $this->pluginLogFile->write($log);
        
    }

    private function primeiroPremio($partida) {
        
        $modelAposta = new Model_DbTable_Aposta();
        $apostas = $modelAposta->getApostasVencedorasByPremio($partida->partida_id, 1);
        
        $pluginPremio = new Plugin_Premio($partida->partida_id);                
        $premio = $apostas->count() > 0 ? $pluginPremio->getPrimeiroPremio() / $apostas->count() : 0;
        
        if ($premio < 0.01) {
            $premio = 0.10;
        }
        
        /**
         * Atualizar dados da partida
         */
        $modelPartida = new Model_DbTable_Partida();
        $modelPartida->updateById(array(
            'partida_vencedores_premio1' => $apostas->count(),
            'partida_valor_premio1' => $pluginPremio->getPrimeiroPremio()
        ), $partida->partida_id);
        
        foreach ($apostas as $aposta) {
            
            $string_mandante = $partida->time_mandante_nome . ' ' . $aposta->aposta_placar_mandante;
            $string_visitante = $aposta->aposta_placar_visitante . ' ' . $partida->time_visitante_nome;
            $descricao = "PRÊMIO PARTIDA ({$string_mandante} X {$string_visitante})";
            
            // lanca o premio para o usuario            
            $lancamento = array(
                'lancamento_valor' => $premio,
                'lancamento_descricao' => $descricao,
                'usuario_id' => $aposta->usuario_id
            );
            $this->lancamento($lancamento);            
            
            // lanca a notificacao
            $vencedora = $apostas->count() > 1 ? 'uma das vencedoras' : 'a vencedora';
            $conteudo = " 
                <p>Parabéns!</p>
                <p>Sua aposta na partida {$string_mandante} X {$string_visitante} foi {$vencedora} do 1º Prêmio</p>
            ";
            $notificacao = array(
                'usuario_id' => $aposta->usuario_id,
                'notificacao_conteudo' => $conteudo
            );
            $this->notificacao($notificacao);
            
            // grava na fila de emails
            $pluginMail = new Plugin_Mail();
            $paramns = serialize(array(
                'partida' => $partida,
                'vencedores' => $apostas->count(),
                'premio' => $premio
            ));
            $pluginMail->inQueue('usuario-premio1.phtml', 'Sua aposta foi VENCEDORA!!!!', $paramns, $aposta->usuario_email);
            
            // seta a aposta como processada
            $modelAposta->updateById(array(
                'aposta_vencedora' => 1,
                'aposta_vencedora_premio' => 1,
                'aposta_vencedora_valor' => $premio,
                'aposta_processada' => 1
            ), $aposta->aposta_id);
            
        }
        
    }
    
    private function segundoPremio($partida) {
        
        $modelAposta = new Model_DbTable_Aposta();
        $apostas = $modelAposta->getApostasVencedorasByPremio($partida->partida_id, 2);
               
        $pluginPremio = new Plugin_Premio($partida->partida_id);            
        $premio = $apostas->count() > 0 ? $pluginPremio->getSegundoPremio() / $apostas->count() : 0;
                
        if ($premio < 0.01) {
            $premio = 0.10;
        }
        
        /**
         * Atualizar dados da partida
         */
        $modelPartida = new Model_DbTable_Partida();
        $modelPartida->updateById(array(
            'partida_vencedores_premio2' => $apostas->count(),
            'partida_valor_premio2' => $pluginPremio->getSegundoPremio()
        ), $partida->partida_id);
        
        foreach ($apostas as $aposta) {
            
            $string_mandante = $partida->time_mandante_nome . ' ' . $aposta->aposta_placar_mandante;
            $string_visitante = $aposta->aposta_placar_visitante . ' ' . $partida->time_visitante_nome;
            $descricao = "PRÊMIO PARTIDA ({$string_mandante} X {$string_visitante})";
            
            // lanca o premio para o usuario            
            $lancamento = array(
                'lancamento_valor' => $premio,
                'lancamento_descricao' => $descricao,
                'usuario_id' => $aposta->usuario_id
            );
            $this->lancamento($lancamento);            
            
            // lanca a notificacao
            $vencedora = $apostas->count() > 1 ? 'uma das vencedoras' : 'a vencedora';
            $conteudo = " 
                <p>Parabéns!</p>
                <p>Sua aposta na partida {$string_mandante} X {$string_visitante} foi {$vencedora} do 2º Prêmio</p>
            ";
            $notificacao = array(
                'usuario_id' => $aposta->usuario_id,
                'notificacao_conteudo' => $conteudo
            );
            $this->notificacao($notificacao);
            
            // grava na fila de emails
            $pluginMail = new Plugin_Mail();
            $paramns = serialize(array(
                'partida' => $partida,
                'vencedores' => $apostas->count(),
                'premio' => $premio
            ));
            $pluginMail->inQueue('usuario-premio2.phtml', 'Sua aposta foi VENCEDORA!!!!', $paramns, $aposta->usuario_email);
            
            // seta a aposta como processada
            $modelAposta->updateById(array(
                'aposta_vencedora' => 1,
                'aposta_vencedora_premio' => 2,
                'aposta_vencedora_valor' => $premio,
                'aposta_processada' => 1
            ), $aposta->aposta_id);
            
        }
        
    }
    
    private function terceiroPremio($partida) {
        
        $modelAposta = new Model_DbTable_Aposta();
        $apostas = $modelAposta->getApostasVencedorasByPremio($partida->partida_id, 3);
        
        $pluginPremio = new Plugin_Premio($partida->partida_id);        
        $premio = $apostas->count() > 0 ? $pluginPremio->getTerceiroPremio() / $apostas->count() : 0;
        
        if ($premio < 0.01) {
            $premio = 0.10;
        }
        
        /**
         * Atualizar dados da partida
         */
        $modelPartida = new Model_DbTable_Partida();
        $modelPartida->updateById(array(
            'partida_vencedores_premio3' => $apostas->count(),
            'partida_valor_premio3' => $pluginPremio->getTerceiroPremio()
        ), $partida->partida_id);
        
        foreach ($apostas as $aposta) {
            
            $string_mandante = $partida->time_mandante_nome . ' ' . $aposta->aposta_placar_mandante;
            $string_visitante = $aposta->aposta_placar_visitante . ' ' . $partida->time_visitante_nome;
            $descricao = "PRÊMIO PARTIDA ({$string_mandante} X {$string_visitante})";
            
            // lanca o premio para o usuario            
            $lancamento = array(
                'lancamento_valor' => $premio,
                'lancamento_descricao' => $descricao,
                'usuario_id' => $aposta->usuario_id
            );
            $this->lancamento($lancamento);            
            
            // lanca a notificacao
            $vencedora = $apostas->count() > 1 ? 'uma das vencedoras' : 'a vencedora';
            $conteudo = " 
                <p>Parabéns!</p>
                <p>Sua aposta na partida {$string_mandante} X {$string_visitante} foi {$vencedora}  do 3º Prêmio</p>
            ";
            $notificacao = array(
                'usuario_id' => $aposta->usuario_id,
                'notificacao_conteudo' => $conteudo
            );
            $this->notificacao($notificacao);
            
            // grava na fila de emails
            $pluginMail = new Plugin_Mail();
            $paramns = serialize(array(
                'partida' => $partida,
                'vencedores' => $apostas->count(),
                'premio' => $premio
            ));
            $pluginMail->inQueue('usuario-premio3.phtml', 'Sua aposta foi VENCEDORA!!!!', $paramns, $aposta->usuario_email);
            
            // seta a aposta como processada
            $modelAposta->updateById(array(
                'aposta_vencedora' => 1,
                'aposta_vencedora_premio' => 3,
                'aposta_vencedora_valor' => $premio,
                'aposta_processada' => 1
            ), $aposta->aposta_id);
            
        }
        
    }

    public function naoVencedorasAction() {
        
        $this->disabled();
    
        $partida_id = $this->getRequest()->getParam('id');
        
        $modelPartida = new Model_DbTable_Partida();
        $partida = $modelPartida->getById($partida_id);
        
        if ($partida) {
            $this->naoVencedororas($partida);
        }
        
        echo "OK";
        
    }

    private function naoVencedororas($partida) {
        
        // busca as apostas que nao foram vencedoras
        $modelAposta = new Model_DbTable_Aposta();
        $apostas = $modelAposta->getApostas($partida->partida_id, null, null, 0);
        
        foreach ($apostas as $aposta) {
            
            // lanca a notificacao
            $string_mandante = $partida->time_mandante_nome . ' ' . $aposta->aposta_placar_mandante;
            $string_visitante = $aposta->aposta_placar_visitante . ' ' . $partida->time_visitante_nome;            
            $conteudo = " 
                <p>Parabéns!</p>
                <p>Sua aposta na partida {$string_mandante} X {$string_visitante} infelizmente não foi vencedora. Continue tentando.</p>
            ";
            $notificacao = array(
                'usuario_id' => $aposta->usuario_id,
                'notificacao_conteudo' => $conteudo
            );
            $this->notificacao($notificacao);
            
            // seta a aposta como processada
            $modelAposta->updateById(array(
                'aposta_vencedora' => 0,
                'aposta_vencedora_premio' => null,
                'aposta_processada' => 1
            ), $aposta->aposta_id);
            
        }
        
        return true;
        
    }


    protected function lancamento($data) {
        
        $modelLancamento = new Model_DbTable_Lancamento();
        try {
            $modelLancamento->insert($data);
            return true;
        } catch (Exception $ex) {
            throw new Exception('LANCAMENTO - ' . $ex->getMessage());
        }
        
    }
    
    protected function notificacao($data) {
        
        $modelNotificacao = new Model_DbTable_Notificacao();
        
        try {
            $modelNotificacao->insert($data);
            return true;
        } catch (Exception $ex) {
            throw new Exception('NOTIFICACAO - ' . $ex->getMessage());
        }
        
    }

    /**
     * 
     */
    private function disabled() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }
    
}
