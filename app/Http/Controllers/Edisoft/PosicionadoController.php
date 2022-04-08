<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers\Edisoft;

/**
 * Description of PosicionadoController
 *
 * @author adupuy
 */
class PosicionadoController extends \App\Http\Controllers\Controller {

    static $campos_obligatorios=["id","agente_aduana","maniobra"];
    public function validar_campos(){
        $vars = array_keys(self::$variables);
        $diff = array_diff(self::$campos_obligatorios,$vars);
        if(count($diff))
            throw new \Exception("Faltan parametros.");
    }
    //put your code here
    public function obtener() {
        $id = self::$variables["id"];
        $rs = \Posiciones::select_posiciones(["id_container" => $id]);
        $respuesta = [];
        $i = 0;
        foreach ($rs as $row) {
            $posicion = new \Posiciones($row);
            $methods = get_class_methods($posicion);
            foreach ($methods as $method) {
//                var_dump($method);
                if ($method !== "get_id" and strstr($method, "get_")) {
                    $respuesta[$i][substr($method, 4, strlen($method) - 4)] = $posicion->$method();
                }
            }
            $container = new \Container($row);
//            $container->get($posicion->get_id_container());
            $methods = get_class_methods($container);
            foreach ($methods as $method) {
//                var_dump($method);
                if ($method !== "get_id" and strstr($method, "get_")) {
                    $respuesta[$i][substr($method, 4, strlen($method) - 4)] = $container->$method();
                }
            }
            $tipocontainer = new \Tipocontainer($row);
//            $tipocontainer->get($container->get_id_tipocontainer());
            $tipoingreso = new \Tipo_ingreso($row);
//            $tipoingreso->get($container->get_id_tipoingreso());
            $cliente = new \Cliente($row);
//            $cliente->get($posicion->get_id_cliente());
            $usuario = new \Usuario($row);
//            $usuario->get($posicion->get_id_usuario());
            $authstat = new \Authstat($row);
//            $authstat->get($posicion->get_id_authstat());
            $respuesta[$i]["tipocontainer"] = $tipocontainer->get_tipo_container();
            $respuesta[$i]["tipoingreso"] = $tipoingreso->get_id_tipo_ingreso();
            $respuesta[$i]["cliente"] = $cliente->get_nombre_completo();
            $respuesta[$i]["usuario"] = $usuario->get_nombre_usuario();
            $respuesta[$i]["authstat"] = $authstat->get_authstat();
            unset($respuesta[$i]["id_usuario"]);
            unset($respuesta[$i]["id_tipoingreso"]);
            unset($respuesta[$i]["id_tipocontainer"]);
            unset($respuesta[$i]["id_authstat"]);
            unset($respuesta[$i]["id_cliente"]);
            $i++;
        }
        return $this->retornar(self::RESPUESTA_CORRECTA, "Encontrados " . $rs->rowCount(), $respuesta);
    }

    public function posicionar_post() {
        $this->validar_campos();
        if (!isset(self::$variables["id"])) {
            throw new \Exception("No hay id");
        }
        $container = new \Container();
        $container->get(self::$variables["id"]);
        $rs = \Posiciones::select(["id_container" => $container->get_id(), "id_authstat" => \Authstat::SALIDA]);
        if ($rs->rowCount() > 0) {
            throw new \Exception("El container ya fue marcado como salida.");
        }

        if (in_array($container->get_id_authstat(), [\Authstat::ENTRADA, \Authstat::ACTIVO])) {
            $posicionado = new \Posiciones();
            $posicionado->set_agente_aduana(self::$variables["agente_aduana"]);
            $posicionado->set_bl($container->get_bl());
            $posicionado->set_id_authstat(\Authstat::ACTIVO);
            $posicionado->set_maniobra(self::$variables["maniobra"]);
            $posicionado->set_id_cliente($container->get_id_cliente());
            $posicionado->set_id_container($container->get_id_container());
            $posicionado->set_id_tipoingreso($container->get_id_tipoingreso());
            $posicionado->set_id_usuario(self::$USUARIO->get_id_usuario());

            if ($posicionado->set()) {
                $id_container = $container->get_id_container();
                $id_posicion = $posicionado->get_id_posicion();
                $response["msg"] = "Maniobra ".$posicionado->get_maniobra()." correctamente";
                $resp = self::RESPUESTA_CORRECTA;
            } else {
                $response["msg"] = "Error al ingresar la posicion ".$posicionado->get_maniobra();
                $resp = self::RESPUESTA_INCORRECTA;
            }
            return $this->retornar($resp, $response["msg"], ["msg" => $response["msg"], "id_container" => $id_container, "id_posicion" => $id_posicion]);
        } else {
            throw new Exception("No se puede mover este container");
        }
    }

}
