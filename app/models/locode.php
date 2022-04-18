<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of locode
 *
 * @author adupuy
 */
class Locode extends Model {

    //put your code here
    public static $id_tabla = "id_locode";
    public static $prefijo_tabla = "ho_";
    private $id_locode;
    private $locode;
    private $name;
    public function get_id_locode() {
        return $this->id_locode;
    }

    public function get_locode() {
        return $this->locode;
    }

    public function get_name() {
        return $this->name;
    }

    public function set_id_locode($id_locode) {
        $this->id_locode = $id_locode;
        return $this;
    }

    public function set_locode($locode) {
        $this->locode = $locode;
        return $this;
    }

    public function set_name($name) {
        $this->name = $name;
        return $this;
    }


            
}
