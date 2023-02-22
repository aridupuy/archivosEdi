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

    const REMITENTE = "SVACJTM";

    //put your code here
    public function generar_edi() {
        $cliente = new \Cliente();
        $cliente->get($this->container->getIterator()->current()->get_id_cliente());
        $posicion = self::$posiciones->current();

        $fecha_recepcion = DateTime::createFromFormat("Ymd Hi", $this->container->getIterator()->current()->get_fecha_recepcion() . " " . $this->container->getIterator()->current()->get_hora_recepcion());
        $oInterchange = (new \EDI\Generator\Interchange(
                        self::REMITENTE, //este es el remitente ver si es fijo o no 
                        $cliente->get_nombre_completo(), //este es el destinatario
                        $fecha_recepcion->format("ymd"), $fecha_recepcion->format("Hi")
                ));
        foreach ($this->container as $container) {
            $tipoContainer = new Tipocontainer();
            $tipoContainer->get($container->get_id_tipocontainer());
            $oCodeco = (new \EDI\Generator\Codeco($container->get_cod_contenedor(), "CODECO", "D", "95B", "UN", "ITG14"));

            $fecha = Datetime::createFromFormat("Y-m-d H:i:s", $container->get_fecha_gen());
            $ie = new Ie();
            $ie->get($container->get_id_ie());
            switch ($ie->get_ie()) {
                case "IMPORTACION":
                    $imex = "3";
                    break;
                case "EXPORTACION":
                    $imex = "2";
                    break;
            }
            !$container->get_sello() ? $ca = "CA" : $ca = "";
            $locode = Locode::obtener_locode($container->get_destino());
            $oContainer = (new \Codeco_container())
                    ->setContainer($container->get_cod_contenedor(),
                            $tipoContainer->get_tipo_container(),
                            $imex,
                            $container->get_eir())
                    ->setEffectiveDate($fecha->format("YmdHi"))
                    ->setSeal($container->get_sello(), $ca) /* Hay quce ver el selloIssuer */
                    ->setLocation($locode !=false?$locode :null) /* setear locode aca */
                    ->setLoc99($locode) /* ver si es viaje el campo que va aca */
                    ->setModeOfTransport("MERCHANT", 3);
            if ((float)$container->get_peso() > 0)
                $oContainer->setWeight($container->get_peso());
            if ($container->get_nota())
                $oContainer->setGoodsDescription($container->get_nota()); /* texto libre */

            $oCodeco = $oCodeco->addContainer($oContainer);
        }
        $oCodeco = $this->compose($oCodeco);
        $aComposed = $oInterchange->addMessage($oCodeco)->getComposed();
        $archivo = $this->generar_archivo((new \_encoder($aComposed, false))->get());
        return $archivo;
    }

    abstract function compose($oCodeco);
}
