<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers\Edisoft;

/**
 * Description of ContainerController
 *
 * @author adupuy
 */
class ContainerController extends \App\Http\Controllers\Controller {

    //put your code here
    static $campos_obligatorios = array(
        "cod_contenedor",
        "eir",
        "id_tipocontainer",
        "id_cliente",
//        "bl",
//        "booking",
//        "buque",
//        "viaje",
//        "sello",
//        "destino",
        "id_ie",
        "rff_ep",
        "id_tipoingreso",
//        "peso",
    );
    static $filtrado =["fecha_desde","fecha_hasta","tiene_edi_entrada","tiene_edi_salida" ,"id_estado","cod_contenedor", "id_tipocontenedor","tipocontenedor", "id_cliente","cliente","destino"];
    public function validar_campos() {
        $vars = array_keys(self::$variables);
        $diff = array_diff(self::$campos_obligatorios, $vars);
        if (count($diff))
            throw new \Exception("Faltan parametros.");
    }
    public function obtener_registros_post() {
        $respuesta = $this->get_registros("todos");
        return $this->retornar(self::RESPUESTA_CORRECTA, "Encontrados " . count($respuesta), $respuesta);
    }
    
    public function obtener_entradas() {
        $respuesta = $this->get_registros("entrada");
        return $this->retornar(self::RESPUESTA_CORRECTA, "Encontrados " . count($respuesta), $respuesta);
    }
    public function obtener_entradas_post() {
        return $this->obtener_entradas();
    }
    

    public function obtener_salidas() {
        $respuesta = $this->get_registros("salida");
        return $this->retornar(self::RESPUESTA_CORRECTA, "Encontrados " . count($respuesta), $respuesta);
    }
     public function obtener_salidas_post() {
         
        return $this->obtener_salidas();
    }
    
    private function get_registros($tipo){
        $filtros=$this->set_filtros();
        $rs = \Container::select_containers($tipo,$filtros);
        $respuesta = [];
        $i = 0;
        foreach ($rs as $row) {
            $container = new \Container($row);
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
            $ie = new \Ie($row);
//            $authstat->get($posicion->get_id_authstat());
            $respuesta[$i]["tipocontainer"] = $tipocontainer->get_tipo_container();
            $respuesta[$i]["tipoingreso"] = $tipoingreso->get_id_tipo_ingreso();
            $respuesta[$i]["cliente"] = $cliente->get_nombre_completo();
            $respuesta[$i]["usuario"] = $usuario->get_nombre_usuario();
            $respuesta[$i]["authstat"] = $authstat->get_authstat();
            $respuesta[$i]["ie"] = $ie->get_ie();
            $respuesta[$i]["peso"] = $container->get_peso();
//            $respuesta[$i]["unidad_peso"]=$container->get_unidad_peso();
            unset($respuesta[$i]["id_usuario"]);
            unset($respuesta[$i]["id_tipoingreso"]);
            unset($respuesta[$i]["id_tipocontainer"]);
            unset($respuesta[$i]["id_authstat"]);
            unset($respuesta[$i]["id_cliente"]);
            unset($respuesta[$i]["id_ie"]);
            $i++;
        }
        return $respuesta;
    }

    public function entrada_post() {
        $this->validar_campos();

        \Model::StartTrans();
        $container = new \Container();
        foreach (self::$variables as $key => $val) {
            $set = "set_" . $key;
            if (method_exists($container, $set))
                $container->$set($val);
        }
        $container->set_id_authstat(\Authstat::ENTRADA);
        $container->set_id_usuario(self::$USUARIO->get_id_usuario());
        if (!$container->set()) {
            \Model::FailTrans();
        }
        if (!\Model::HasFailedTrans()) {
            $posiciones = new \Posiciones();
//            $posiciones->set_agente_aduana(self::$variables["agente_aduana"]);
            $posiciones->set_id_container($container->get_id_container());
            $posiciones->set_id_authstat(\Authstat::ENTRADA);
            $posiciones->set_id_cliente($container->get_id_cliente());
            $posiciones->set_id_usuario(self::$USUARIO->get_id());
            $posiciones->set_id_tipoingreso($container->get_id_tipoingreso());
//            $posiciones->set_bl($container->get_bl());
            $posiciones->set_maniobra("ENTRADA");
            
            if ($posiciones->set()) {
                \Model::CompleteTrans();
                $id_container = $container->get_id_container();
                $id_posicion = $posiciones->get_id_posicion();
                $response["msg"] = "Container ingresado correctamente.";
                $resp = self::RESPUESTA_CORRECTA;
            } else {
                $response["msg"] = "Error al ingresar la posicion de entrada";
                $resp = self::RESPUESTA_INCORRECTA;
            }
            return $this->retornar($resp, $response["msg"], ["msg" => $response["msg"], "id_container" => $id_container, "id_posicion" => $id_posicion]);
        } else {
            $response["msg"] = "Error al ingresar el container";
            $resp = self::RESPUESTA_INCORRECTA;
            return $this->retornar($resp, $response["msg"], ["msg" => $response["msg"]]);
        }
    }

