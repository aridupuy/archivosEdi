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
//        var_dump($archivo);
        if($archivo){
            $this->container->set_tiene_edi_salida(true);
        }
        if($this->container->set())
            return $archivo;
    }
    public function nombrar_archivo(){
        
//        $filename = get_called_class() . "_" . $this->variables["id"] . "_archivo_" . $fecha->format("Ymdhis") . ".edi";
        $cliente = new Cliente();
        $cliente->get($this->container->get_id_cliente());
        $fecha = new DateTime("now");
        return $cliente->get_nombre_completo()."GATE_OUT".$fecha->format("Ymdhi").".edi";
    }
    public function compose( $oCodeco) {
        return $oCodeco->compose(9,36);
    }
}
