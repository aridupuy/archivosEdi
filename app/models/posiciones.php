<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of posiciones
 *
 * @author adupuy
 */
class Posiciones extends Model{
    //put your code here
    public static $id_tabla="id_posicion";
    
    private $id_posicion;
    private $id_container;
    private $fecha_gen;
    private $id_usuario;
    private $id_authstat;
    private $agente_aduana;
    private $id_tipoingreso;
    private $bl;
    private $maniobra;
    private $transportista;
    private $id_cliente;
    
    public function get_id_posicion() {
        return $this->id_posicion;
    }

    public function get_id_container() {
        return $this->id_container;
    }

    public function get_fecha_gen() {
        return $this->fecha_gen;
    }

    public function get_id_usuario() {
        return $this->id_usuario;
    }

    public function get_id_authstat() {
        return $this->id_authstat;
    }

    public function get_agente_aduana() {
        return $this->agente_aduana;
    }

    public function get_id_tipoingreso() {
        return $this->id_tipoingreso;
    }

    public function get_bl() {
        return $this->bl;
    }

    public function get_maniobra() {
        return $this->maniobra;
    }

    public function get_transportista() {
        return $this->transportista;
    }

    public function get_id_cliente() {
        return $this->id_cliente;
    }

    public function set_id_posicion($id_posicion) {
        $this->id_posicion = $id_posicion;
        return $this;
    }

    public function set_id_container($id_container) {
        $this->id_container = $id_container;
        return $this;
    }

    public function set_fecha_gen($fecha_gen) {
        $this->fecha_gen = $fecha_gen;
        return $this;
    }

    public function set_id_usuario($id_usuario) {
        $this->id_usuario = $id_usuario;
        return $this;
    }

    public function set_id_authstat($id_authstat) {
        $this->id_authstat = $id_authstat;
        return $this;
    }

    public function set_agente_aduana($agente_aduana) {
        $this->agente_aduana = $agente_aduana;
        return $this;
    }

    public function set_id_tipoingreso($id_tipoingreso) {
        $this->id_tipoingreso = $id_tipoingreso;
        return $this;
    }

    public function set_bl($bl) {
        $this->bl = $bl;
        return $this;
    }

    public function set_maniobra($maniobra) {
        $this->maniobra = $maniobra;
        return $this;
    }

    public function set_transportista($transportista) {
        $this->transportista = $transportista;
        return $this;
    }

    public function set_id_cliente($id_cliente) {
        $this->id_cliente = $id_cliente;
        return $this;
    }

    public static function select_posiciones($ids,$filtros=[]) {
        $where =" true ";
        if($ids!=null || count($ids)>0){
            foreach ($ids as $id){
                $in=$in==""?$in."(?": $in.",?";
                $variables[]=$id;
            }
            $in.=")";
            $where .=" AND A.id_container in $in";    
        }
        if(isset($filtros["fecha_desde"])){
            $where.=" and date(A.fecha_gen)  >=? ";
            $variables[]=$filtros["fecha_desde"];
        }
        if(isset($filtros["fecha_hasta"])){
            $where.=" and date(A.fecha_gen) <=? ";
            $variables[]=$filtros["fecha_hasta"];
        }
        if(isset($filtros["cod_contenedor"])){
            $where.=" and B.cod_contenedor =? ";
            $variables[]=$filtros["cod_contenedor"];
        }
        
        if(isset($filtros["id_cliente"])){
            $where.=" and F.id_cliente =? ";
            $variables[]=$filtros["id_cliente"];
        }
        if(isset($filtros["tipocontenedor"])){
            $where.=" and LOWER(E.tipo_container ) LIKE  LOWER( concat('%' , concat(? , '%' ))) ";
            $variables[]=$filtros["tipocontenedor"];
        }
        if(isset($filtros["cliente"])){
            $where.=" and LOWER(F.nombre_completo) LIKE LOWER( concat('%' , concat(? , '%' ))) ";
            $variables[]=$filtros["cliente"];
        }
        if(isset($filtros["id_tipocontenedor"])){
            $where.=" and B.id_tipocontainer = ? ";
            $variables[]=$filtros["id_tipocontenedor"];
        }
        if(isset($filtros["destino"])){
            $where.=" and LOWER(B.destino) LIKE LOWER( concat('%' , concat(? , '%' )))";
            $variables[]=$filtros["destino"];
        }
        if(isset($filtros["noentrada"])){
            $where.=" and maniobra!=?";
            $variables[]="ENTRADA";
        }
        $sql = "select *,F.nombre_completo nombre_completo from ed_posiciones A "
                . "left join ed_container B on  A.id_container=B.id_container "
                . "left join ho_authstat C on A.id_authstat=C.id_authstat "
                . "left join ho_tipo_ingreso D on B.id_tipoingreso = D.id_tipo_ingreso "
                . "left join ho_tipocontainer E on B.id_tipocontainer = E.id_tipocontainer "
                . "left join ed_cliente F on B.id_cliente= F.id_cliente "
                . "left join ed_usuario G on A.id_usuario= G.id_usuario "
                . " where $where "
                ." order by A.id_posicion desc ";
        var_dump($sql);
        exit();
        return self::execute_select($sql,$variables);
    }
    
    
    
    
}
