<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of copino
 *
 * @author adupuy
 * 
 * Orden de transporte
 * 
 */
class Copino extends \Coparn {

    //put your code here

    public function generar_edi() {
        developer_log("ACA Copino");
//        $parte = parent::generar_edi();
        $copino = new Copino($this->container, $this->variables);
        $copino->generar_edi();
        $cliente = new \Cliente();
        $cliente->get($this->container->get_id_cliente());
        $posicion = self::$posiciones->current();
        $tipoContainer = new Tipocontainer();
        $tipoContainer->get($this->container->get_id_tipocontainer());
        $oInterchange = (new \EDI\Generator\Interchange($posicion->get_transportista(), $cliente->get_nombre_completo()));
        
        $oCopino = (new \EDI\Generator\Copino())
                ->setSenderAndReceiver($posicion->get_transportista(), $cliente->get_nombre_completo())
                ->setDTM('201204260000')
                ->setTransporter('12000051161000025', 8, '', 'TRUCKER CORP.', 'XA212345', 'JOHN DOE')
                ->setVessel('CARRIER', 'XNOE', $this->container->get_buque())
                ->setContainer('CBHU1234567', '22G1', '4001234567', '1')
                ->setMeasures('G', 11000)
                ->setPort('ITGOA', 'VTE')
                ->setDestination('HKHKG');

        $oCopino = $oCopino->compose(9, 661);

        $aComposed = $oInterchange->addMessage($oCopino)->getComposed();

        return $this->generar_archivo((new \EDI\Encoder($aComposed, false))->get());    }

}
