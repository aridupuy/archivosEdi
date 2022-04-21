<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of codeco
 *
 * @author adupuy
 */
abstract class Codeco extends Edi {

    //put your code here
    public function generar_edi() {
        developer_log("ACA Codeco");
        $cliente = new \Cliente();
        $cliente->get($this->container->get_id_cliente());
        $posicion = self::$posiciones->current();
        $tipoContainer = new Tipocontainer();
        $tipoContainer->get($this->container->get_id_tipocontainer());
        $fecha_recepcion = DateTime::createFromFormat("Ymd Hi",$this->container->get_fecha_recepcion(). " " . $this->container->get_hora_recepcion());
        /* validar que es el valor SVACJTM */
        $oInterchange = (new \EDI\Generator\Interchange(
                "SVACJTM", //este es el remitente ver si es fijo o no 
                $cliente->get_nombre_completo(), //este es el destinatario
                $fecha_recepcion->format("ymd"), $fecha_recepcion->format("Hi"), 
                $this->container->get_cod_contenedor()));
        $oCodeco = (new \EDI\Generator\Codeco($this->container->get_cod_contenedor()));

        $fecha = Datetime::createFromFormat("Y-m-d H:i:s", $this->container->get_fecha_gen());
        $ie = new Ie();
        $ie->get($this->container->get_id_ie());
//        var_dump($ie->get_ie());
        switch ($ie->get_ie()) {
            case "IMPORTACION":
                $imex = "3";
                break;
            case "EXPORTACION":
                $imex = "2";
                break;
        }

        $oContainer = (new \Codeco_container())
                ->setContainer($this->container->get_cod_contenedor(), 
                                $tipoContainer->get_tipo_container(), 
                                $imex, 
                                $this->container->get_eir())
                ->setBooking($this->container->get_booking())
                ->setBillOfLading($this->container->get_bl())
                ->setEffectiveDate($fecha->format("YmdHi"))
                ->setSeal($this->container->get_sello(), '') /* Hay que ver el selloIssuer */
                ->setLocation($this->container->get_destino()) /* setear locode aca*/
//                ->setLoc99($this->container->get_viaje()) /* ver si es viaje el campo que va aca */
//                ->setOrderDescription("lalalal", "lalala")
                ->setModeOfTransport("MERCHANT", 3)
//                ->setGoodsDescription($this->container->get_destino())
        ;
        if ($this->container->get_peso() > 0)
            $oContainer->setWeight($this->container->get_peso());
        if ($this->container->get_nota())
            $oContainer->setGoodsDescription($this->container->get_nota()); /* texto libre */

        $oCodeco = $oCodeco->addContainer($oContainer);

        $oCodeco = $oCodeco->compose(9, 36);
        $aComposed = $oInterchange->addMessage($oCodeco)->getComposed();

        $archivo = $this->generar_archivo((new \_encoder($aComposed, false))->get());
        return $archivo;
    }

}
