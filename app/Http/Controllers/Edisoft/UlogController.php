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
class UlogController extends \App\Http\Controllers\Backoffice\methaController {

    //put your code here

    public function obtener() {
        return parent::obtener_metha(\Ulog::class);
    }


}
