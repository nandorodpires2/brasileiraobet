<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LogFile
 *
 * @author Fernando
 */
class Plugin_LogFile {
    
    protected $filename;
    protected $filepath;
    
    protected $file;
    protected $fopen;

    public function __construct($filename, $filepath = null) {
        
        $this->filename = $filename;
        
        if ($filepath) {
            $this->filepath = $filepath;
        } else {
            $this->filepath = APPLICATION_PATH . '/data/log/';   
        }
        
        $this->file = $this->filepath . $this->filename;
     
        $this->create();
        
    }
    
    private function create() {
        $this->fopen = fopen($this->file, "ab");        
    }
    
    public function write($content) {
        fwrite($this->fopen, $content);
    }
    
}
