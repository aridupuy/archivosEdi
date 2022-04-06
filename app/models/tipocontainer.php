<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of tipocontainer
 *
 * @author adupuy
 */
class Tipocontainer extends Model{
    //put your code here
    public static $id_tabla = "id_tipocontainer";
    public static $prefijo_tabla='ho_';

    private $id_tipocontainer;
    private $tipo_container;
    private $code;
    private $descrip;
    private $teu;
    private $tipo;
    private $cntr_type;
    
    public function get_id_tipocontainer() {
        return $this->id_tipocontainer;
    }

    public function get_tipo_container() {
        return $this->tipo_container;
    }

    public function set_id_tipocontainer($id_tipocontainer) {
        $this->id_tipocontainer = $id_tipocontainer;
        return $this;
    }

    public function set_tipo_container($tipo_container) {
        $this->tipo_container = $tipo_container;
        return $this;
    }
    public function get_code() {
        return $this->code;
    }

    public function get_descrip() {
        return $this->descrip;
    }

    public function get_teu() {
        return $this->teu;
    }

    public function get_tipo() {
        return $this->tipo;
    }

    public function get_cntr_type() {
        return $this->cntr_type;
    }

    public function set_code($code) {
        $this->code = $code;
        return $this;
    }

    public function set_descrip($descrip) {
        $this->descrip = $descrip;
        return $this;
    }

    public function set_teu($teu) {
        $this->teu = $teu;
        return $this;
    }

    public function set_tipo($tipo) {
        $this->tipo = $tipo;
        return $this;
    }

    public function set_cntr_type($cntr_type) {
        $this->cntr_type = $cntr_type;
        return $this;
    }



}
