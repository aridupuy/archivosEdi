<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers\Backoffice;

/**
 * Description of methaController
 *
 * @author adupuy
 */
class MethaController extends \App\Http\Controllers\Controller{
    //put your code here
    public function obtener_metha($class) {
        $rs = $class::select();
        $respuesta = [];
        $i=0;
        foreach ($rs as $row) {
            $obj = new $class($row);
            $methods = get_class_methods($obj);
            foreach ($methods as $method) {
//                var_dump($method);
                if ($method !== "get_id" and strstr($method, "get_")) {
                    $respuesta[$i][substr($method, 4, strlen($method) - 4)] = $obj->$method();
                }
            }
            $i++;
        }
        return $this->retornar(self::RESPUESTA_CORRECTA, "Encontrados ".$rs->rowCount(), $respuesta);
    }
    public function crear_post_metha($class) {
        $obj= new $class(self::$variables);
//        $obj->set_authstat(self::$variables["authstat"]);
        if ($obj->set()) {
            return $this->retornar(self::RESPUESTA_CORRECTA, "Creado Correctamente ", ["id_authstat"=>$obj->get_id()]);
        }
        return $this->retornar(self::RESPUESTA_INCORRECTA, "No se puede crear");
    }
    public function eliminar_post_metha($class) {
        $obj= new $class();
        $obj->get(self::$variables["id"]);
        if (\Model::delete($obj->get_id(), $obj, $class)) {
            return $this->retornar(self::RESPUESTA_CORRECTA, "Eliminado Correctamente ", ["id"=>$obj->get_id()]);
        }
        return $this->retornar(self::RESPUESTA_INCORRECTA, "No se puede eliminar", ["id"=>$obj->get_id()]);
    }
}
