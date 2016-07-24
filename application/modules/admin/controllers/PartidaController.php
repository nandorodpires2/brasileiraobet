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
class Admin_PartidaController extends Zend_Controller_Action {
    
    public function init() {
        
        // Busca as rodadas partidas
        $modelPartida = new Model_DbTable_Partida();
        $rodadas = $modelPartida->getPartidasRodadas();
        $this->view->rodadas = $rodadas;
        
    }
    
    public function indexAction() {
        
        /**
         * Busca as partidas
         */
        $modelPartida = new Model_DbTable_Partida();
        $where = array(          
            'realizada' => 0
        );
        $partidas = $modelPartida->getPartidas($where);
        $this->view->partidas = $partidas;
        
    }
    
    public function addAction() {
        
        // form 
        $form = new Form_Admin_Partida();
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $data = $form->getValues();
                
                // formatando a data da partida
                $data['partida_data'] = $this->dateMatchFormat($data['partida_data'], $data['partida_horario']);
                
                // premio inicial
                $data['partida_fator_inicial'] = Zend_Registry::get("config")->premio->fator;
                
                /**
                 * Percentuais
                 */
                $data['partida_perc_premio1'] = Zend_Registry::get("config")->banca->perc->premio1;
                $data['partida_perc_premio2'] = Zend_Registry::get("config")->banca->perc->premio2;
                $data['partida_perc_premio3'] = Zend_Registry::get("config")->banca->perc->premio3;
                
                unset($data['partida_horario']);
                
                //Zend_Debug::dump($data); die();
                
                try {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                    
                    $modelPartida = new Model_DbTable_Partida();
                    $partida_id = $modelPartida->insert($data);     
                    
                    //$this->populate($partida_id);
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'success' => 'Partida cadastrada com sucesso'
                    ));
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                    
                } catch (Exception $ex) {
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'danger' => $ex->getTraceAsString()
                    ));
                }
                
                $this->_redirect("admin/partida/add");
                
            }
        }
        
    }
    
    public function editAction() {
        
    }
    
    public function deleteAction() {
        
    }
    
    /**
     * 
     */
    public function resultAction() {
       
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
            unset($data['submit']);
            
            try {

                Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();

                /**
                 * Cadastra o resultado
                 */
                $data['partida_realizada'] = 1;

                // verifica se deu empate ou quem ganhou                    
                if ($data['partida_placar_mandante'] == $data['partida_placar_visitante']) {
                    $data['partida_resultado'] = "E";
                } elseif ($data['partida_placar_mandante'] > $data['partida_placar_visitante']) {
                    $data['partida_resultado'] = "VM";
                } else {
                    $data['partida_resultado'] = "VV";
                }

                $modelPartida = new Model_DbTable_Partida();
                $modelPartida->updateById($data, $data['partida_id']);

                $this->_helper->flashMessenger->addMessage(array(
                    'success' => 'Resultado cadastrado com sucesso'
                ));

                Zend_Db_Table_Abstract::getDefaultAdapter()->commit();

                $this->_redirect("admin/partida");

            } catch (Exception $ex) {
                Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                $this->_helper->flashMessenger->addMessage(array(
                    'danger' => $ex->getMessage()
                ));
            }           
            
        }        
        
    }

    public function parciaisAction() {
        /**
         * Busca as partidas
         */
        $modelPartida = new Model_DbTable_Partida();
        $partidas = $modelPartida->getPartidasParcial();
        $this->view->partidas = $partidas;
        
        $this->inicializa($partidas);
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            
            foreach ($data['partida_id'] as $key => $value) {
                
                $placar_mandante = $data['placar_mandante'][$key];
                $placar_visitante = $data['placar_visitante'][$key];
                
                /**
                 * atualiza o placar da parcial
                 */
                $modelParcial = new Model_DbTable_Parcial();
                $update = array(
                    'parcial_placar_mandante' => $placar_mandante,
                    'parcial_placar_visitante' => $placar_visitante                
                );
                
                $modelParcial->update($update, "partida_id = {$value}");
                
                /**
                 * atualiza a qtde de vencedores
                 */
                $modelAposta = new Model_DbTable_Aposta();
                $parcialVencedoresPremio1 = $modelAposta->getApostasVencedorasParcial($value, 1);       
                $parcialVencedoresPremio2 = $modelAposta->getApostasVencedorasParcial($value, 2);       
                $parcialVencedoresPremio3 = $modelAposta->getApostasVencedorasParcial($value, 3);       
                
                $modelParcial->update(array(
                    'parcial_vencedores_premio1' => $parcialVencedoresPremio1->count(),
                    'parcial_vencedores_premio2' => $parcialVencedoresPremio2->count(),
                    'parcial_vencedores_premio3' => $parcialVencedoresPremio3->count(),
                ), "partida_id = {$value}");
                
            }
            
            $this->_redirect("admin/partida/parciais");
            
        }
        
    }
    
    /**
     * 
     */
    public function proccessAction() {
        
        /**
         * Busca as partidas
         */
        $modelPartida = new Model_DbTable_Partida();
        $where = array(
            'realizada' => 1
        );
        $partidas = $modelPartida->getPartidas($where, 'partida_data desc', 10);          
        $this->view->partidas = $partidas;
        
    }
    
    public function abertaAction() {
        
        $partida_rodada = $this->getRequest()->getParam('rodada', false);
        
        if (!$partida_rodada) {
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => 'Passe a rodada como parametro!'
            ));
            $this->_redirect("admin/partida");
        }
        
        /**
         * Busca as partidas da rodada
         */
        $modelPartida = new Model_DbTable_Partida();
        $partidas = $modelPartida->fetchAll("
            partida_rodada = {$partida_rodada}
            and partida_serie = 1
        ");
        
        if ($partidas->count() !== 10) {
            $this->_helper->flashMessenger->addMessage(array(
                'warning' => 'As partidas da rodada ainda não estão completas!'
            ));
            $this->_redirect("admin/partida");
        }
        
        /**
         * Busca os usuarios
         */
        $modelUsuarios = new Model_DbTable_Usuario();
        $usuarios = $modelUsuarios->fetchAll();
        
        foreach ($usuarios as $usuario) {
            $pluginMail = new Plugin_Mail();
            $paramns = serialize(array(
                'usuario_nome' => $usuario->usuario_nome,
                'partida_rodada' => $partida_rodada
            ));
            $pluginMail->inQueue('partidas-abertas-apostar.phtml', 'Já pode apostar!!!!', $paramns, $usuario->usuario_email);
        }
        
        $this->_helper->flashMessenger->addMessage(array(
            'success' => 'Mensagens enviadas com sucess'
        ));
        $this->_redirect("admin/partida");
        
    }

    public function geraApostaAction() {
        
        $this->_helper->viewRenderer->setNoRender();
        
        $partida_id = $this->getRequest()->getParam("id", false);
        
        if (!$partida_id) {
            $this->_helper->flashMessenger->addMessage(array(
                'danger' => "Passe o partida como parâmetro!"
            ));
            $this->_redirect("admin/partida");
        }
        
        $apostas = $this->populate($partida_id);
        
        $this->_helper->flashMessenger->addMessage(array(
            'success' => "{$apostas} apostas geradas com sucesso"
        ));
        
        $this->_redirect("admin/partida");
        
    }

    /**
     * 
     * @param type $data
     * @param type $hora
     * @return string
     */
    protected function dateMatchFormat($data, $hora) {
        
        $data = implode('-', array_reverse(explode('/', $data)));
        $data .= ' ' . $hora;
        
        return $data;
                
    }
    
    /**
     * 
     */
    private function populate($partida_id) {
        
        if (!Zend_Registry::get("config")->aposta->populate) {
            return;
        }
        
        $modelAposta = new Model_DbTable_Aposta();

        /**
         * Buscas os usuarios maquina
         */
        $modelUsuario = new Model_DbTable_Usuario();
        $usuarios = $modelUsuario->getUsuariosMaquina();
        
        if ($usuarios->count() == 0) {
            return 0;
        }

        $ids = array();
        foreach ($usuarios as $usuario) {
            $modelLancamento = new Model_DbTable_Lancamento();
            $saldo = $modelLancamento->getSaldoUsuario($usuario->usuario_id);
            
            if ($saldo >= 3) {
                $ids[] = $usuario->usuario_id;
            }
        }

        $apostas = rand(7, $usuarios->count());
        $placares = array(0, 1, 2, 3, 4, 5);
        
        $cont = 0;
        for ($i = $apostas; $i >= 1; $i--) {
           
            $i_ids = array_rand($ids);
            $i_placares_mandante = array_rand($placares);
            $i_placares_visitante = array_rand($placares);
           
            /**
             * 
             */
            $modelLancamento = new Model_DbTable_Lancamento();
            $saldo = $modelLancamento->getSaldoUsuario($ids[$i_ids]);

            /**
             * Partida
             */
            $modelPartida = new Model_DbTable_Partida();
            $partida = $modelPartida->getById($partida_id);
            
            if ($saldo >= $partida->partida_valor) {

                $lancamento = array(
                    'usuario_id' => $ids[$i_ids],
                    'lancamento_descricao' => 'DÉBITO APOSTA PARTIDA #' . $partida->partida_id,
                    'partida_id' => $partida->partida_id,
                    'lancamento_valor' => $partida->partida_valor * -1
                );
                $modelLancamento = new Model_DbTable_Lancamento();
                $modelLancamento->insert($lancamento);                
                
                if ($placares[$i_placares_mandante] == $placares[$i_placares_visitante]) {
                    $aposta_resultado = "E";
                } elseif ($placares[$i_placares_mandante] > $placares[$i_placares_visitante]) {
                    $aposta_resultado = "VM";
                } else {
                    $aposta_resultado = "VV";
                }
                
                $data = array(
                    'partida_id' => $partida_id,
                    'usuario_id' => $ids[$i_ids],
                    'aposta_codigo' => sha1(sha1(uniqid() . $partida_id)),
                    'aposta_placar_mandante' => $placares[$i_placares_mandante],
                    'aposta_placar_visitante' => $placares[$i_placares_visitante],
                    'aposta_resultado' => $aposta_resultado
                );

                $modelAposta->insert($data);
                $cont++;
            
            }
        }
        
        return $cont;
       
    }
    
    /**
     * 
     * @param type $partidas
     */
    protected function inicializa($partidas) {
        
        foreach ($partidas as $partida) {
            if ($partida->parcial_placar_mandante === null) {
                
                $modelParcial = new Model_DbTable_Parcial();
                
                $modelAposta = new Model_DbTable_Aposta();
                $parcialVencedoresPremio1 = $modelAposta->getApostasVencedorasParcial($partida->partida_id, 1);       
                $parcialVencedoresPremio2 = $modelAposta->getApostasVencedorasParcial($partida->partida_id, 2);       
                $parcialVencedoresPremio3 = $modelAposta->getApostasVencedorasParcial($partida->partida_id, 3); 
                
                $data = array(
                    'partida_id' => $partida->partida_id,
                    'parcial_placar_mandante' => 0,
                    'parcial_placar_visitante' => 0,
                    'parcial_vencedores_premio1' => $parcialVencedoresPremio1->count(),
                    'parcial_vencedores_premio2' => $parcialVencedoresPremio2->count(),
                    'parcial_vencedores_premio3' => $parcialVencedoresPremio3->count(),
                );
                
                if (!$modelParcial->insert($data)) {
                    throw new Exception("Falha ao inicializar as parciais");
                }
            }
        }
        
        return true;
        
    }
    
    public function correctAction() {
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        // buscar as apostas vencedoras que nao tem valor de premiacao
        $modelAposta = new Model_DbTable_Aposta();
        $where = "aposta.aposta_vencedora = 1 and aposta.aposta_vencedora_valor is null";
        $apostas = $modelAposta->fetchAll($where);
        
        $cont = 0;
        
        foreach ($apostas as $aposta) {
            
            $modelPartida = new Model_DbTable_Partida();
            $partida = $modelPartida->getById($aposta->partida_id);
            
            // busca o valor do premio            
            if ($aposta->aposta_vencedora_premio == 1) {
                $aposta_vencedora_valor = $partida->partida_vencedores_premio1 != 0 ? $partida->partida_valor_premio1 / $partida->partida_vencedores_premio1 : 0;
            }
            
            if ($aposta->aposta_vencedora_premio == 2) {
                $aposta_vencedora_valor = $partida->partida_vencedores_premio2 != 0 ? $partida->partida_valor_premio2 / $partida->partida_vencedores_premio2 : 0;
            }
            
            if ($aposta->aposta_vencedora_premio == 3) {
                $aposta_vencedora_valor = $partida->partida_vencedores_premio3 != 0 ? $partida->partida_valor_premio3 / $partida->partida_vencedores_premio3 : 0;
            }
            
            // atualiza o valor
            $update = array(
                'aposta_vencedora_valor' => $aposta_vencedora_valor
            );
            $modelAposta->updateById($update, $aposta->aposta_id);
            
            $cont++;
            
        }
        
        echo $cont . " apostas processadas";
        
    }
    
}
