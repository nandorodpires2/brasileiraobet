<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Premio
 *
 * @author Fernando
 */
class Plugin_Premio {
    
    protected $partida;
    
    protected $primeiroPremio;
    protected $segundoPremio;
    protected $terceiroPremio;

    public function __construct($partida_id) {
        
        /**
         * Busca os dados da partida
         */
        $modelPartida = new Model_DbTable_Partida();
        $this->partida = $modelPartida->getById($partida_id);
        
        /**
         * Caso nao exista partida
         */
        if (!$this->partida) {
            throw new Exception("Nenhuma partida encontrada!");
        }
        
        $this->init();
        
    }
    
    protected function init() {        
        $this->setPrimeiroPremio();
        $this->setSegundoPremio();
        $this->setTerceiroPremio();
    }

    public function getPrimeiroPremio() {        
        return $this->primeiroPremio;
    }

    public function getSegundoPremio() {
        return $this->segundoPremio;
    }

    public function getTerceiroPremio() {
        return $this->terceiroPremio;
    }

    protected function setPrimeiroPremio() {
        $primeiroPremio = ($this->inicial() + $this->montante()) * ($this->partida->partida_perc_premio1/100) * $this->partida->partida_coringa_valor;        
        $this->primeiroPremio = $primeiroPremio;
        return $this;
    }

    protected function setSegundoPremio() {
        $segundoPremio = ($this->inicial() + $this->montante()) * ($this->partida->partida_perc_premio2/100) * $this->partida->partida_coringa_valor;        
        $this->segundoPremio = $segundoPremio;
        return $this;
    }

    protected function setTerceiroPremio() {
        $terceiroPremio = ($this->inicial() + $this->montante()) * ($this->partida->partida_perc_premio3/100) * $this->partida->partida_coringa_valor;        
        $this->terceiroPremio = $terceiroPremio;
        return $this;
    }

    private function inicial() {        
        $inicial = ($this->partida->partida_valor * (int)$this->partida->partida_fator_inicial);
        return $inicial;        
    }
    
    private function montante() {
        $modelAposta = new Model_DbTable_Aposta();
        $montante = $modelAposta->getMontante($this->partida->partida_id);
        return $montante;
    }
    
    public function __toString() {
        
        $calculo = array(
            'premio_1' => array(
                '(' . $this->inicial() . ' + ' . $this->montante() . ') *  (' . $this->partida->partida_perc_premio1 . ' / 100)'
            ),
            'premio_2' => array(
                '(' . $this->inicial() . ' + ' . $this->montante() . ') *  (' . $this->partida->partida_perc_premio2 . ' / 100)'
            ),
            'premio_3' => array(
                '(' . $this->inicial() . ' + ' . $this->montante() . ') *  (' . $this->partida->partida_perc_premio3 . ' / 100)'
            ),
        );
        
        Zend_Debug::dump($calculo);
        
    }
    
}
