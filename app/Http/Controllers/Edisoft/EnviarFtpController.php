<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Edisoft;

use App\Http\Controllers\Controller;

/**
 * Description of EnviarFtpController
 *
 * @author adupuy
 */
class EnviarFtpController extends Controller {
    
    static $campos_obligatorios = array("tipo", "id_container");
    
    public function enviar_post() {
        $this->validar_campos();
        $container = new \Container();
        $container->get(self::$variables["id_container"]);
        $result = false;
        switch (self::$variables["tipo"]){
            case "entrada":
                $result = $this->enviarFTP($container->get_path_edi_entrada());
                break;
            case "salida": 
            case "posicionado":
                $result = $this->enviarFTP($container->get_path_edi_salida());
                break;
        }
        if( $result ) {
            return $this->retornar(self::RESPUESTA_CORRECTA, "Archivo enviado correctamente", ["enviado" => true]);
        }
        return $this->retornar(self::RESPUESTA_INCORRECTA, "Archivo no enviado", ["enviado" => false]);
    }
    
    private function validar_campos() {
        $vars = array_keys(self::$variables);
        $diff = array_diff(self::$campos_obligatorios, $vars);
        if (count($diff))
            throw new \Exception("Faltan parametros.");
    }
    
    private function enviarFTP($file) {
        $ftpController = new \Gestor_de_ftp();
        return $ftpController->enviar_ftp($file);
    }
    
}
