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
class Container extends Model {

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
    private $tiene_edi_entrada;
    private $tiene_edi_salida;
    private $peso;
    private $fecha_recepcion;
    private $hora_recepcion;
    private $path_edi_entrada;
    private $path_edi_salida;

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

    public function get_tiene_edi_entrada() {
        return $this->tiene_edi_entrada;
    }

    public function set_tiene_edi_entrada($tiene_edi) {
        $this->tiene_edi_entrada = $tiene_edi;
        return $this;
    }

    public function get_tiene_edi_salida() {
        return $this->tiene_edi_salida;
    }

    public function set_tiene_edi_salida($tiene_edi_salida) {
        $this->tiene_edi_salida = $tiene_edi_salida;
        return $this;
    }

    public function get_peso() {
        return $this->peso;
    }

    public function set_peso($peso) {
        $this->peso = $peso;
        return $this;
    }

    public function get_fecha_recepcion() {
        return $this->fecha_recepcion;
    }

    public function get_hora_recepcion() {
        return $this->hora_recepcion;
    }

    public function set_fecha_recepcion($fecha_recepcion) {
        $this->fecha_recepcion = $fecha_recepcion;
        return $this;
    }

    public function set_hora_recepcion($hora_recepcion) {
        $this->hora_recepcion = $hora_recepcion;
        return $this;
    }

    public function get_path_edi_entrada() {
        return $this->path_edi_entrada;
    }

    public function get_path_edi_salida() {
        return $this->path_edi_salida;
    }

    public function set_path_edi_entrada($path_edi_entrada) {
        $this->path_edi_entrada = $path_edi_entrada;
        return $this;
    }

    public function set_path_edi_salida($path_edi_salida) {
        $this->path_edi_salida = $path_edi_salida;
        return $this;
    }

    public static function select_containers($tipo, $filtros = [], $ids = null) {
        $array = ["entrada" => \Authstat::ENTRADA, "salida" => \Authstat::SALIDA, "posicionado" => \Authstat::POSICIONADO, "_all" => ""];
        $where = " TRUE ";
        if (isset($array[$tipo]) and $tipo != "_all") {
            $tipo = $array[$tipo];
            $where .= " and A.id_authstat in (?) ";
            $variables = [$tipo];
        } else if (isset($filtros["id_estado"])) {
            $where .= " and A.id_authstat in (?)  ";
            $variables[] = $filtros["id_estado"];
        }
        if (isset($filtros["tipoingreso"])) {
            $where .= " and C.id_tipo_ingreso = ?  ";
            $variables[] = $filtros["tipoingreso"];
        }
        if (isset($filtros["tiene_edi_entrada"])) {
            $where .= " and A.tiene_edi_entrada = ?";
            $variables[] = $filtros["tiene_edi_entrada"];
        }
        if (isset($filtros["tiene_edi_salida"])) {
            $where .= " and A.tiene_edi_salida= ?";
            $variables[] = $filtros["tiene_edi_salida"];
        }
        if (isset($filtros["fecha_desde"])) {
            $where .= " and date(A.fecha_gen)  >=? ";
            $variables[] = $filtros["fecha_desde"];
        }
        if (isset($filtros["fecha_hasta"])) {
            $where .= " and date(A.fecha_gen) <=? ";
            $variables[] = $filtros["fecha_hasta"];
        }
        if (isset($filtros["cod_contenedor"])) {
            $where .= " and A.cod_contenedor =? ";
            $variables[] = $filtros["cod_contenedor"];
        }

        if (isset($filtros["id_cliente"])) {
            $where .= " and E.id_cliente =? ";
            $variables[] = $filtros["id_cliente"];
        }
        if (isset($filtros["tipocontenedor"])) {
            $where .= " and LOWER(D.tipo_container ) LIKE  LOWER( concat('%' , concat(? , '%' ))) ";
            $variables[] = $filtros["tipocontenedor"];
        }
        if (isset($filtros["cliente"])) {
            $where .= " and LOWER(F.nombre_completo) LIKE LOWER( concat('%' , concat(? , '%' )))";
            $variables[] = $filtros["cliente"];
        }
        if (isset($filtros["id_tipocontenedor"])) {
            $where .= " and D.id_tipocontainer = ? ";
            $variables[] = $filtros["id_tipocontenedor"];
        }
        if (isset($filtros["destino"])) {
            $where .= " and LOWER(A.destino) LIKE LOWER( concat('%' , concat(? , '%' )))";
            $variables[] = $filtros["destino"];
        }

        if ($ids != null) {
            if ($ids != null || count($ids) > 0) {
                foreach ($ids as $id) {
                    $in = $in == "" ? $in . "(?" : $in . ",?";
                    $variables[] = $id;
                }
                $in .= ")";
                $where .= " AND A.id_container in $in";
            }
        }

        $sql = "select *,
                    A.fecha_gen as fecha_generacion,
                    F.fecha_gen as fecha_usuario  ,
                    H.*,F.id_usuario as idusuario,
                    E.id_cliente as idCliente
                    
                    "
                . "from ed_container A "
                . "left join ho_authstat B on A.id_authstat=B.id_authstat "
                . "left join ho_tipo_ingreso C on A.id_tipoingreso = C.id_tipo_ingreso "
                . "left join ho_tipocontainer D on A.id_tipocontainer = D.id_tipocontainer "
                . "left join ed_cliente E on A.id_cliente= E.id_cliente "
                . "left join ed_usuario F on A.id_usuario= F.id_usuario "
                . "left join ho_ie G on A.id_ie= G.id_ie "
                . "right join ed_posiciones H on A.id_container= H.id_container and H.id_authstat=A.id_authstat "
                . "where $where order by 1 desc";
        return self::execute_select($sql, $variables);
    }

