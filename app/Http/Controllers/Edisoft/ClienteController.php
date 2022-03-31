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

    public function obtener() {
        $rs = \Cliente::select(["id_authstat"=> \Authstat::ACTIVO]);
        $respuesta = [];
        $i=0;
        foreach ($rs as $row){
            $respuesta[$i]["id_cliente"]=$row["id_cliente"];
            $respuesta[$i]["nombre_completo"]=$row["nombre_completo"];
            $respuesta[$i]["documento"]=$row["documento"];
            $respuesta[$i]["email"]=$row["email"];
            $respuesta[$i]["telefono"]=$row["telefono"];
            $i++;
        }
        return $this->retornar(self::RESPUESTA_CORRECTA, "Encontrados ".$rs->rowCount(), $respuesta);
    }
    private function validar_entrada($params) {
        $valores = array("documento","email","nombre_completo","telefono");
        foreach ($valores as $valor){
            if(!array_key_exists($valor, $params)){
                return false;
            }
        }
        return true;
    }
    public function crear_post() {
        
        if(!$this->validar_entrada(self::$variables)){
            throw new Exception("Error en los parametros");
        }
        $rs_cliente = \Cliente::select_busqueda_cliente(self::$variables["email"], self::$variables["documento"]);
        if ($rs_cliente and $rs_cliente->fetchRow() > 0) {
            throw new \Exception("Ya existe este usuario");
        }
        
        $cliente = new \Cliente();
        $cliente->set_documento(self::$variables["documento"]);
        $cliente->set_email(self::$variables["email"]);
        $cliente->set_nombre_completo(self::$variables["nombre_completo"]);
        $cliente->set_telefono(self::$variables["telefono"]);
        $cliente->set_id_authstat(\Authstat::ACTIVO);
        if($cliente->set()){
            $id_cliente= $cliente->getId();
            $response["msg"]="Usuario generado Correctamente.";
            $resp = self::RESPUESTA_CORRECTA;
        }
        else{
            $response["msg"]="Error al generar el usuario";
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

}
