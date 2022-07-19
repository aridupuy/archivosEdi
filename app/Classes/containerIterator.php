<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of ContainerIterator
 *
 * @author adupuy
 */
class ContainerIterator implements ArrayIterator{
    //put your code here
   
    public function current(): \Container {
        return parent::current();
    }
}
