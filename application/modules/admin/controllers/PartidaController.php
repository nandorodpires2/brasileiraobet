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
                    //$this->populate($partida_id);
                    
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
        $partida_placar_mandante->setLabel("Placar Mandante: ");
        $partida_placar_mandante->setAttrib("class", "form-control");
        $partida_placar_mandante->setRequired();
        $partida_placar_mandante->setOrder(3);        
        $formPartida->addElement($partida_placar_mandante);
        
        $partida_placar_visitante = new Zend_Form_Element_Text("partida_placar_visitante");
        $partida_placar_visitante->setLabel("Placar Visitante: ");
        $partida_placar_visitante->setAttrib("class", "form-control");
        $partida_placar_visitante->setRequired();
        $partida_placar_visitante->setOrder(5);        
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
                        $updateAposta = array();
                        if ($aposta->aposta_placar_mandante == $partida->partida_placar_mandante && 
                            $aposta->aposta_placar_visitante == $partida->partida_placar_visitante) {
                            
                            $ganhadores[]['usuario_id'] = $aposta->usuario_id;       
                            $updateAposta['aposta_vencedora'] = 1;
                        } else {
                            $updateAposta['aposta_vencedora'] = 0;
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
                    
                    /**
                     * Notifica os usuarios
                     */
                    
                    Zend_Db_Table_Abstract::getDefaultAdapter()->commit();
                    
                    $this->_redirect("admin/partida");
                    
                } catch (Exception $ex) {
                    Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
                    die($ex->getMessage());
                }
                
            }
            
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
        $partidas = $modelPartida->getPartidas(1, true);
        $this->view->partidas = $partidas;
        
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
       $placares = array(0, 1, 2, 3, 4, 5);
       
       for ($i = $apostas; $i >= 1; $i--) {
           
           $i_ids = array_rand($ids);
           $i_placares_mandante = array_rand($placares);
           $i_placares_visitante = array_rand($placares);
           
           $data = array(
               'partida_id' => $partida_id,
               'usuario_id' => $ids[$i_ids],
               'aposta_codigo' => sha1(sha1(uniqid() . $partida_id)),
               'aposta_placar_mandante' => $placares[$i_placares_mandante],
               'aposta_placar_visitante' => $placares[$i_placares_visitante]
           );
           
           //Zend_Debug::dump($data); die();
           $modelAposta->insert($data);
       }
       
    }
    
}
