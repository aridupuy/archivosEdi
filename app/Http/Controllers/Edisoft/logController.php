<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers\Edisoft;

/**
 * Description of logController
 *
 * @author adupuy
 */
class logController extends \App\Http\Controllers\Controller{
    //put your code here
    
    public function resetlog(){
        $log_file = "/../../../../my-errors.log";
        
        if(unlink(__DIR__.$log_file)){
            return $this->retornar(self::RESPUESTA_CORRECTA, "Reseteado Correctamente ", []);
        }
        return $this->retornar(self::RESPUESTA_INCORRECTA, "No se puede eliminar", []);
    }
}
