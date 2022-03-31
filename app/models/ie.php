<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of ie
 *
 * @author adupuy
 */
class Ie extends Model{
    public static $id_tabla="id_ie";
    public static $prefijo_tabla='ho_';

    private $id_ie;
    private $ie;
    public function get_id_ie() {
        return $this->id_ie;
    }

    public function get_ie() {
        return $this->ie;
    }

    public function set_id_ie($id_ie) {
        $this->id_ie = $id_ie;
        return $this;
    }

    public function set_ie($ie) {
        $this->ie = $ie;
        return $this;
    }


}
