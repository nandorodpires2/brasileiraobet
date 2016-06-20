<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Apostar
 *
 * @author Fernando
 */
class Form_Site_Apostar extends App_Forms_Form {
    
    public function init() {
        
        $action = $this->getView()->url(array(
            'controller' => 'aposta',
            'action' => 'apostar'
        ), null, true);        
        $this->setAction($action);
        $this->setMethod("post");
        
        /**
         * aposta_placar_mandante
         */
        $aposta_placar_mandante = new Zend_Form_Element_Text("aposta_placar_mandante");
        //$aposta_placar_mandante->setLabel("Data: ");
        $aposta_placar_mandante->setRequired();        
        $aposta_placar_mandante->setAttribs(array(
            'class' => 'form-control text-center'
        ));
        $this->addElement($aposta_placar_mandante);
        
        /**
         * aposta_placar_visitante
         */
        $aposta_placar_visitante = new Zend_Form_Element_Text("aposta_placar_visitante");
        //$aposta_placar_visitante->setLabel("Data: ");
        $aposta_placar_visitante->setRequired();
        $aposta_placar_visitante->setAttribs(array(
            'class' => 'form-control text-center'
        ));
        $this->addElement($aposta_placar_visitante);
        
        /**
         * partida_id
         */
        $partida_id = new Zend_Form_Element_Hidden("partida_id");
        $this->addElement($partida_id);
        
        /**
         * usuario_id
         */
        $usuario_id = new Zend_Form_Element_Hidden("usuario_id");
        $this->addElement($usuario_id);
        
        parent::init();
    }
    
}
