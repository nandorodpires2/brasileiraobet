<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Classificacao
 *
 * @author Fernando
 */
class Plugin_Classificacao {
    
    protected $_classificacao;
    protected $time_id;
    
    protected $jogos;
    protected $pontos;
    protected $vitorias;
    protected $empates;
    protected $derrotas;
    protected $golspro;
    protected $golscontra;

    public function __construct($time_id) {
        $this->time_id = $time_id;
        // dados da classificacao
        $modelClassificacao = new Model_DbTable_Classificacao();
        $this->_classificacao = $modelClassificacao->getByField("classificacao.time_id", $time_id);      
        
        //
        $this->partidas();
        
    }
    
    public function atualizar() {        
        
        $modelClassificacao = new Model_DbTable_Classificacao;
        
        $data = array(
            'time_id' => $this->time_id,
            'classificacao_jogos' => $this->getJogos(),
            'classificacao_pontos' => $this->getPontos(),
            'classificacao_vitorias' => $this->getVitorias(),
            'classificacao_empates' => $this->getEmpates(),
            'classificacao_derrotas' => $this->getDerrotas(),
            'classificacao_golspro' => $this->getGolspro(),
            'classificacao_golscontra' => $this->getGolscontra()            
        );
        
        if ($this->_classificacao) {
            $modelClassificacao->updateById($data, $this->_classificacao->classificacao_id);
        } else {
            $modelClassificacao->insert($data);
        }
        
    }

    public function getJogos() {
        return $this->jogos;
    }

    public function getPontos() {
        return $this->pontos;
    }

    public function getVitorias() {
        return $this->vitorias;
    }

    public function getEmpates() {
        return $this->empates;
    }

    public function getDerrotas() {
        return $this->derrotas;
    }

    public function getGolspro() {
        return $this->golspro;
    }

    public function getGolscontra() {
        return $this->golscontra;
    }
    
    public function setJogos($jogos) {
        $this->jogos = $jogos;
        return $this;
    }

    public function setPontos($pontos) {
        $this->pontos = $pontos;
        return $this;
    }

    public function setVitorias($vitorias) {
        $this->vitorias = $vitorias;
        return $this;
    }

    public function setEmpates($empates) {
        $this->empates = $empates;
        return $this;
    }

    public function setDerrotas($derrotas) {
        $this->derrotas = $derrotas;
        return $this;
    }

    public function setGolspro($golspro) {
        $this->golspro = $golspro;
        return $this;
    }

    public function setGolscontra($golscontra) {
        $this->golscontra = $golscontra;
        return $this;
    }
    
    protected function partidas() {
        $modelPartida = new Model_DbTable_Partida();
        
        $partidasMandante = $modelPartida->getPartidasMandante($this->time_id, 1);
        $partidasVisitante = $modelPartida->getPartidasVisitante($this->time_id, 1);
        
        $jogos = 0;
        $pontos = 0;
        $vitorias = 0;
        $empates = 0;
        $derrotas = 0;
        $golspro = 0;
        $golscontra = 0;
        
        foreach ($partidasMandante as $partida) {
            $jogos++;
            
            if ($partida->partida_resultado === "VM") {
                $pontos += 3;
                $vitorias++;
            }
            
            if ($partida->partida_resultado === "E") {
                $pontos += 1;
                $empates++;
            }
            
            $golspro += $partida->partida_placar_mandante;
            $golscontra += $partida->partida_placar_visitante;
            
        }
        
        foreach ($partidasVisitante as $partida) {
            $jogos++;
            
            if ($partida->partida_resultado === "VV") {
                $pontos += 3;
                $vitorias++;
            }
            
            if ($partida->partida_resultado === "E") {
                $pontos += 1;
                $empates++;
            }
            
            $golspro += $partida->partida_placar_visitante;
            $golscontra += $partida->partida_placar_mandante;
        }
        
        $this->setJogos($jogos);
        $this->setPontos($pontos);
        $this->setVitorias($vitorias);
        $this->setEmpates($empates);
        $this->setDerrotas($derrotas);
        $this->setGolspro($golspro);
        $this->setGolscontra($golscontra);
        
    }
    
    /**
     * Funcao que busca dados de complemento da classificacao caso
     * a mesma esteja desatualizada
     */
    protected function complemento() {
        
    }

}
