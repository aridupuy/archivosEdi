<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of coparn
 *
 * @author adupuy
 * 
 * Anuncio de contenedores
 * 
 */
class Coparn extends Edi {

    //put your code here
    public function generar_edi() {
        developer_log("ACA Coparn");
        $cliente = new \Cliente();
        $cliente->get($this->container->get_id_cliente());
        $posicion = self::$posiciones->current();
        $tipoContainer = new Tipocontainer();
        $tipoContainer->get($this->container->get_id_tipocontainer());
        $ahora = new DateTime("now");
        $oInterchange = (new \EDI\Generator\Interchange($posicion->get_transportista(), $cliente->get_nombre_completo(),$ahora->format("Ymdhis")));
        $fecha = DateTime::createFromFormat("Y-m-d H:i:s", $this->container->get_fecha_gen());
        $oCoparn = (new \EDI\Generator\Coparn())
                ->setBooking($this->container->get_booking(),$this->container->get_rff_ep())
                ->setRFFOrder($this->container->get_rff_ep())
                ->setETA($fecha->format("YmdHis")) /*dia de llegada*/
                ->setVessel("", "", $this->container->get_buque(),"")
                ->setFND($this->container->get_destino()!=null?$this->container->get_destino():"")
                ->setCarrier('COS')
                ->setContainer($this->container->get_cod_contenedor(), $tipoContainer->get_tipo_container())
                ->setETD("")
                ->setVGM("","")
                ->setPOL("")
                ->setPOD("")
                ->setCargoCategory('GENERAL CARGO')
//                ->set$oCoparn
        ;
        
        $oCoparn->setMessageContent($this->container->get_nota());
        $oCoparn = $oCoparn->compose(5, 126);
//        var_dump($oCoparn);
        $aComposed = $oInterchange->addMessage($oCoparn)->getComposed();
        $content = (new \EDI\Encoder($aComposed, false))->get();
        developer_log("OK Coparn");
        return $this->generar_archivo($content);
    }

}
