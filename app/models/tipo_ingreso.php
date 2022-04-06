<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of tipo_ingreso
 *
 * @author adupuy
 */
class Tipo_ingreso extends Model{
    //put your code here
    public static $id_tabla = "id_tipo_ingreso";
    public static $prefijo_tabla='ho_';

    private $id_tipo_ingreso;
    private $tipo_ingreso;
    
    public function get_id_tipo_ingreso() {
        return $this->id_tipo_ingreso;
    }

    public function get_tipo_ingreso() {
        return $this->tipo_ingreso;
    }

    public function set_id_tipo_ingreso($id_tipo_ingreso) {
        $this->id_tipo_ingreso = $id_tipo_ingreso;
        return $this;
    }

    public function set_tipo_ingreso($tipo_ingreso) {
        $this->tipo_ingreso = $tipo_ingreso;
        return $this;
    }


}
