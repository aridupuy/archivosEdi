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
        developer_log("ACA Codeco");
        
        /*ojo aca con los clientes*/
        
        $cliente = new \Cliente();
        $cliente->get($this->container->getIterator()->current()->get_id_cliente());
        $posicion = self::$posiciones->current();

        $fecha_recepcion = DateTime::createFromFormat("Ymd Hi", $this->container->getIterator()->current()->get_fecha_recepcion() . " " . $this->container->getIterator()->current()->get_hora_recepcion());
        /* validar que es el valor SVACJTM */
//        $oInterchange = (new \EDI\Generator\Interchange("SVACJTM", $cliente->get_nombre_completo()));
        $oInterchange = (new \EDI\Generator\Interchange(
                        self::REMITENTE, //este es el remitente ver si es fijo o no 
                        $cliente->get_nombre_completo(), //este es el destinatario
                        $fecha_recepcion->format("ymd"), $fecha_recepcion->format("Hi")
//                        $this->container->get_id())
                ));
        foreach ($this->container as $container) {
            $tipoContainer = new Tipocontainer();
            $tipoContainer->get($container->get_id_tipocontainer());
            $oCodeco = (new \EDI\Generator\Codeco($container->get_cod_contenedor(), "CODECO", "D", "95B", "UN", "ITG14"));

            $fecha = Datetime::createFromFormat("Y-m-d H:i:s", $container->get_fecha_gen());
            $ie = new Ie();
            $ie->get($container->get_id_ie());
//        var_dump($ie->get_ie());
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
//                    ->setBooking($container->get_booking())
//                    ->setBillOfLading($container->get_bl())
                    ->setEffectiveDate($fecha->format("YmdHi"))
                    ->setSeal($container->get_sello(), $ca) /* Hay quce ver el selloIssuer */
                    ->setLocation($locode !=false?$locode :null) /* setear locode aca */
                    ->setLoc99($locode) /* ver si es viaje el campo que va aca */
                    /* TDT+1+HS+3' ejemplo */
                    ->setModeOfTransport("MERCHANT", 3);
//        ;
            if ($container->get_peso() > 0)
                $oContainer->setWeight($container->get_peso());
            if ($container->get_nota())
                $oContainer->setGoodsDescription($container->get_nota()); /* texto libre */

            $oCodeco = $oCodeco->addContainer($oContainer);
        }
//        $oCodeco = $oCodeco->compose(9, 36);
        $oCodeco = $this->compose($oCodeco);
        $aComposed = $oInterchange->addMessage($oCodeco)->getComposed();

        /*
          $oCodeco = (new \EDI\Generator\Codeco())
          ->setSenderAndReceiver('SVACJTM', $cliente->get_nombre_completo())
          ->setCarrier('COS')
          ;


          $oContainer = (new \EDI\Generator\Codeco\Container())
          ->setContainer($this->container->get_cod_contenedor(), $tipoContainer->get_tipo_container(), $imex, $this->container->get_eir())
          ->setBooking($this->container->get_booking())
          ->setEffectiveDate($fecha->format("YmdHi"))
          ->setSeal($this->container->get_sello(), '')
          ->setModeOfTransport(3, 31)
          ->setWeight('G', 15400)
          ;

          $oCodeco = $oCodeco->addContainer($oContainer);

          $oCodeco = $oCodeco->compose(5, 34);

          $aComposed = $oInterchange->addMessage($oCodeco)->getComposed();
         */
//echo (new \EDI\Encoder($aComposed, false))->get();



        $archivo = $this->generar_archivo((new \_encoder($aComposed, false))->get());
//        $archivo = $this->generar_archivo((new \EDI\Encoder($aComposed, false))->get());
        return $archivo;
    }

    abstract function compose($oCodeco);
}
