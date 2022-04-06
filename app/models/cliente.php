<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of cliente
 *
 * @author adupuy
 */
class Cliente extends Model{
    //put your code here
    public static $id_tabla = "id_cliente";
    private $id_cliente;
    private $nombre_completo;
    private $email;
    private $telefono;
    private $documento;
    private $id_authstat;
    
    
    public function get_id_cliente() {
        return $this->id_cliente;
    }

    public function get_nombre_completo() {
        return $this->nombre_completo;
    }

    public function get_email() {
        return $this->email;
    }

    public function get_telefono() {
        return $this->telefono;
    }

    public function get_documento() {
        return $this->documento;
    }

    public function set_id_cliente($id_cliente) {
        $this->id_cliente = $id_cliente;
        return $this;
    }

    public function set_nombre_completo($nombre_completo) {
        $this->nombre_completo = $nombre_completo;
        return $this;
    }

    public function set_email($email) {
        $this->email = $email;
        return $this;
    }

    public function set_telefono($telefono) {
        $this->telefono = $telefono;
        return $this;
    }

    public function set_documento($documento) {
        $this->documento = $documento;
        return $this;
    }
    public function get_id_authstat() {
        return $this->id_authstat;
    }

    public function set_id_authstat($id_authstat) {
        $this->id_authstat = $id_authstat;
        return $this;
    }
    public function get($id):?bool{
        return parent::get($id);
    }

public static function select_busqueda_cliente($email, $documento){
    $sql = "select * from ed_cliente where email =? or documento =?";
    $vars[]=$email;
    $vars[]=$documento;
    return self::execute_select($sql, $vars);
}
    
    
    
}
