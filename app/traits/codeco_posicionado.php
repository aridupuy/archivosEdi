<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of codeco
 *
 * @author adupuy
 * 
 * Movimiento de contenedores
 * 
 */
class Codeco_posicionado extends \Codeco_salida{

    
    public function nombrar_archivo(){
        $fecha = new DateTime("now");
        return "{$this->container->getIterator()->current()->get_cod_contenedor()}_POSICIONADO_{$fecha->format("Ymdhi")}.edi";
    }
    
}
