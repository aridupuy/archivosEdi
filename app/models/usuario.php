<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of usuario
 *
 * @author adupuy
 */
class Usuario extends Model{
    protected $hash=true;
    protected $actualizar_password=false;
    
    public static $id_tabla="id_usuario";
    private $id_usuario;
    private $nombre_completo;
    private $id_authstat;
    private $nombre_usuario;
    private $password;
    private $email;
//    private $cod_area;
//    private $celular;
    private $last_login;
    
    public function activar_hash(){
           $this->hash=true;
    }
    public function desactivar_hash(){
           $this->hash=false;
    }
    public function get_id_usuario() {
        return $this->id_usuario;
    }

    public function get_nombre_completo() {
        return $this->nombre_completo;
    }

    public function get_id_authstat() {
        return $this->id_authstat;
    }

    public function get_nombre_usuario() {
        return $this->nombre_usuario;
    }

    public function get_password() {
        return $this->password;
    }

    public function get_email() {
        return $this->email;
    }
    public function get_mail() {
        return $this->email;
    }
//
//    public function get_cod_area() {
//        return $this->cod_area;
//    }
//
//    public function get_celular() {
//        return $this->celular;
//    }

    public function set_id_usuario($id_usuario) {
        $this->id_usuario = $id_usuario;
        return $this;
    }

    public function set_nombre_completo($nombre_completo) {
        $this->nombre_completo = $nombre_completo;
        return $this;
    }

    public function set_id_authstat($id_authstat) {
        $this->id_authstat = $id_authstat;
        return $this;
    }

    public function set_nombre_usuario($nombre_usuario) {
        $this->nombre_usuario = $nombre_usuario;
        return $this;
    }

    public function set_password($password) {
        $this->password = $password;
        return $this;
    }

    public function set_email($email) {
        $this->email = $email;
        return $this;
    }
//
//    public function set_cod_area($cod_area) {
//        $this->cod_area = $cod_area;
//        return $this;
//    }
//
//    public function set_celular($celular) {
//        $this->celular = $celular;
//        return $this;
//    }
//    
    public function get_last_login() {
        return $this->last_login;
    }

    public function set_last_login($last_login) {
        $this->last_login = $last_login;
        return $this;
    }

        
    public function set() {
//	developer_log("-------->".get_called_class($this));
//        developer_log($property);
        if ($this->hash and $this->password!=null) {
            if (($this->get_id_usuario() == null or $this->actualizar_password)) {
                $this->password = $this->calcular_passw2($this->password);
            } 
        }
        return parent::set();
    }

    public static function select_login($usuario, $password) {
        $array["usuario"] = strtolower($usuario);
        $array["password"] = $password;
        $array["authstat"] = Authstat::ACTIVO;
        $sql = "select * from ed_usuario where nombre_usuario=? and sha1(?) = password and id_authstat=?";
        
        return self::execute_select($sql, $array);
    }

    public function calcular_passw2($password_sc) {

        $sql = "select sha1( ?) as resultado ";
        $p = array($password_sc);
        $record = self::execute_select($sql, $p);
        if ($record) {
            $row = $record->fetchRow();
            return utf8_decode($row['resultado']);
        }
        return '';
    }

//    public static function select_cel_mail($cel = "", $mail = "") {
//        $sql = "select * from ed_usuario where celular= ? or email= ?";
//        $variables = array($cel, $mail);
//        return self::execute_select($sql, $variables);
//    }

    public function login($usuario, $password) {

        if (is_numeric($usuario) OR strlen($usuario) > 16)
            return false;
        if (is_numeric($password) OR strlen($password) > 32)
            return false;
        $variables = array();

        if (get_called_class() == 'Usuario') {
            $variables['nombre_usuario'] = $usuario;
            $variables['id_authstat'] = Authstat::ACTIVO;
            $variables['password'] = $password;
            $sql = "SELECT id_usuario FROM eb_usuario WHERE nombre_usuario= ? AND id_authstat=? AND sha1(?)=password";
        }
        
        if (!isset($sql))
            return false;

        $recordset = self::execute_select($sql, $variables);
        if (!$recordset)
            return false;

        if ($recordset->RowCount() != 1)
            $resultado = false;
        else {
            $result = $recordset->FetchRow(0);
            $resultado = $result[0];
        }

        return $resultado;
    }

    /* para ser llamada cueando es auth */

    private function calcular_passw($password_sc) {

        $sql = "select sha1(?) as resultado ";
        $p = array($password_sc);
        $record = self::execute_select($sql, $p);
        if ($record) {
            $row = $record->fetchRow();
            return utf8_decode($row['resultado']);
        }
        return '';
    }
    
    public function actualizar_password(){
        $this->actualizar_password=true;
    }
    
    public static function select_busqueda_cuenta($usuario,$id_usuario){
        $sql = "select * from ed_usuario where (nombre_usuario=?  ) and id_usuario!=?";
        $variables[]=$usuario;
        $variables[]=$id_usuario;
        
        return self::execute_select($sql, $variables);
    }
    public static function select_usuarios(){
        $sql = "select * from ed_usuario where id_authstat in (?,?)";
        $variables=[Authstat::ACTIVO, Authstat::INACTIVO];
        return self::execute_select($sql, $variables);
    }
}
