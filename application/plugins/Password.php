<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Password
 *
 * @author Fernando
 */
class Plugin_Password {
    
    protected $password;

    public function __construct($password) {
        $this->password = $password;
    }
    
    public function encrypt() {
        $hashPassword = hash("sha512", $this->password);
        for ($i = 0; $i < 64000; $i++) {
            $hashPassword = hash("sha512", $hashPassword);
        }
        
        return $hashPassword;
    }
    
}
