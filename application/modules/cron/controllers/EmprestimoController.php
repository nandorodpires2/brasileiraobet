<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EmprestimoController
 *
 * @author Fernando
 */
class Cron_EmprestimoController extends Zend_Controller_Action {
    
    public function init() {
        if (!Plugin_Cron::checkAccess()) {
            $server = $this->getRequest()->getServer('REMOTE_ADDR');
			
            $this->pluginLogFile = new Plugin_LogFile("logEmprestimoError.txt");
            $data = date('d/m/Y H:i:s');
            $this->pluginLogFile->write("[{$data}] Sem permissão de acesso [{$server}]; \r\n");  
            die();     
        }
        
        // file log
        $this->pluginLogFile = new Plugin_LogFile("logEmprestimo.txt");
        $data = date('d/m/Y H:i:s');
        $this->pluginLogFile->write("[{$data}] Tarefa iniciada; \r\n");
        
    }
    
    public function indexAction() {
        
        $log = "";
        $this->disabled();
        
        /**
         * Busca emprestimos pendentes
         */
        $modelEmprestimo = new Model_DbTable_Emprestimo();
        $where = $modelEmprestimo->getDefaultAdapter()->quoteInto("emprestimo_pago = ?", 0);
        $emprestimos = $modelEmprestimo->fetchAll($where);
        
        //Zend_Debug::dump($emprestimos); die();
        
        foreach ($emprestimos as $emprestimo) {
         
            // busca o saldo atual do usuario
            $modelUsuario = new Model_DbTable_Usuario();
            $usuario = $modelUsuario->getById($emprestimo->usuario_id);

            /**
             * Saldo do usuario
             */
            $modelLancamento = new Model_DbTable_Lancamento();
            //saldo para apostas
            $saldo = $modelLancamento->getSaldoUsuario($usuario->usuario_id);
            
            // calcula o valor a ser pago
            $valor_taxa = $emprestimo->emprestimo_valor * ($emprestimo->emprestimo_taxa / 100);
            $emprestimo_valor = $emprestimo->emprestimo_valor + $valor_taxa;
                        
            if ($saldo >= $emprestimo_valor) {
                
                try {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                
                    // lanca o debito                
                    $zendCurrency = new Zend_Currency();
                    $lancamento = array(
                        'usuario_id' => $usuario->usuario_id,
                        'lancamento_descricao' => "DÉBITO EMPRÉSTIMO " . $zendCurrency->toCurrency($emprestimo->emprestimo_valor) . " + " . $zendCurrency->toCurrency($valor_taxa),
                        'lancamento_valor' => $emprestimo_valor * -1
                    );
                    $modelLancamento = new Model_DbTable_Lancamento();                    
                    $modelLancamento->insert($lancamento);

                    // notifica
                    $conteudo = "Foi lançado um débito em sua conta relativo à cobrança de empréstimo.";
                    $notificacao = array(
                        'usuario_id' => $usuario->usuario_id,
                        'notificacao_conteudo' => $conteudo
                    );
                    $modeNotificacao = new Model_DbTable_Notificacao();
                    $modeNotificacao->insert($notificacao);

                    // informa por email 
                    
                    // atualiza a tabela de pagamento
                    $update = array(
                        'emprestimo_pago' => 1,
                        'emprestimo_pago_valor' => $emprestimo_valor,
                        'emprestimo_data_pago' => date('Y-m-d H:i:s')
                    );
                    $modelEmprestimo->updateById($update, $emprestimo->emprestimo_id);
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                    
                    $log .= "[".date('d/m/Y H:i:s')."] Emprestimo (#{$emprestimo->emprestimo_id}) cobrado. \r\n";
                    
                } catch (Exception $ex) {
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                    die($ex->getTraceAsString());
                }
                
            }
            
        }
        
        $log .= "[".date('d/m/Y H:i:s')."] Tarefa finalizada. \r\n";
        $this->pluginLogFile->write($log);        
        
    }
    
    private function disabled() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }
    
}
