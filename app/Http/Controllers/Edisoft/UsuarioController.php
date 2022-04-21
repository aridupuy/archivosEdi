<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers\Edisoft;

/**
 * Description of UsuarioController
 *
 * @author adupuy
 */
class UsuarioController extends \App\Http\Controllers\Controller {

    //put your code here
    static $campos_obligatorios = ["nombre_usuario", "nombre_completo", "password"];

    //put your code here
    public function obtener() {

        $usuarios = \Usuario::select();

        $respuesta = [];
        foreach ($usuarios as $row) {
//            var_dump($row);
            $usuario = new \Usuario($row);
            $linea["nombre"] = $usuario->get_nombre_completo();
            $linea["id"] = $usuario->get_id();
            $linea["username"] = $usuario->get_nombre_usuario();
            $linea["activo"] = $usuario->get_id_authstat();

            if ($usuario->get_last_login() == null) {
                $linea["ultimo_login"] = "No Login";
            } else {
                $fecha = \DateTime::createFromFormat("Y-m-d H:i:s", $usuario->get_last_login());
                $linea["ultimo_login"] = $fecha->format("Y-m-d H:i:s");
            }
            $respuesta[] = $linea;
        }

        return $this->retornar(self::RESPUESTA_CORRECTA, "Encontrados " . $usuarios->rowCount(), $respuesta);
    }

    public function cambiar_estado_post() {
        $usuario = new \Usuario();
        $usuario->get(self::$variables["id"]);
        if ($usuario->getId() == null) {
            throw new \Exception("No se puede identificar el usuario");
        }
        if ($usuario->get_id() == self::$USUARIO->get_id()) {
            throw new \Exception("No se puede desactivar el usuario logueado.");
        }
        if ($usuario->get_id_authstat() == \Authstat::ACTIVO) {
            $usuario->set_id_authstat(\Authstat::INACTIVO);
        } elseif ($usuario->get_id_authstat() == null OR $usuario->get_id_authstat() == \Authstat::INACTIVO) {
            $usuario->set_id_authstat(\Authstat::ACTIVO);
        }
        if ($usuario->set()) {
            return $this->retornar(self::RESPUESTA_CORRECTA, "", ["id_authstat" => $usuario->get_id_authstat()]);
        }
        return $this->retornar(self::RESPUESTA_INCORRECTA, "Error al cambiar estado", ["resultado" => "not-ok"]);
    }

    public function validar_campos() {
        $vars = array_keys(self::$variables);
        $diff = array_diff(self::$campos_obligatorios, $vars);
        if (count($diff))
            throw new \Exception("Faltan parametros.");
    }

    public function crear_usuario_post() {
        /* No me gusta mezclar controladores ya que son dos capaz iguales, seria mejor pasar la logica a un trait */
        $this->validar_campos();
        $params["nombre_usuario"] = self::$variables["nombre_usuario"];
        $params["nombre_completo"] = self::$variables["nombre_completo"];

        $params["password"] = self::$variables["password"];
        $rs_usuario = \Usuario::select_busqueda_cuenta($params["nombre_usuario"], self::$USUARIO->get_id());
        if ($rs_usuario and $rs_usuario->fetchRow() > 0) {
            throw new \Exception("Ya existe este usuario");
        } else {
            $usuario = new \Usuario();
            $usuario->set_nombre_completo($params["nombre_completo"]);
            $usuario->set_nombre_usuario($params["nombre_usuario"]);
            $usuario->set_password($params["password"]);
            $usuario->set_id_authstat(\Authstat::ACTIVO);
            if ($usuario->set()) {
                $id_usuario = $usuario->getId();
                $response["msg"] = "Usuario generado Correctamente.";
                $resp = self::RESPUESTA_CORRECTA;
            } else {
                $response["msg"] = "Error al generar el usuario";
                $resp = self::RESPUESTA_INCORRECTA;
            }
        }
        return $this->retornar($resp, $response["msg"], ["msg" => $response["msg"], "id_usuario" => $id_usuario]);
    }

    public function obtener_permisos() {

        $rs = \Usuario_menu::select_menu(self::$CUENTA_USUARIO->get_id());
        $respuesta = array();
        foreach ($rs as $row) {
            developer_log(\GuzzleHttp\json_encode($row));
            $elemento = new \Elemento_menu();
            $elemento->get($row["id_elemento_menu"]);
            $fila["nombre"] = $elemento->get_nombre();
            $fila["id"] = $elemento->get_id();
            $fila["grupo"] = $elemento->get_grupo();
            $fila["icono"] = $elemento->get_icono();
            $fila["ruta"] = $elemento->get_ruta();
            $respuesta[] = $fila;
        }
        if (count($respuesta) > 0)
            return $this->retornar(self::RESPUESTA_CORRECTA, "", $respuesta);
        return $this->retornar(self::RESPUESTA_INCORRECTA, "El usuario no tiene permisos", []);
    }

    public function reenviar_url_post() {
//        var_dump(self::$variables);
        return $this->retornar(self::RESPUESTA_CORRECTA, "Correo enviado", $respuesta);
    }

    public function editar_post() {
        $usuario = new \Usuario();
        $usuario->get(self::$variables["id"]);
        if (isset(self::$variables["nombre_completo"]))
            $usuario->set_nombre_completo(self::$variables["nombre_completo"]);
        if (isset(self::$variables["nombre_usuario"]))
            $usuario->set_nombre_usuario(self::$variables["nombre_usuario"]);
        if (isset(self::$variables["password"]))
            $usuario->set_password(self::$variables["password"]);
        if ($usuario->set()) {
            $id_usuario = $usuario->getId();
            $response["msg"] = "Usuario editado Correctamente.";
            $resp = self::RESPUESTA_CORRECTA;
        } else {
            $response["msg"] = "Error al editar el usuario";
            $resp = self::RESPUESTA_INCORRECTA;
        }
        return $this->retornar($resp, $response["msg"], ["msg" => $response["msg"], "id_usuario" => $id_usuario]);
    }

}
