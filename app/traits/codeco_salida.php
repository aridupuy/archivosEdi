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
class Codeco_salida extends \Edi{

    
    //put your code here
    public function generar_edi() {
        developer_log("ACA Codeco");
        $cliente = new \Cliente();
        $cliente->get($this->container->get_id_cliente());
        $posicion = self::$posiciones->current();
        $tipoContainer = new Tipocontainer();
        $tipoContainer->get($this->container->get_id_tipocontainer());
        /*validar que es el valor SVACJTM*/
        $oInterchange = (new \EDI\Generator\Interchange("SVACJTM", $cliente->get_nombre_completo(),null,null,$this->container->get_cod_contenedor()));
        $oCodeco = (new \EDI\Generator\Codeco($this->container->get_cod_contenedor()));
        
        
        $fecha = Datetime::createFromFormat("Y-m-d H:i:s", $this->container->get_fecha_gen());
        $ie = new Ie();
        $ie->get($this->container->get_id_ie());
//        var_dump($ie->get_ie());
        switch ($ie->get_ie()){
            case "IMPORTACION":
                $imex = "3";
                break;
            case "EXPORTACION":
                $imex = "2";
                break;
        }
        
        $oContainer = (new \Codeco_container())
                ->setContainer($this->container->get_cod_contenedor(),$tipoContainer->get_tipo_container(), $imex, 4)
                ->setBooking($this->container->get_booking())
                ->setBillOfLading($this->container->get_bl())
                ->setEffectiveDate($fecha->format("YmdHi"))
                ->setSeal($this->container->get_sello(), '') /* Hay que ver el selloIssuer */
                ->setLocation($this->container->get_viaje()) /*ver si es viaje el campo que va aca*/ 
                ->setLoc99($this->container->get_viaje()) /*ver si es viaje el campo que va aca*/ 
//                ->setOrderDescription("lalalal", "lalala")
                ->setModeOfTransport("MERCHANT", 3)
                ->setGoodsDescription($this->container->get_destino())              
        ;       
        if($this->container->get_peso()>0)
            $oCodeco->setWeight($this->container->get_unidad_peso(),$this->container->get_peso());
        if($this->container->get_nota())
            $oCodeco->setGoodsDescription($this->container->get_nota()); /*texto libre*/
           
        $oCodeco = $oCodeco->addContainer($oContainer);
        
        $oCodeco = $oCodeco->compose(9, 36);
        $aComposed = $oInterchange->addMessage($oCodeco)->getComposed();
        
        return $this->generar_archivo((new \_encoder($aComposed, false))->get());
    }
    public function nombrar_archivo(){
        
//        $filename = get_called_class() . "_" . $this->variables["id"] . "_archivo_" . $fecha->format("Ymdhis") . ".edi";
        $cliente = new Cliente();
        $cliente->get($this->container->get_id_cliente());
        $fecha = new DateTime("now");
        return $cliente->get_nombre_completo()."GATE_OUT".$fecha->format("Ymdhi").".edi";
    }
}
