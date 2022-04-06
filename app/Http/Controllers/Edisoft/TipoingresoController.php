<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers\Edisoft;

/**
 * Description of tipoingresoController
 *
 * @author adupuy
 */
class TipoingresoController extends \App\Http\Controllers\Backoffice\MethaController {

    //put your code here

    public function obtener() {
        return parent::obtener_metha(\Tipo_ingreso::class);
    }

    public function crear_post() {
        return parent::crear_post_metha(\Tipo_ingreso::class);
        
    }

    public function eliminar_post() {
        return parent::eliminar_post_metha(\Tipo_ingreso::class);
    }

}
