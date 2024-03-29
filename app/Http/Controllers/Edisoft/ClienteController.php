<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers\Edisoft;

/**
 * Description of ClienteController
 *
 * @author adupuy
 */
class ClienteController extends \App\Http\Controllers\Controller {

    //put your code here
    static $campos_obligatorios = array("documento", "nombre_completo", "telefono");

    public function validar_campos() {
        $vars = array_keys(self::$variables);
        $diff = array_diff(self::$campos_obligatorios, $vars);
        if (count($diff))
            throw new \Exception("Faltan parametros.");
    }
    public function borrar($id) {
        $cliente = new \Cliente();
        $cliente->get($id);
        
        if($cliente->get_id()==$id and \Cliente::delete($id, $cliente, get_class($cliente))){
            return $this->retornar(self::RESPUESTA_CORRECTA, "Borrados: " . 1, []);
        }
        else{
            return $this->retornar(self::RESPUESTA_INCORRECTA, "Error al eliminar cliente", []);
        }
    }

    public function obtener($id=null) {
        $where = ["id_authstat" => \Authstat::ACTIVO];
        if($id!=null){
            $where["id_cliente"]=$id;
        }
        $rs = \Cliente::select($where);
        $respuesta = [];
        $i = 0;
        foreach ($rs as $row) {
            $respuesta[$i]["id_cliente"] = $row["id_cliente"];
            $respuesta[$i]["nombre_completo"] = $row["nombre_completo"];
            $respuesta[$i]["documento"] = $row["documento"];
            $respuesta[$i]["email"] = $row["email"];
            $respuesta[$i]["telefono"] = $row["telefono"];
            $i++;
        }
        return $this->retornar(self::RESPUESTA_CORRECTA, "Encontrados " . $rs->rowCount(), $respuesta);
    }

    public function obtenertodos() {
        $rs = \Cliente::select();
        $respuesta = [];
        $i = 0;
        foreach ($rs as $row) {
            $respuesta[$i]["id_cliente"] = $row["id_cliente"];
            $respuesta[$i]["nombre_completo"] = $row["nombre_completo"];
            $respuesta[$i]["documento"] = $row["documento"];
            $respuesta[$i]["email"] = $row["email"];
            $respuesta[$i]["telefono"] = $row["telefono"];
            $respuesta[$i]["id_authstat"] = $row["id_authstat"];
            $i++;
        }
        return $this->retornar(self::RESPUESTA_CORRECTA, "Encontrados " . $rs->rowCount(), $respuesta);
    }
    
    public function crear_post() {

        $this->validar_campos();
        $rs_cliente = \Cliente::select_busqueda_cliente(self::$variables["documento"]);
        if ($rs_cliente and $rs_cliente->fetchRow() > 0) {
            throw new \Exception("Ya existe este usuario");
        }

        $cliente = new \Cliente();
        $cliente->set_documento(self::$variables["documento"]);
        if(isset(self::$variables["email"]))
            $cliente->set_email(self::$variables["email"]);
        $cliente->set_nombre_completo(self::$variables["nombre_completo"]);
        $cliente->set_telefono(self::$variables["telefono"]);
        $cliente->set_id_authstat(\Authstat::ACTIVO);
        if ($cliente->set()) {
            $id_cliente = $cliente->getId();
            $response["msg"] = "Usuario generado Correctamente.";
            $resp = self::RESPUESTA_CORRECTA;
        } else {
            $response["msg"] = "Error al generar el usuario";
            $resp = self::RESPUESTA_INCORRECTA;
        }
        return $this->retornar($resp, $response["msg"], ["msg" => $response["msg"], "id_cliente" => $id_cliente]);
    }

    public function cambiar_estado_post() {
        $id = self::$variables["id"];
        $cliente = new \Cliente();
        $cliente->get($id);
        if ($cliente->get_id_cliente() == null) {
            throw new Exception("No existe el cliente");
        }
        if ($cliente->get_id_authstat() == \Authstat::ACTIVO) {
            $cliente->set_id_authstat(\Authstat::INACTIVO);
        } elseif ($cliente->get_id_authstat() == \Authstat::INACTIVO) {
            $cliente->set_id_authstat(\Authstat::ACTIVO);
        }
        if ($cliente->set()) {
            $id_cliente = $cliente->get_id_cliente();
            $response["msg"] = "Estado cambiado correctamente.";
            $resp = self::RESPUESTA_CORRECTA;
        } else {
            $response["msg"] = "No se pudo cambiar el estado";
            $resp = self::RESPUESTA_INCORRECTA;
        }
        return $this->retornar($resp, $response["msg"], ["msg" => $response["msg"], "id_authstat" => $cliente->get_id_authstat()]);
    }

    public function editar_post() {
        $id = self::$variables["id"];
        $cliente = new \Cliente();
        $cliente->get($id);
        if (isset(self::$variables["documento"]))
            $cliente->set_documento(self::$variables["documento"]);
        if (isset(self::$variables["email"]))
            $cliente->set_email(self::$variables["email"]);
        if (isset(self::$variables["nombre_completo"]))
            $cliente->set_nombre_completo(self::$variables["nombre_completo"]);
        if (isset(self::$variables["telefono"]))
            $cliente->set_telefono(self::$variables["telefono"]);

        if ($cliente->set()) {
            $id = $cliente->getId();
            $response["msg"] = "Cliente editado Correctamente.";
            $resp = self::RESPUESTA_CORRECTA;
        } else {
            $response["msg"] = "Error al editar el cliente";
            $resp = self::RESPUESTA_INCORRECTA;
        }
        return $this->retornar($resp, $response["msg"], ["msg" => $response["msg"], "id_usuario" => $id]);
    }
    public function borrar_cliente() {
        $cliente = new \Cliente();
        $cliente->get(self::$variables["id"]);
        if(\Model::delete($cliente->get_id_cliente(), $cliente, \Cliente::class)){
            $resp = self::RESPUESTA_CORRECTA;
            return $this->retornar($resp, "Cliente Borrado", ["msg" => "Usuario Borrado", "id_usuario" => $usuario->get_id_usuario()]);
        } else {
             $resp = self::RESPUESTA_INCORRECTA;
            return $this->retornar($resp, "Error, no se pudo borrar", ["msg" => "Error, no se pudo eliminar", "id_usuario" => $id_usuario]);
        }
    }

}
