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


}
