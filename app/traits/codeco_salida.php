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
class Codeco_salida extends \Codeco{

    
    //put your code here
    public function generar_edi() {
        $archivo = parent::generar_edi();
        if($archivo){
            $this->container->getIterator()->current()->set_tiene_edi_salida(1);
            $this->container->getIterator()->current()->set_path_edi_salida($archivo);
        }
        if($this->container->getIterator()->current()->set())
            return $archivo;
    }
    public function nombrar_archivo(){
        $fecha = new DateTime("now");
        return "{$this->container->getIterator()->current()->get_cod_contenedor()}_GATE_OUT_{$fecha->format("Ymdhi")}.edi";
    }
    public function compose( $oCodeco) {
        return $oCodeco->compose(9,36);
    }
}
