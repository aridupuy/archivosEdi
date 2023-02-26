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
        "bl", //bill of landing El Bill of Lading es un documento que sirve como evidencia del contrato de transporte entre el expedidor y la naviera
        "booking", //nota de embarque es el factor clave que se necesita para que una mercancía pueda recibir la orden de ser cargada y exportada. el transportista se compromete a conservar un cierto espacio en el buque para el fletador.
        "id_ie", //1 o 2 importacion exportacion
        "rff_ep",
        "id_tipoingreso",
    );
    static $filtrado = ["fecha_desde", "fecha_hasta", "tiene_edi_entrada", "tiene_edi_salida", "id_estado", "cod_contenedor", "id_tipocontenedor", "tipocontenedor", "id_cliente", "cliente", "destino"];

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

    private function get_registros($tipo) {
        $filtros = $this->set_filtros();
        $ids = isset(self::$variables["ids"]) ? self::$variables["ids"] : null;
        $rs = \Container::select_containers($tipo, $filtros, $ids);
        $respuesta = [];
        $i = 0;
        foreach ($rs as $row) {
            $container = new \Container($row);
            $methods = get_class_methods($container);
            foreach ($methods as $method) {
                if ($method !== "get_id" and strstr($method, "get_")) {
                    $respuesta[$i][substr($method, 4, strlen($method) - 4)] = $container->$method();
                }
            }
            $tipocontainer = new \Tipocontainer($row);
            $tipoingreso = new \Tipo_ingreso($row);
            $cliente = new \Cliente($row);
            $usuario = new \Usuario();
            $usuario->get($row["idusuario"]);
            $authstat = new \Authstat($row);
            $posiciones = new \Posiciones($row);
            
            $ie = new \Ie($row);
            $respuesta[$i]["tipocontainer"] = $tipocontainer->get_tipo_container();
            $respuesta[$i]["tipoingreso"] = $tipoingreso->get_tipo_ingreso();
            $respuesta[$i]["cliente"] = $cliente->get_nombre_completo();
            $respuesta[$i]["usuario"] = $usuario->get_nombre_usuario();
            $respuesta[$i]["authstat"] = $authstat->get_authstat();
            $respuesta[$i]["ie"] = $ie->get_ie();
            $respuesta[$i]["peso"] = $container->get_peso();
            $respuesta[$i]["path_edi_entrada"] = $container->get_path_edi_entrada();
            $respuesta[$i]["path_edi_salida"] = $container->get_path_edi_salida();
            $respuesta[$i]["agente"] = $posiciones->get_agente_aduana();
            $respuesta[$i]["fecha_ultima_maniobra"] = $posiciones->get_fecha_gen();
            unset($respuesta[$i]["id_usuario"]);
            unset($respuesta[$i]["id_tipoingreso"]);
            unset($respuesta[$i]["id_tipocontainer"]);
            unset($respuesta[$i]["id_authstat"]);
            unset($respuesta[$i]["id_cliente"]);
            unset($respuesta[$i]["id_ie"]);
            /*
            if(!$container->get_fecha_gen())
                $container->set_fecha_gen($row["fecha_generacion"]);
            $fecha_recepcion = \DateTime::createFromFormat("Y-m-d H:i:s", $container->get_fecha_recepcion()==null ? $container->get_fecha_gen() : $container->get_fecha_recepcion());
            if(!$fecha_recepcion){
                $fecha_recepcion=\DateTime::createFromFormat("Ymd", $container->get_fecha_recepcion());
            }
            if($fecha_recepcion)
                $hora_recepcion = $container->get_hora_recepcion()!=null ? $container->get_hora_recepcion() : $fecha_recepcion->format("H:i");
            else $hora_recepcion="00:00";
            
            $fechaPosicion ="";
            if($posiciones->get_fecha_gen()){
                $fechaPosicion = \DateTime::createFromFormat("Y-m-d H:i:s", $posiciones->get_fecha_gen())->format("d/m/Y H:i:s");
            }
            $respuesta[$i]["fecha posicionado"] =  $fechaPosicion;
            $UsuarioPosicionado=new \Usuario();
            $UsuarioPosicionado->get($posiciones->get_id_usuario());
            $respuesta[$i]["posicionado Usuario"] = $UsuarioPosicionado->get_nombre_usuario();
           */
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
            $posiciones->set_id_container($container->get_id_container());
            $posiciones->set_id_authstat(\Authstat::ENTRADA);
            $posiciones->set_id_cliente($container->get_id_cliente());
            $posiciones->set_id_usuario(self::$USUARIO->get_id());
            $posiciones->set_id_tipoingreso($container->get_id_tipoingreso());
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
        self::$campos_obligatorios = ["destino",
            "sello",
            "buque",
            "viaje",
            "peso",
            "unidad_peso",
            "agente_aduana"];
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
            $posicion = new \Posiciones();
            $posicion->set_id_authstat(\Authstat::SALIDA);
            $posicion->set_bl(self::$variables["bl"]);
            $posicion->set_id_container($container->get_id_container());
            $posicion->set_id_cliente($container->get_id_cliente());

            $container->set_destino(self::$variables["destino"]);
            $posicion->set_id_tipoingreso($container->get_id_tipoingreso());
            $container->set_eir(self::$variables["eir"] ?: null);
            $posicion->set_maniobra(self::$variables["maniobra"]);
            $container->set_nota(self::$variables["nota"]);
            $container->set_sello(self::$variables["sello"]);
            $container->set_rff_ep(self::$variables["rff_ep"]);
            $container->set_peso(self::$variables["peso"]);
            $container->set_buque(self::$variables["buque"]);
//            $container->set_(self::$variables["buque"]);
            $posicion->set_id_usuario(self::$USUARIO->get_id());
            $posicion->set_agente_aduana(self::$variables["agente_aduana"] ?: "No Proporcionado");
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
        $resultado = $this->get_registros("entrada");
        $fecha = new \DateTime("now");
        $respuesta=[];
        $i=0;
        foreach ($resultado as $row){
            $container = new \Container();
            $container->get($row["id_container"]);
            $tipocontainer = new \Tipocontainer();
            $tipocontainer->get($container->get_id_tipocontainer());
            $cliente = new \Cliente();
            $cliente->get($container->get_id_cliente());
            $usuario = new \Usuario();
            $usuario->get($container->get_id_usuario());
            $authstat = new \Authstat();
            $authstat->get($container->get_id_authstat());
            $tipoingreso = new \Tipo_ingreso();
            $tipoingreso->get($container->get_id_tipoingreso());
            $ie = new \Ie();
            $ie->get($container->get_id_ie());
            
            $fecha_recepcion = $hora_recepcion = "";
            if($row["fecha_recepcion"]!=null )
                $fecha_recepcion = \DateTime::createFromFormat("Ymd", $row["fecha_recepcion"])->format("Y-m-d");
            if($fecha_recepcion==""){
                $fecha_recepcion = \DateTime::createFromFormat("Y-m-d H:i:s", $row["fecha_gen"])->format("Y-m-d");
            }
            if($row["hora_recepcion"]!=null )
                $hora_recepcion = $row["hora_recepcion"];
            if($hora_recepcion=="" ){
                $hora_recepcion = \DateTime::createFromFormat("Y-m-d H:i:s", $row["fecha_gen"])->format("H:i");
            }
            $respuesta[$i]["id"] = $container->get_id_container();
            $respuesta[$i]["Fecha"] = $fecha_recepcion;
            $respuesta[$i]["Hora"] = $hora_recepcion ;
            $respuesta[$i]["Contenedor"] = $container->get_cod_contenedor();
            $respuesta[$i]["Tipo"] = $tipocontainer->get_tipo_container();
            $respuesta[$i]["Cliente"] = $cliente->get_nombre_completo();
            $respuesta[$i]["Usuario"] = $usuario->get_nombre_usuario();
            $respuesta[$i]["Estado"] = $authstat->get_authstat();
            $respuesta[$i]["Tipo Ingreso"] = $tipoingreso->get_tipo_ingreso();
            $respuesta[$i]["Ie"] = $ie->get_ie();
            $respuesta[$i]["peso"] = $container->get_peso();
            $respuesta[$i]["Destino"] = $container->get_destino();
            $respuesta[$i]["Eir"] = $container->get_eir();
            $respuesta[$i]["Nota"] = $container->get_nota();
            $respuesta[$i]["Sello"] = $container->get_sello();
            $respuesta[$i]["Rff_ep"] =  $container->get_rff_ep();
            
            $i++;
        }
        
        switch (self::$variables["tipo"]) {
            case "xls":
                $filename = "Export_entradas" . $fecha->format("Y-m-d_h_i_s") . ".xlsx";
                break;
            case "pdf":
                $filename = "Export_entradas" . $fecha->format("Y-m-d_h_i_s") . ".pdf";
                break;
        }

        return $this->export($filename, $respuesta);
    }

    public function exportar_todos_post() {
        $resultado = $this->get_registros("_all");
        $fecha = new \DateTime("now");
        $respuesta=[];
        $i=0;
        foreach ($resultado as $row){
            $container = new \Container();
            $container->get($row["id_container"]);
            $tipocontainer = new \Tipocontainer();
            $tipocontainer->get($container->get_id_tipocontainer());
            $cliente = new \Cliente();
            $cliente->get($container->get_id_cliente());
            $usuario = new \Usuario();
            $usuario->get($container->get_id_usuario());
            $authstat = new \Authstat();
            $authstat->get($container->get_id_authstat());
            $tipoingreso = new \Tipo_ingreso();
            $tipoingreso->get($container->get_id_tipoingreso());
            $ie = new \Ie();
            $ie->get($container->get_id_ie());
            
            $fecha_recepcion = $hora_recepcion = "";
            if($row["fecha_recepcion"]!=null )
                $fecha_recepcion = \DateTime::createFromFormat("Ymd", $row["fecha_recepcion"])->format("Y-m-d");
            if($fecha_recepcion==""){
                $fecha_recepcion = \DateTime::createFromFormat("Y-m-d H:i:s", $row["fecha_gen"])->format("Y-m-d");
            }
            if($row["hora_recepcion"]!=null )
                $hora_recepcion = $row["hora_recepcion"];
            if($hora_recepcion=="" ){
                $hora_recepcion = \DateTime::createFromFormat("Y-m-d H:i:s", $row["fecha_gen"])->format("H:i");
            }
            $respuesta[$i]["id"] = $container->get_id_container();
            $respuesta[$i]["Fecha"] = $fecha_recepcion;
            $respuesta[$i]["Hora"] = $hora_recepcion ;
            $respuesta[$i]["Contenedor"] = $container->get_cod_contenedor();
            $respuesta[$i]["Tipo"] = $tipocontainer->get_tipo_container();
            $respuesta[$i]["Cliente"] = $cliente->get_nombre_completo();
            $respuesta[$i]["Usuario"] = $usuario->get_nombre_usuario();
            $respuesta[$i]["Estado"] = $authstat->get_authstat();
            $respuesta[$i]["Tipo Ingreso"] = $tipoingreso->get_tipo_ingreso();
            $respuesta[$i]["Ie"] = $ie->get_ie();
            $respuesta[$i]["peso"] = $container->get_peso();
            $respuesta[$i]["Destino"] = $container->get_destino();
            $respuesta[$i]["Eir"] = $container->get_eir();
            $respuesta[$i]["Nota"] = $container->get_nota();
            $respuesta[$i]["Sello"] = $container->get_sello();
            $respuesta[$i]["Rff_ep"] =  $container->get_rff_ep();
            
            $i++;
        }
        switch (self::$variables["tipo"]) {
            case "xls":
                $filename = "Export_all" . $fecha->format("Y-m-d_h_i_s") . ".xlsx";
                break;
            case "pdf":
                $filename = "Export_all" . $fecha->format("Y-m-d_h_i_s") . ".pdf";
                break;
        }

        return $this->export($filename, $respuesta);
    }

    public function exportar_posicionados_post() {
        $posiciones = array();
        $rs = \Container::select_contenedores_posicionados(self::$variables["ids"]);
        foreach ($rs as $row) {
            $container = new \Container($row);            
            $tipocontainer = new \Tipocontainer($row);
            $tipoingreso = new \Tipo_ingreso($row);
            $cliente = new \Cliente($row);
            $usuario = new \Usuario();
            $usuario->get($row["idusuario"]);
            $authstat = new \Authstat($row);
            $pos = new \Posiciones($row);
            $ie = new \Ie($row);
            $fecha_recepcion = $hora_recepcion = "";
            if($row["fecha_recepcion"]!=null ){
                $fecha_recepcion = \DateTime::createFromFormat("Ymd", $row["fecha_recepcion"])->format("Y-m-d");
            }
            if($fecha_recepcion==""){
                $fecha_recepcion = \DateTime::createFromFormat("Y-m-d H:i:s", $row["fecha_gen"])->format("Y-m-d");
            }
            if($row["hora_recepcion"]!=null )
                $hora_recepcion = $row["hora_recepcion"];
            if($hora_recepcion=="" ){
                $hora_recepcion = \DateTime::createFromFormat("Y-m-d H:i:s", $row["fecha_gen"])->format("H:i");
            }
            
            $posicion["id"] = $container->get_id_container();
            $posicion["Fecha"] = $fecha_recepcion;
            $posicion["Hora"] = $hora_recepcion ;
            $posicion["Contenedor"] = $container->get_cod_contenedor();
            $posicion["Tipo"] = $tipocontainer->get_tipo_container();
            $posicion["Cliente"] = $cliente->get_nombre_completo();
            $posicion["Usuario"] = $usuario->get_nombre_usuario();
            $posicion["Estado"] = $authstat->get_authstat();
            $posicion["Tipo Ingreso"] = $tipoingreso->get_tipo_ingreso();
            $posicion["Ie"] = $ie->get_ie();
            $posicion["peso"] = $container->get_peso();
            $posicion["Destino"] = $container->get_destino();
            $posicion["Eir"] = $container->get_eir();
            $posicion["Nota"] = $container->get_nota();
            $posicion["Sello"] = $container->get_sello();
            $posicion["Rff_ep"] =  $container->get_rff_ep();
            $posicion["path_edi_entrada"] = $container->get_path_edi_entrada();
            $posicion["path_edi_salida"] = $container->get_path_edi_salida();
            
            unset($posicion["id_usuario"]);
            unset($posicion["id_tipoingreso"]);
            unset($posicion["id_tipocontainer"]);
            unset($posicion["id_authstat"]);
            unset($posicion["id_cliente"]);
            unset($posicion["id_ie"]);
            
            $posiciones[] = $posicion;
        }
        
        $fecha = new \DateTime("now");
        switch (self::$variables["tipo"]) {
            case "xls":
                $filename = "Export_posicionados" . $fecha->format("Y-m-d_h_i_s") . ".xlsx";
                break;
            case "pdf":
                $filename = "Export_posicionados" . $fecha->format("Y-m-d_h_i_s") . ".pdf";
                break;
        }

        return $this->export($filename, $posiciones);
    }

    public function exportar_salidas_post() {
        $resultado = $this->get_registros("salida");
        $fecha = new \DateTime("now");
        $respuesta=[];
        $i=0;
        foreach ($resultado as $row){
            
            $container = new \Container();
            $container->get($row["id_container"]);
            $tipocontainer = new \Tipocontainer();
            $tipocontainer->get($container->get_id_tipocontainer());
            $cliente = new \Cliente();
            $cliente->get($container->get_id_cliente());
            $usuario = new \Usuario();
            $usuario->get($container->get_id_usuario());
            $authstat = new \Authstat();
            $authstat->get($container->get_id_authstat());
            $tipoingreso = new \Tipo_ingreso();
            $tipoingreso->get($container->get_id_tipoingreso());
            $ie = new \Ie();
            $ie->get($container->get_id_ie());
            
            $fecha_recepcion = $hora_recepcion = "";
            if($row["fecha_recepcion"]!=null )
                $fecha_recepcion = \DateTime::createFromFormat("Ymd", $row["fecha_recepcion"])->format("Y-m-d");
            if($fecha_recepcion=="" and $row["fecha_gen"]!=null){
                $fecha_recepcion = \DateTime::createFromFormat("!Y-m-d H:i:s", $row["fecha_gen"])->format("Y-m-d");
            }
            if($row["hora_recepcion"]!=null )
                $hora_recepcion = $row["hora_recepcion"];
            if($hora_recepcion=="" and $row["fecha_gen"]!=null){
                $hora_recepcion = \DateTime::createFromFormat("Y-m-d H:i:s", $row["fecha_gen"])->format("H:i");
            }
            $respuesta[$i]["id"] = $container->get_id_container();
            $respuesta[$i]["Fecha"] = $fecha_recepcion;
            $respuesta[$i]["Hora"] = $hora_recepcion ;
            $respuesta[$i]["Contenedor"] = $container->get_cod_contenedor();
            $respuesta[$i]["Tipo"] = $tipocontainer->get_tipo_container();
            $respuesta[$i]["Cliente"] = $cliente->get_nombre_completo();
            $respuesta[$i]["Usuario"] = $usuario->get_nombre_usuario();
            $respuesta[$i]["Estado"] = $authstat->get_authstat();
            $respuesta[$i]["Tipo Ingreso"] = $tipoingreso->get_tipo_ingreso();
            $respuesta[$i]["Ie"] = $ie->get_ie();
            $respuesta[$i]["peso"] = $container->get_peso();
            $respuesta[$i]["Destino"] = $container->get_destino();
            $respuesta[$i]["Eir"] = $container->get_eir();
            $respuesta[$i]["Nota"] = $container->get_nota();
            $respuesta[$i]["Sello"] = $container->get_sello();
            $respuesta[$i]["Rff_ep"] =  $container->get_rff_ep();
            
            $i++;
        }
        switch (self::$variables["tipo"]) {
            case "xls":
                $filename = "Export_salidas" . $fecha->format("Y-m-d_h_i_s") . ".xlsx";
                break;
            case "pdf":
                $filename = "Export_salidas" . $fecha->format("Y-m-d_h_i_s") . ".pdf";
                break;
        }
        return $this->export($filename, $respuesta);
    }

}
