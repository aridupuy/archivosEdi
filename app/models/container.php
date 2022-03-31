<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of container
 *
 * @author adupuy
 */
class Container extends Model{

    //put your code here
    public static $id_tabla = "id_container";
    private $id_container;
    private $fecha_gen;
    private $cod_contenedor;
    private $eir;
    private $id_tipocontainer;
    private $id_cliente;
    private $id_authstat;
    private $id_usuario;
    private $bl;
    private $booking;
    private $buque;
    private $nota;
    private $viaje;
    private $sello;
    private $destino;
    private $id_ie;
    private $rff_ep;
    private $id_tipoingreso;
    
    
    public function get_id_container() {
        return $this->id_container;
    }

    public function get_fecha_gen() {
        return $this->fecha_gen;
    }

    public function get_cod_contenedor() {
        return $this->cod_contenedor;
    }

    public function get_eir() {
        return $this->eir;
    }

    public function get_id_tipocontainer() {
        return $this->id_tipocontainer;
    }

    public function get_id_cliente() {
        return $this->id_cliente;
    }

    public function get_id_authstat() {
        return $this->id_authstat;
    }

    public function get_id_usuario() {
        return $this->id_usuario;
    }

    public function get_bl() {
        return $this->bl;
    }

    public function get_booking() {
        return $this->booking;
    }

    public function get_buque() {
        return $this->buque;
    }

    public function get_nota() {
        return $this->nota;
    }

    public function get_viaje() {
        return $this->viaje;
    }

    public function get_sello() {
        return $this->sello;
    }

    public function get_destino() {
        return $this->destino;
    }

    public function get_id_ie() {
        return $this->id_ie;
    }

    public function get_rff_ep() {
        return $this->rff_ep;
    }

    public function get_id_tipoingreso() {
        return $this->id_tipoingreso;
    }

    public function set_id_container($id_container) {
        $this->id_container = $id_container;
        return $this;
    }

    public function set_fecha_gen($fecha_gen) {
        $this->fecha_gen = $fecha_gen;
        return $this;
    }

    public function set_cod_contenedor($cod_contenedor) {
        $this->cod_contenedor = $cod_contenedor;
        return $this;
    }

    public function set_eir($eir) {
        $this->eir = $eir;
        return $this;
    }

    public function set_id_tipocontainer($id_tipocontainer) {
        $this->id_tipocontainer = $id_tipocontainer;
        return $this;
    }

    public function set_id_cliente($id_cliente) {
        $this->id_cliente = $id_cliente;
        return $this;
    }

    public function set_id_authstat($id_authstat) {
        $this->id_authstat = $id_authstat;
        return $this;
    }

    public function set_id_usuario($id_usuario) {
        $this->id_usuario = $id_usuario;
        return $this;
    }

    public function set_bl($bl) {
        $this->bl = $bl;
        return $this;
    }

    public function set_booking($booking) {
        $this->booking = $booking;
        return $this;
    }

    public function set_buque($buque) {
        $this->buque = $buque;
        return $this;
    }

    public function set_nota($nota) {
        $this->nota = $nota;
        return $this;
    }

    public function set_viaje($viaje) {
        $this->viaje = $viaje;
        return $this;
    }

    public function set_sello($sello) {
        $this->sello = $sello;
        return $this;
    }

    public function set_destino($destino) {
        $this->destino = $destino;
        return $this;
    }

    public function set_id_ie($id_ie) {
        $this->id_ie = $id_ie;
        return $this;
    }

    public function set_rff_ep($rff_ep) {
        $this->rff_ep = $rff_ep;
        return $this;
    }

    public function set_id_tipoingreso($id_tipoingreso) {
        $this->id_tipoingreso = $id_tipoingreso;
        return $this;
    }



}
