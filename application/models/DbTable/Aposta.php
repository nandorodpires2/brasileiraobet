<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Aposta
 *
 * @author Fernando
 */
class Model_DbTable_Aposta extends App_Db_Table_Abstract {

    protected $_name = "aposta";
    protected $_primary = "aposta_id";

    protected function getQueryAll() {
        $select = parent::getQueryAll();
        $select->joinInner(array('partida'), 'aposta.partida_id = partida.partida_id', array('*'));
        $select->joinInner(array('t1' => 'time'), 'partida.time_id_mandante = t1.time_id', array(
            'time_nome_mandante' => 't1.time_nome',
            'time_escudo_mandante' => 't1.time_escudo'
        ));
        $select->joinInner(array('t2' => 'time'), 'partida.time_id_visitante = t2.time_id', array(
            'time_nome_visitante' => 't2.time_nome',
            'time_escudo_visitante' => 't2.time_escudo'
        ));
        $select->joinInner(array('usuario'), 'aposta.usuario_id = usuario.usuario_id', array('*'));

        return $select;
    }

    public function get($usuario_id = null) {
        $select = $this->getQueryAll()
                ->where("aposta_processada = ?", 0)
                ->where("partida.partida_data >= now()");

        if ($usuario_id) {
            $select->where("aposta.usuario_id = ?", $usuario_id);
        }

        $select->order("partida.partida_data asc");
        $select->order("aposta.aposta_data asc");

        return $this->fetchAll($select);
    }

    public function getApostas($partida_id = null, $usuario_id = null, $vencedora = null, $processada = null) {
        $select = $this->getQueryAll();

        if ($partida_id) {
            $select->where("aposta.partida_id = ?", $partida_id);
        }

        if ($usuario_id) {
            $select->where("aposta.usuario_id = ?", $usuario_id);
        }

        if (null !== $vencedora) {
            $select->where("aposta.aposta_vencedora = ?", $vencedora);
            
            // coluna de premiacao
            $select->columns(array(
                'aposta_premio' => new Zend_Db_Expr(" 
                    CASE aposta.aposta_vencedora_premio 
                        WHEN 1 THEN ifnull(partida.partida_valor_premio1 / partida.partida_vencedores_premio1, 0) 
                        WHEN 2 THEN ifnull(partida.partida_valor_premio2 / partida.partida_vencedores_premio2, 0) 
                        WHEN 3 THEN ifnull(partida.partida_valor_premio3 / partida.partida_vencedores_premio3, 0) 
                        ELSE 0
                    END
                ")
            ));
            
        }
        
        if (null !== $processada) {
            $select->where("aposta.aposta_processada = ?", $processada);
        }

        $select->order("partida_data desc");
        
        return $this->fetchAll($select);
    }

    public function getMontante($partida_id) {

        $select = $this->getQueryAll()
                ->columns(array('montante' => new Zend_Db_Expr("sum(partida_valor)")))
                ->where("partida.partida_id = ?", $partida_id);

        $query = $this->fetchRow($select);

        return $query->montante ? $query->montante : 0;
    }

    public function getTotalMontanteApostas() {

        $select = $this->getQueryAll()
                ->columns(array('montante' => new Zend_Db_Expr("sum(partida_valor)")));
        //->where("partida.partida_realizada = ?", 1);

        $query = $this->fetchRow($select);

        return $query->montante ? $query->montante : 0;
    }

    public function getApostasVencedorasByPremio($partida_id, $premio = 1) {
        $select = $this->getQueryAll()
            ->where("partida.partida_id = ?", $partida_id)
            ->where("aposta.aposta_processada = ?", 0);

        switch ($premio) {
            case 1:
                $select->where("partida_placar_mandante = aposta_placar_mandante and partida_placar_visitante = aposta_placar_visitante");
                break;
            case 2:
                $select->where("partida_placar_mandante = aposta_placar_mandante or partida_placar_visitante = aposta_placar_visitante");
                break;
            case 3:
                $select->where("partida.partida_resultado = aposta.aposta_resultado");
                $select->where("partida_placar_mandante <> aposta_placar_mandante and partida_placar_visitante <> aposta_placar_visitante");
                break;
        }

        return $this->fetchAll($select);
    }

    public function getApostasVencedorasParcial($partida_id, $premio = 1, $usuario_id = null) {
        $select = $this->getQueryAll()
                ->joinLeft("parcial", "partida.partida_id = parcial.partida_id", array(
                    'parcial_id',
                    'parcial_placar_mandante',
                    'parcial_placar_visitante',
                    'parcial_vencedores_premio1',
                    'parcial_vencedores_premio2',
                    'parcial_vencedores_premio3',
                    'parcial_atualizacao'
                ))
                ->where("partida.partida_id = ?", $partida_id);
        
        switch ($premio) {
            case 1:
                $select->where("parcial_placar_mandante = aposta_placar_mandante and parcial_placar_visitante = aposta_placar_visitante");
                break;
            case 2:
                $select->where("parcial_placar_mandante = aposta_placar_mandante or parcial_placar_visitante = aposta_placar_visitante");
                break;
            case 3:
                $select->where("1 = 2");
                break;
            default:
                break;
        }
        
        if ($usuario_id) {
            $select->where("aposta.usuario_id = ?", $usuario_id);
        }
                
        return $this->fetchAll($select);
    }
    
    public function getMaioresVencedores() {
        $select = $this->getQueryAll();

        // total das pemiacoes (1º, 2º e 3º pemios)
        $select->columns(array(
            'aposta_total_premio' => new Zend_Db_Expr("
                ifnull(sum(aposta.aposta_vencedora_valor), 0)
            ")
        ));
        
        $select->where("aposta_vencedora = ?", 1);
        
        $select->group("usuario.usuario_id");
        $select->order(" 
            sum(aposta.aposta_vencedora_valor) DESC
        ");
        
        $select->limit(5);
        
        //echo $select->__toString(); die();
        return $this->fetchAll($select);
    }

}
