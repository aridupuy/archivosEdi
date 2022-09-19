<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of authstat
 *
 * @author adupuy
 */
class Authstat extends Model{
    CONST ACTIVO = 1;
    CONST ENTRADA = 2;
    CONST SALIDA = 3;
    CONST INACTIVO = 4;
    CONST BORRADO = 5;
    
    public static $id_tabla="id_authstat";
    public static $prefijo_tabla="ho_";
    private $id_authstat;
    private $authstat;
    public function get_id_authstat() {
        return $this->id_authstat;
    }

    public function get_authstat() {
        return $this->authstat;
    }

    public function set_id_authstat($id_authstat) {
        $this->id_authstat = $id_authstat;
        return $this;
    }

    public function set_authstat($authstat) {
        $this->authstat = $authstat;
        return $this;
    }



}
