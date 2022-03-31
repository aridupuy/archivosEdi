<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of ed_archivo
 *
 * @author adupuy
 */
class Archivo extends Model{
    //put your code here
    public static $id_tabla = "id_archivo";
    private $id_archivo;
    private $header;
    private $fecha_gen;
    private $id_authstat;
    private $id_usuario;
    
    public function get_id_archivo() {
        return $this->id_archivo;
    }

    public function get_header() {
        return $this->header;
    }

    public function get_fecha_gen() {
        return $this->fecha_gen;
    }

    public function get_id_authstat() {
        return $this->id_authstat;
    }

    public function get_id_usuario() {
        return $this->id_usuario;
    }

    public function set_id_archivo($id_archivo) {
        $this->id_archivo = $id_archivo;
        return $this;
    }

    public function set_header($header) {
        $this->header = $header;
        return $this;
    }

    public function set_fecha_gen($fecha_gen) {
        $this->fecha_gen = $fecha_gen;
        return $this;
    }

    public function set_id_authstat($id_authstat) {
        $this->id_authstat = $id_authstat;
        return $this;
    }

    public function set_id_usuario($id_usuario) {
        $this->id_usuario = $id_usuario;
        return $this;
    }


    
}
