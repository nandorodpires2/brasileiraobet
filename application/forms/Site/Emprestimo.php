<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Emprestimo
 *
 * @author Fernando
 */
class Form_Site_Emprestimo extends App_Forms_Form {
    
    public function init() {
        
        // emprestimo_valor
        $emprestimo_valor = new Zend_Form_Element_Radio("emprestimo_valor");
        $emprestimo_valor->setLabel("Selecione o valor:");
        $emprestimo_valor->setRequired();
        $this->addElement($emprestimo_valor);
        
        // usuario_id
        $usuario_id = new Zend_Form_Element_Hidden("usuario_id");
        $usuario_id->setOrder(10);
        $this->addElement($usuario_id);
        
        // emprestimo_taxa
        $emprestimo_taxa = new Zend_Form_Element_Hidden("emprestimo_taxa");
        $emprestimo_taxa->setOrder(9);
        $this->addElement($emprestimo_taxa);
        
        parent::init();
    }
    
}
