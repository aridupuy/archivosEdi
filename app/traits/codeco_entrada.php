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
class Codeco_entrada extends \Codeco{

    
    //put your code here
    public function generar_edi() {
        $archivo = parent::generar_edi();
        
        foreach ($this->container as $container){
            if($archivo){
                $container->set_tiene_edi_entrada(1);
                $container->set_path_edi_entrada($archivo);
            }
            
            if(!$container->set()){
                developer_log("sale mal");
                $false = true;
            }
        }
        if(isset($false))
            return false;
        return $archivo;
    }
    public function nombrar_archivo(){
        $fecha = new DateTime("now");
        return "{$this->container->getIterator()->current()->get_cod_contenedor()}_GATE_IN_{$this->id}_{$fecha->format("Ymdhi")}.edi";
    }
    public function compose($oCodeco) {
        return $oCodeco->compose(9,34);
    }
}
