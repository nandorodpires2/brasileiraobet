<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Serie
 *
 * @author Fernando
 */
class Zend_View_Helper_Serie extends Zend_View_Helper_Abstract {
    
    public function serie($serie) {
        
        $text = "";
        
        switch ($serie) {
            case 1:                
                $text = "Brasileirão Série A";
                break;
            case 2:                
                $text = "Brasileirão Série B";
                break;
            case 3:                
                $text = "Brasileirão Série C";
                break;
            case 4:                
                $text = "Brasileirão Série D";
                break;
            default:
                break;
        }            
        
        return $text;
        
    }
    
}
