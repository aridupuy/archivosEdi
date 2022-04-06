<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of ArrayPosiciones
 *
 * @author adupuy
 */
class Array_posiciones implements Iterator{
    private $posiciones;
    private $i=0;
    public function current(): ?\Posiciones {
        
        return $this->posiciones[$this->i-1];
    }

    public function key(): int {
        return $this->i;
    }

    public function next(): void {
        $this->i++;
        
    }

    public function rewind(): void {
        $this->i--;
    }

    public function valid(): bool {
        return isset($this->posiciones[$this->i]);
    }
    public function add(Posiciones $posicion){
        $this->posiciones[$this->i]=$posicion;
        $this->next();
    }
    public function pop(): \Posiciones{
        $this->rewind();
        return $this->posiciones[$this->i+1];
    }
    public function get_at($key): ?\Posiciones{
        return $this->posiciones[$key-1];
    }
    public function destroy(){
        $this->posiciones=[];
        $this->i=0;
    }
    public function get_all() {
        return $this->posiciones;
    }
}
