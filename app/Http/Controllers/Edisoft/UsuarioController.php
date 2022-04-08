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
    static $campos_obligatorios=["nombre_usuario","nombre_completo","password"];
    
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
            
            if($usuario->get_last_login()==null){
                $linea["ultimo_login"] = "No Login";
            }
            else{
                $fecha = \DateTime::createFromFormat("Y-m-d H:i:s",$usuario->get_last_login());
                $linea["ultimo_login"] = $fecha->format("Y-m-d H:i:s");
            }
            $respuesta[] = $linea;
        }

        return $this->retornar(self::RESPUESTA_CORRECTA, "Encontrados ".$usuarios->rowCount(), $respuesta);
    }

    public function cambiar_estado_post() {
        $usuario = new \Usuario();
        $usuario->get(self::$variables["id"]);
        if($usuario->getId()==null){
            throw new \Exception("no se puede identificar el usuario");
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

    public function validar_campos(){
        $vars = array_keys(self::$variables);
        $diff = array_diff(self::$campos_obligatorios,$vars);
        if(count($diff))
            throw new \Exception("Faltan parametros.");
    }
    public function crear_usuario_post() {
        /* No me gusta mezclar controladores ya que son dos capaz iguales, seria mejor pasar la logica a un trait */
        $this->validar_campos();
        $params["nombre_usuario"] = self::$variables["nombre_usuario"];
        $params["nombre_completo"] = self::$variables["nombre_completo"];

        $params["password"]=self::$variables["password"];
        $rs_usuario = \Usuario::select_busqueda_cuenta($params["nombre_usuario"], self::$USUARIO->get_id());
        if ($rs_usuario and $rs_usuario->fetchRow() > 0) {
            throw new \Exception("Ya existe este usuario");
        }
        else{
            $usuario = new \Usuario();
            $usuario->set_nombre_completo($params["nombre_completo"]);
            $usuario->set_nombre_usuario($params["nombre_usuario"]);
            $usuario->set_password($params["password"]);
            $usuario->set_id_authstat(\Authstat::ACTIVO);
            if($usuario->set()){
                $id_usuario= $usuario->getId();
                $response["msg"]="Usuario generado Correctamente.";
                $resp = self::RESPUESTA_CORRECTA;
            }
            else{
                $response["msg"]="Error al generar el usuario";
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

    public function obtener_submodulos_post() {
        $rs = \Elemento_menu::select(["id_padre" => self::$variables["id_elemento_menu"]]);
//        $rs = \Elemento_menu::select();

        $respuesta = array();
        foreach ($rs as $row) {
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

    public function obtener_permisos_post() {
//        var_dump(self::$variables);
        $cuenta_usuario = new \Cuenta_usuario();
//        var_dump(self::$variables);

        if (!isset(self::$variables["id"])) {
            throw new \Exception("Falta el parametro id");
        }
        $cuenta_usuario->get(self::$variables["id"]);
        if (!$cuenta_usuario OR $cuenta_usuario->get_id() == null) {
            throw new \Exception("El usuario no esta asignado a la cuenta");
        }
        $rs = \Usuario_menu::select(["id_cuenta_usuario" => $cuenta_usuario->get_id(), "id_authstat" => \Authstat::ACTIVO]);
        $respuesta = array();
        foreach ($rs as $row) {
//            var_dump($row);
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

    public function setear_permisos_post() {
        $cuenta_usuario = new \Cuenta_usuario();
        $cuenta_usuario->get(self::$variables["usuario"]["id"]);
        if ($cuenta_usuario->get_id_usuario() == self::$USUARIO->get_id()) {
            throw new \Exception("No puede editar sus propios permisos");
        }
        $options = self::$variables["options"];
        $submodulos = self::$variables["submodulos"];
        \Model::StartTrans();
        \Usuario_menu::update($cuenta_usuario->get_id());
//        var_dump(self::$variables["permisos"]);
        foreach (self::$variables["permisos"] as $row) {
            $permiso = new \Elemento_menu();
            $permiso->get($row["id_elemento_menu"]);
            $rs = \Usuario_menu::select(["id_cuenta_usuario" => $cuenta_usuario->get_id(), "id_elemento_menu" => $permiso->get_id()]);
            
            if ($options[$row["id_elemento_menu"]] OR $this->in_submodulos($submodulos, $row["id_elemento_menu"])) {
                /* activacion */
                if (!$rs or $rs->rowCount() == 0) {
                    developer_log("no existe");
                    $menu = new \Usuario_menu();
                    $menu->set_id_cuenta_usuario($cuenta_usuario->get_id());
                    $menu->set_id_elemento_menu($permiso->get_id());
                    $menu->set_id_authstat(\Authstat::ACTIVO);
                    if (!$menu->set()) {
                        developer_log("Error al setear");
                        \Model::FailTrans();
                    } else {
                        developer_log("Seteado");
                    }
                } else {
                    $menu = new \Usuario_menu($rs->fetchRow());
                    $menu->set_id_authstat(\Authstat::ACTIVO);
                    if (!$menu->set()) {
                        developer_log("Error al setear");
                        \Model::FailTrans();
                    } else {
                        developer_log("Seteado");
                    }
                }
            } else {
                /* desactivacion */
                if ($rs and $rs->rowCount() > 0) {
                    $r = $rs->fetchRow();
                    $menu = new \Usuario_menu($r);
                    $menu->set_id_authstat(\Authstat::INACTIVO);
                    if (!$menu->set()) {
                        developer_log("Error al setear");
                        \Model::FailTrans();
                    } else {
                        developer_log("Seteado");
                    }
                }
            }
        }
//        return setcookie($GLOBALS['COOKIE_NAME'], null, -1, '/');
        \Gestor_de_cookies::set_cookie("menu", null);
//        developer_log(\Model::HasFailedTrans());
        $usser = new \Usuario();
        $usser ->get($cuenta_usuario->get_id_usuario());
        if (!\Model::HasFailedTrans() and \Model::CompleteTrans()) {
            \Gestor_de_notificaciones::notificar_y_guardar($usser->get_id_cuenta(), "Se efectuaron cambios en tus permisos para el usuario.".$usser->get_nombre_usuario(), "Cambios en tu usuario", "usuarios", 1);
            $view=new \Vista();
            $view->cargar("views/mail_avisos.html");
            $usuario=$view->getElementById("usuario");
            $usuario->appendChild($view->createTextNode(self::$CUENTA->get_titular()));
            $mensaje=$view->getElementById("mensaje");
            $mensaje->appendChild($view->createTextNode("Se efectuaron cambios en tus permisos para el usuario: .".$usser->get_nombre_usuario()));
            $usser = new \Usuario();
            $usser ->get($cuenta_usuario->get_id_usuario());
            return $this->retornar(self::RESPUESTA_CORRECTA, "Permisos seteados", []);
        }
        return $this->retornar(self::RESPUESTA_INCORRECTA, "No se pudo setear el permiso", []);
    }

    private function in_submodulos($submodulos, $id_elemento_menu) {
        $modulos = [];
        foreach ($submodulos as $clave => $row) {
            if ($row != null) {
                foreach ($row as $c => $r)
                    if ($r != null) {
                        $modulos[$c] = $r;
                    }
            }
        }
        foreach ($modulos as $clave => $submodulo) {
            if ($clave == $id_elemento_menu) {
                return true;
            }
        }
        return false;
    }

    public function reenviar_url_post() {
//        var_dump(self::$variables);
        $usuario_cuenta = new \Cuenta_usuario();
        $usuario_cuenta->get(self::$variables["usuario"]["id"]);
        $persona = new \App\Http\Controllers\Backoffice\PersonaJuridicaController();
        $respuesta = $persona->generar_url_post("usuario", $usuario_cuenta->get_id_usuario(), true);
        return $this->retornar(self::RESPUESTA_CORRECTA, "Correo enviado", $respuesta);
    }
}
