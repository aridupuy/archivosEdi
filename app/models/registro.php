<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of registro
 *
 * @author adupuy
 */
class Registro extends Model{
    //put your code here
    public static $id_tabla = "id_registro";
    private $id_registro;
    private $registro;
    private $fecha_gen;
    private $id_authstat;
    private $id_archivo;
    public function get_id_registro() {
        return $this->id_registro;
    }

    public function get_registro() {
        return $this->registro;
    }

    public function get_fecha_gen() {
        return $this->fecha_gen;
    }

    public function get_id_authstat() {
        return $this->id_authstat;
    }

    public function get_id_archivo() {
        return $this->id_archivo;
    }

    public function set_id_registro($id_registro) {
        $this->id_registro = $id_registro;
        return $this;
    }

    public function set_registro($registro) {
        $this->registro = $registro;
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

    public function set_id_archivo($id_archivo) {
        $this->id_archivo = $id_archivo;
        return $this;
    }


    
}