    protected function validar_entrada($params) {
        $vars = [
            "cod_contenedor",
            "eir",
            "agente_aduana",
            "id_tipocontainer",
            "id_cliente",
            "id_authstat",
            "id_usuario",
            "bl",
            "booking",
            "buque",
            "nota",
            "viaje",
            "sello",
            "destino",
            "id_ie",
            "rff_ep",
            "id_tipoingreso",
            "peso",
            "unidad_peso"
        ];
        foreach ($vars as $valor) {
            if (!array_key_exists($valor, $params)) {
                return false;
            }
        }
        return true;
    }

    public function cambiar_estado_post() {
        $id = self::$variables["id"];
        $container = new \Container();
        $container->get($id);
        if ($container->get_id_cliente() == null) {
            throw new \Exception("No existe el container ");
        }
        if (in_array($container->get_id_authstat(), [\Authstat::ACTIVO, \Authstat::ENTRADA])) {
            $container->set_id_authstat(\Authstat::INACTIVO);
        } else {
            throw new \Exception("No se puede modificar este container");
        }
        if ($container->set()) {
            $id_container = $container->get_id_container();
            $response["msg"] = "Estado cambiado correctamente.";
            $resp = self::RESPUESTA_CORRECTA;
        } else {
            $response["msg"] = "No se pudo cambiar el estado";
            $resp = self::RESPUESTA_INCORRECTA;
        }
        return $this->retornar($resp, $response["msg"], ["msg" => $response["msg"], "id_authstat" => $container->get_id_authstat()]);
    }

    public function salida_post() {
//        $this->validar_campos();
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
            $posicion = new \Posiciones();
            $posicion->set_id_authstat(\Authstat::SALIDA);
            $posicion->set_bl(self::$variables["bl"]);
            $posicion->set_id_container($container->get_id_container());
            $posicion->set_id_cliente($container->get_id_cliente());
            $container->set_destino(self::$variables["destino"]);
            $posicion->set_id_tipoingreso($container->get_id_tipoingreso());
            $container->set_eir(self::$variables["eir"]);
            $posicion->set_maniobra(self::$variables["maniobra"]);
            $container->set_nota(self::$variables["nota"]);
            $container->set_sello(self::$variables["sello"]);
            $container->set_rff_ep(self::$variables["rff_ep"]);
            $posicion->set_id_usuario(self::$USUARIO->get_id());
            $posicion->set_agente_aduana(self::$variables["agente_aduana"]);
            $posicion->set_maniobra("SALIDA");
            $container->set_id_authstat(\Authstat::SALIDA);
            if ($posicion->set() and $container->set()) {
                $id_container = $container->get_id_container();
                $id_posicion = $posicion->get_id_posicion();
                $response["msg"] = "Container movido correctamente a salida.";
                $resp = self::RESPUESTA_CORRECTA;
            } else {
                $response["msg"] = "Error al ingresar la posicion de salida";
                $resp = self::RESPUESTA_INCORRECTA;
            }
            return $this->retornar($resp, $response["msg"], ["msg" => $response["msg"], "id_container" => $id_container, "id_posicion" => $id_posicion]);
        } else {
            throw new \Exception("No se puede mover este container");
        }
    }

    public function exportar_entradas_post() {
        $resultado = $this->get_entradas();
        $fecha = new \DateTime("now");
        switch (self::$variables["tipo"]) {
            case "xls":
                $filename = "Export_entradas" . $fecha->format("Y-m-d_h_i_s") . ".xlsx";
                break;
            case "pdf":
                $filename = "Export_entradas" . $fecha->format("Y-m-d_h_i_s") . ".pdf";
                break;
        }
        
        return $this->export($filename, $resultado);
    }
    
        
    public function exportar_salidas_post() {
        $resultado = $this->get_registros("salida");
        $fecha = new \DateTime("now");
        switch (self::$variables["tipo"]) {
            case "xls":
                $filename = "Export_salidas" . $fecha->format("Y-m-d_h_i_s") . ".xlsx";
                break;
            case "pdf":
                $filename = "Export_salidas" . $fecha->format("Y-m-d_h_i_s") . ".pdf";
                break;
        }
        return $this->export($filename, $resultado);
    }
    

}
