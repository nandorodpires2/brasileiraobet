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
        
    }
    
    public function indexAction() {
        
        /**
         * Busca as partidas
         */
        $modelPartida = new Model_DbTable_Partida();
        $partidas = $modelPartida->getPartidas(0, true);
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
                // porcentagem 
                $data['partida_porcentagem'] = Zend_Registry::get("config")->banca->porcentagem;
                
                unset($data['partida_horario']);
                
                try {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                    
                    $modelPartida = new Model_DbTable_Partida();
                    $partida_id = $modelPartida->insert($data);     
                    
                    $this->populate($partida_id);
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                    
                } catch (Exception $ex) {
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                    die($ex->getMessage());
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
       
        $partida_id = $this->getRequest()->getParam("id");
        
        /**
         * Busca dados da partida
         */
        $modelPartida = new Model_DbTable_Partida();
        $partida = $modelPartida->getById($partida_id);
        
        /**
         * Form 
         */
        $formPartida = new Form_Admin_Partida();
        
        /**
         * Removendo alguns elementos
         */
        $formPartida->removeElement("partida_data");
        $formPartida->removeElement("partida_horario");        
        
        /**
         * Adicionando elementos (placares)
         */
        $partida_placar_mandante = new Zend_Form_Element_Text("partida_placar_mandante");
        $partida_placar_mandante->setLabel("Placar {$partida->time_mandante_nome}: ");
        $partida_placar_mandante->setAttrib("class", "form-control");
        $partida_placar_mandante->setRequired();
        $partida_placar_mandante->setOrder(4);        
        $formPartida->addElement($partida_placar_mandante);

        $partida_placar_visitante = new Zend_Form_Element_Text("partida_placar_visitante");
        $partida_placar_visitante->setLabel("Placar {$partida->time_visitante_nome}: ");
        $partida_placar_visitante->setAttrib("class", "form-control");
        $partida_placar_visitante->setRequired();
        $partida_placar_visitante->setOrder(6);        
        $formPartida->addElement($partida_placar_visitante);
                
        $formPartida->populate($partida->toArray());
        $this->view->formPartida = $formPartida;
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($formPartida->isValid($data)) {
                
                $data = $formPartida->getValues();
                
                try {
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->beginTransaction();
                    
                    /**
                     * Cadastra o resultado
                     */
                    $data['partida_realizada'] = 1;
                    $modelPartida->updateById($data, $partida_id);
                    
                    /**
                     * Verifica os ganhadores e seta os premios
                     */
                    $modelApostas = new Model_DbTable_Aposta();
                    $apostas = $modelApostas->getApostas($partida_id);
                    
                    $partida = $modelPartida->getById($partida_id);
                    
                    $ganhadores = array();
                    
                    foreach ($apostas as $aposta) {
                        
                        $modelNotificacao = new Model_DbTable_Notificacao();
                        
                        $updateAposta = array();
                        if ($aposta->aposta_placar_mandante == $partida->partida_placar_mandante && 
                            $aposta->aposta_placar_visitante == $partida->partida_placar_visitante) {
                            
                            $ganhadores[]['usuario_id'] = $aposta->usuario_id;       
                            $updateAposta['aposta_vencedora'] = 1;
                            
                            // notifica
                            $conteudo = " 
                                Parabéns! Sua aposta na partida 
                                {$aposta->time_nome_mandante} {$aposta->aposta_placar_mandante} X {$aposta->aposta_placar_visitante} {$aposta->time_nome_visitante}
                                foi vencedora
                            ";                            
                            $notificacao = array(
                                'usuario_id' => $aposta->usuario_id,
                                'notificacao_conteudo' => $conteudo
                            );
                            $modelNotificacao->insert($notificacao);
                            
                        } else {
                            $updateAposta['aposta_vencedora'] = 0;
                            
                            // notifica
                            $conteudo = " 
                                Sua aposta na partida 
                                {$aposta->time_nome_mandante} {$aposta->aposta_placar_mandante} X {$aposta->aposta_placar_visitante} {$aposta->time_nome_visitante}
                                infelismente não foi vencedora
                            ";
                            $notificacao = array(
                                'usuario_id' => $aposta->usuario_id,
                                'notificacao_conteudo' => $conteudo
                            );
                            $modelNotificacao->insert($notificacao);
                            
                        }
                        $updateAposta['aposta_processada'] = 1;
                        
                        $modelApostas->updateById($updateAposta, $aposta->aposta_id);
                    }
                    
                    $montante = $this->view->getHelper('montante');
                    $premio = $montante->montante($partida_id) / count($ganhadores);
                                        
                    /**
                     * Atualiza os vencedores na tabela de partida
                     */
                    $modelPartida->updateById(array(
                        'partida_vencedores' => count($ganhadores),
                        'partida_montante' => $montante->montante($partida_id)
                    ), $partida_id);
                    
                    foreach ($ganhadores as $ganhador) {
                        $data = array(
                            'lancamento_descricao' => 'PRÊMIO APOSTA PARTIDA #' . $partida_id,
                            'usuario_id' => $ganhador['usuario_id'],
                            'lancamento_valor' => $premio,
                            'partida_id' => $partida_id
                        );
                        $modelLancamento = new Model_DbTable_Lancamento();
                        $modelLancamento->insert($data);
                    }
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                    
                    $this->_redirect("admin/partida");
                    
                } catch (Exception $ex) {
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                    die($ex->getMessage());
                }
                
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
                $apostasVencedoras = $modelAposta->getApostasVencedorasParcial($value);                
                $modelParcial->update(array(
                    'parcial_vencedores' => $apostasVencedoras->count(),           
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
        $partidas = $modelPartida->getPartidas(1, true, "partida_data desc", 10);
        $this->view->partidas = $partidas;
        
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

        $ids = array();
        foreach ($usuarios as $usuario) {
            $ids[] = $usuario->usuario_id;
        }

        $apostas = rand(7, $usuarios->count());
        $placares = array(0, 1, 2, 3);
        
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
                
                $data = array(
                    'partida_id' => $partida_id,
                    'usuario_id' => $ids[$i_ids],
                    'aposta_codigo' => sha1(sha1(uniqid() . $partida_id)),
                    'aposta_placar_mandante' => $placares[$i_placares_mandante],
                    'aposta_placar_visitante' => $placares[$i_placares_visitante]
                );

                //Zend_Debug::dump($data); die();
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
                
                $modelAPosta = new Model_DbTable_Aposta();
                $apostasVencedoras = $modelAPosta->getApostasVencedorasParcial($partida->partida_id);
                
                $data = array(
                    'partida_id' => $partida->partida_id,
                    'parcial_placar_mandante' => 0,
                    'parcial_placar_visitante' => 0,
                    'parcial_vencedores' => $apostasVencedoras->count()
                );
                
                if (!$modelParcial->insert($data)) {
                    throw new Exception("Falha ao inicializar as parciais");
                }
            }
        }
        
        return true;
        
    }
    
}
