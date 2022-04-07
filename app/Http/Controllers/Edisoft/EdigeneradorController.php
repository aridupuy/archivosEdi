<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers\Edisoft;

use EDI\Encoder;
use EDI\Generator\Desadv;
use EDI\Generator\EdifactException;
use EDI\Generator\Interchange;

/**
 * Description of EdigeneradorController
 *
 * @author adupuy
 */
class EdigeneradorController extends \App\Http\Controllers\Controller {

    //put your code here


    public function generar_archivo_post() {
        $container = new \Container();
        $container->get(self::$variables["id"]);
        $container->get(self::$variables["id"]);
        if(isset(self::$variables["fecha_recepcion"]))
            $container->set_fecha_recepcion(self::$variables["fecha_recepcion"]);
        if(isset(self::$variables["hora_recepcion"]))
            $container->set_hora_recepcion(self::$variables["hora_recepcion"]);
        if(isset(self::$variables["comentario"]))
            $container->set_nota(self::$variables["comentario"]);
        
        $obj = \Edi::factory($container, self::$variables);
//        var_dump($obj);
        if ($obj) {
            if (($url=$obj->generar_edi())) {
                $container->set_tiene_edi(true);
                if ($container->set()) {
                    return $this->retornar(true, "Archivo generado correctamente.",["url"=>$url]);
                }
                return $this->retornar(false, "Error al actualizar registro " . $container->get_id());
            }
            return $this->retornar(false, "Error al generar archivo.");
        }

        return $this->retornar(false, "El contenedor ya tiene edi actualizado.");
    }

}
