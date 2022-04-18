<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of _Encoder
 *
 * @author adupuy
 */
class _encoder extends EDI\Encoder{
        
    protected $output = '';
    private $UNAActive = false;

public function encode(array $array, $wrap = true, $filterKeys = false): string
    {

    
        $this->originalArray = $array;
        $this->wrap = $wrap;

        $edistring = '';
        $count = \count($array);
        $k = 0;
        foreach ($array as $key=>$row) {
            ++$k;
            if ($filterKeys) {
                unset($row['segmentIdx']);
            }
            $row = \array_values($row);
            
            $var = $this->encodeSegment($row);
            if($var!="")
               $edistring .= $var;
            if (!$wrap && $k < $count) {
                $edistring .= "\n";
            }
        }
//        var_dump($edistring);
        $this->output = $edistring;
        return $edistring;
    }    //put your code here
    public function get(): string
    {
        if ($this->UNAActive) {
            $una = 'UNA' . $this->sepComp .
                   $this->sepData .
                   $this->sepDec .
                   $this->symbRel .
                   $this->symbRep .
                   $this->symbEnd;
            if ($this->wrap === false) {
                $una .= "\n";
            }
//            var_dump($this->output);
            return $una . $this->output;
        }
        
        return $this->output;
    }
}
