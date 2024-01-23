<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers\Edisoft;

/**
 * Description of LocodeController
 *
 * @author adupuy
 */
class LocodeController extends \App\Http\Controllers\Backoffice\methaController{
    //put your code here
    
    public function obtener() {
        return parent::obtener_metha(\Locode::class);
    }

    public function crear_post() {
        throw new \Exception("No se pueden crear nuevos locode");
    
    }

    public function eliminar_post() {
        throw new \Exception("No se pueden eliminar locodes");
    }
}
