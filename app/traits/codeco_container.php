<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of codecoContainer
 *
 * @author adupuy
 */

class Codeco_container extends \EDI\Generator\Codeco\Container{
    //put your code here
    protected $orderDescription;
    protected $effectiveDate;
    protected $cntr;
    protected $destination ;
    protected $loc;
    protected $seal ;
    protected $modeOfTransport;
    protected $weight;
    
    
    public function setLocation($locode) {
        $this->destination = \EDI\Generator\Message::locSegment(165, [$locode, 139, 6]);
        
        return $this;
    }
    public function setLoc99($locode){
        $this->loc= \EDI\Generator\Message::locSegment(99, $locode);
        return $this;
    }
    
    public function setContainer($number, $size, $statusCode, $fullEmptyIndicator)
    {
        $this->cntr = \EDI\Generator\Message::eqdSegment('CN', $number, [$size, '102', '5'], '', $statusCode, $fullEmptyIndicator);
        
        return $this;
    }
    public function setOrderDescription($orderDescription,$reference)
    {
        $this->orderDescription =  \EDI\Generator\Message::addFTXSegment($orderDescription, 'AAI',$reference);
        
        return $this;
    }
    public function setGoodsDescription($description)
    {
        $description = str_split($description, 35);
        $this->orderDescription = ['FTX', 'AAI', '', '', $description];
        return $this;
    }
    public function setEffectiveDate($date = null) {
        if ($date === null) {
            $date = date('YmdHi');
        }
        $this->effectiveDate = \EDI\Generator\Message::dtmSegment(7, $date);

        return $this;
    }
    public function setSeal($seal, $sealIssuer)
    {
        $this->seal = ['SEL', [$seal, $sealIssuer]];

        return $this;
    }
    public function setModeOfTransport($transportMode, $transportMeans) {
        $this->modeOfTransport = \EDI\Generator\Message::tdtShortSegment(1, '', $transportMode, $transportMeans);

        return $this;
    }
    public function setWeight($weight,$type="G") {
        $this->weight = ['MEA', 'WT', $type, ['KGM', $weight]];
        return $this;

    }
    public function compose()
    {
//        var_dump($this->cntr);
        $composed = [$this->cntr];
        if ($this->bkg !== null) {
            $composed[] = $this->bkg;
        }
//        var_dump($this->effectiveDate);
        if ($this->effectiveDate!== null) {
            $composed[] = $this->effectiveDate;
        }
        
        if ($this->destination !== null) {
            $composed[]= $this->destination;
//            foreach ($this->destination as $dest){
//                var_dump($dest);
//                $composed[] = $dest;    
//            }
        }
        if ($this->loc !== null) {
            $composed[]= $this->loc;
//            foreach ($this->destination as $dest){
//                var_dump($dest);
//                $composed[] = $dest;    
//            }
        }
        if ($this->weight !== null) {
            $composed[] = $this->weight;
        }

        if ($this->seal !== null) {
            $composed[] = $this->seal;
        }
        if ($this->modeOfTransport !== null) {
            $composed[] = $this->modeOfTransport;
        }
        if ($this-> orderDescription!== null) {
            $composed[] = $this->orderDescription;
        }
        return $composed;
    }
 
}