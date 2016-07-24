<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Deposito
 *
 * @author Fernando
 */
class Model_DbTable_Deposito extends App_Db_Table_Abstract {
    
    protected $_name = "deposito";
    protected $_primary = "deposito_id";
    
    protected function getQueryAll() {
        $select = parent::getQueryAll();
        $select->columns(array(
            'deposito_status' => new Zend_Db_Expr(
                "case deposito_status
                    when 'PAYMENT_CREATED' then 'Aguardando Pagamento'
                    when 'PAYMENT_RECEIVED' then 'Pago'
                    when 'PAYMENT_OVERDUE' then 'Vencido'
                    else 'Gerar Boleto'
                end"
            )
        ));
        return $select;
    }

    public function getMontante($confirmado = 1) {
        $select = $this->getQueryAll()
                ->columns(array('montante' => new Zend_Db_Expr("sum(deposito_valor)")))
                ->where("deposito_confirmado = ?", $confirmado);
        
        $query = $this->fetchRow($select);   
        return $query->montante ? $query->montante : 0;
    }
    
    public function getDepositosUsuario($usuario_id) {
        
        $select = $this->getQueryAll()
                ->where("usuario_id = ?", $usuario_id)
                ->where("deposito_status is not null");
        
        return $this->fetchAll($select);
        
    }
    
}
