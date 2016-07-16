<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author Fernando
 */
class Model_DbTable_Usuario extends App_Db_Table_Abstract {
    
    protected $_name = "usuario";
    protected $_primary = "usuario_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll();
        
        // qtde de apostas
        $select->columns(array(
            'usuario_apostas' => new Zend_Db_Expr(" 
                (select  count(*)
                from    aposta
                where   usuario.usuario_id = aposta.usuario_id)
            ")
        ));
        
        // total 1 premio
        $select->columns(array(
            'usuario_premios' => new Zend_Db_Expr("(
                select  ifnull(sum(partida.partida_valor_premio1 / partida.partida_vencedores_premio1), 0)
                from    aposta
                        inner join partida on aposta.partida_id = partida.partida_id
                where   usuario.usuario_id = aposta.usuario_id
                        and aposta.aposta_vencedora = 1
            )")
        ));
        
        // saldo atual
        $select->columns(array(
            'usuario_saldo' => new Zend_Db_Expr(" 
                (select  ifnull(sum(lancamento_valor), 0)
                from    lancamento
                where   usuario.usuario_id = lancamento.usuario_id)
            ")
        ));
        
        return $select;
    }

    public function getUsuariosMaquina() {
        $select = $this->getQueryAll()
                ->where("usuario_maquina = ?", 1);
        
        return $this->fetchAll($select);
    }
    
    public function getQuery(array $filtros = null) {
        $select = $this->getQueryAll();
        
        if ($filtros) {
            
        }
        
        return $select;
    }
    
}