    public static function select_containers_entrada() {
        $sql = "select * from ed_container where id_authstat in (?,?)";
        $variables = [Authstat::ACTIVO, Authstat::ENTRADA];
        return self::execute_select($sql, $variables);
    }

    public static function select_containers_salida() {
        $sql = "select * from ed_container where id_authstat in (?)";
        $variables = [Authstat::SALIDA];
        return self::execute_select($sql, $variables);
    }

    public static function select_contenedores_posicionados($ids, $filtros=false) {
        $where = " A.id_authstat=?";
        $variables[] = Authstat::POSICIONADO;

        if ($ids != null || count($ids) > 0) {
            foreach ($ids as $id) {
                $in = $in == "" ? $in . "(?" : $in . ",?";
                $variables[] = $id;
            }
            $in .= ")";
            $where .= " AND A.id_container in $in";
        }
        if (isset($filtros["fecha_desde"])) {
            $where .= " and date(A.fecha_gen)  >=? ";
            $variables[] = $filtros["fecha_desde"];
        }
        if (isset($filtros["fecha_hasta"])) {
            $where .= " and date(A.fecha_gen) <=? ";
            $variables[] = $filtros["fecha_hasta"];
        }
        if (isset($filtros["cod_contenedor"])) {
            $where .= " and B.cod_contenedor =? ";
            $variables[] = $filtros["cod_contenedor"];
        }

        if (isset($filtros["id_cliente"])) {
            $where .= " and F.id_cliente =? ";
            $variables[] = $filtros["id_cliente"];
        }
        if (isset($filtros["tipocontenedor"])) {
            $where .= " and LOWER(E.tipo_container ) LIKE  LOWER( concat('%' , concat(? , '%' ))) ";
            $variables[] = $filtros["tipocontenedor"];
        }
        if (isset($filtros["cliente"])) {
            $where .= " and LOWER(F.nombre_completo) LIKE LOWER( concat('%' , concat(? , '%' ))) ";
            $variables[] = $filtros["cliente"];
        }
        if (isset($filtros["id_tipocontenedor"])) {
            $where .= " and B.id_tipocontainer = ? ";
            $variables[] = $filtros["id_tipocontenedor"];
        }
        if (isset($filtros["destino"])) {
            $where .= " and LOWER(B.destino) LIKE LOWER( concat('%' , concat(? , '%' )))";
            $variables[] = $filtros["destino"];
        }
        if (isset($filtros["noentrada"])) {
            $where .= " and tipo!=?";
            $variables[] = "ENTRADA";
        }

        if (isset($filtros["exclude"])) {
            $where .= " and " . $filtros["exclude"];
        }
        $sql = "select *,F.nombre_completo nombre_completo, G.id_usuario as idusuario from ed_container A "
                . "left join ho_authstat C on A.id_authstat=C.id_authstat "
                . "left join ho_tipo_ingreso D on A.id_tipoingreso = D.id_tipo_ingreso "
                . "left join ho_tipocontainer E on A.id_tipocontainer = E.id_tipocontainer "
                . "left join ed_cliente F on A.id_cliente= F.id_cliente "
                . "left join ed_usuario G on A.id_usuario= G.id_usuario "
                . "left join ed_posiciones H on A.id_container= H.id_container and H.id_authstat=A.id_authstat "
                . "left join ho_ie I on A.id_ie = I.id_ie "
                . " where $where "
                . " order by A.id_container desc ";
        return self::execute_select($sql, $variables);
    }

}
