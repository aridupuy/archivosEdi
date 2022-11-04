<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of edi
 *
 * @author adupuy
 */
abstract class Edi {

    protected ArrayObject $container;
    protected $variables;
    protected $id;
    protected static $posiciones;

    protected function __construct(array $variables, ArrayObject $container, $id) {
        $this->container = $container;
        $this->variables = $variables;
        $this->id = $id;
//        var_dump($this);
    }

    public function get_posiciones(): \Array_posiciones {
        return self::$posiciones;
    }

    //put your code here
    public static function factory(ArrayObject $container, $variables, $id) {
        /*
          solo deveria entrar un array del mismo cliente
          una vez entradas otra vez salidas
         */
        self::$posiciones = new \Array_posiciones();
        $rs = Posiciones::select(["id_container" => $container->getIterator()->current()->get_id()]);
        if ($container->getIterator()->current()->get_id() == null) {
            developer_log("El contenedor no existe.");
            throw new Exception("El contenedor no existe.");
        }
        self::agregar_posiciones($container);
        if (!$container->getIterator()->current()->get_tiene_edi_entrada()) {
        if ($container->getIterator()->current()->get_id_authstat() == Authstat::ENTRADA) {
                developer_log("Generando entrada");
                return new Codeco_entrada($variables, $container, $id);
            }
        } elseif (!$container->getIterator()->current()->get_tiene_edi_salida()) {
            $rs = Posiciones::select(["id_container" => $container->getIterator()->current()->get_id(), "id_authstat" => Authstat::SALIDA]);
//            var_dump($rs->rowCount());
            if ($rs->rowCount() > 0) {
                developer_log("Generando salida.");
                return new Codeco_salida($variables, $container, $id);
            }
        } else {
            throw new Exception("El contenedor ya tiene todos sus edi generado.");
        }
    }

    public function __destruct() {
        self::$posiciones->destroy();
    }

    public abstract function generar_edi();

    private static function agregar_posiciones($container) {
        foreach ($container as $cont) {
            $rs = \Posiciones::select_order(["id_container" => $cont->get_id()], "id_posicion", "asc");
            foreach ($rs as $row) {
                $posiciones = new Posiciones($row);
                self::$posiciones->add($posiciones);
            }
        }
    }

    protected function generar_archivo($content) {
//        developer_log("ACA ");
//        developer_log($content);
        $gestor_de_disco = new \Gestor_de_disco();
        $gestor_de_disco->crear_carpeta(PATH_PUBLIC_FOLDER . "Export/");
        $filename = $this->nombrar_archivo();
//        developer_log($filename);
        $filas = explode("\n", $content);
        $header = $filas[0];
//        var_dump($header);
//        $header = $filas;
        $archivo = new Archivo();
        $archivo->set_header($header);
        $archivo->set_id_usuario(\App\Http\Controllers\Controller::$USUARIO->get_id_usuario());
        $archivo->set_id_authstat(Authstat::ACTIVO);
        $archivo->set_nombre($filename);
        Model::StartTrans();
        if ($archivo->set()) {
            developer_log($archivo->get_id());
            $error = true;
            foreach ($filas as $fila) {
                $registro = new Registro();
                $registro->set_id_archivo($archivo->get_id());
                $registro->set_id_authstat(Authstat::ACTIVO);
                $registro->set_registro($fila);
                if ($registro->set()) {
//                    echo $content;
//                    echo "<br/>";
//                    echo "<br/>";
//                    echo "<br/>";
                    if ($gestor_de_disco->crear_archivo(PATH_PUBLIC_FOLDER . "Export/", $filename, $content)) {
                        developer_log("Termino guardado");
                        $error = false;
                    }
                }
            }
        }
        if (!$error) {
            if ($this->enviar_ftp("Export/" . $filename)) {
                Model::CompleteTrans();
                return "Export/" . $filename;
            }
        }
        Model::FailTrans();
        Model::CompleteTrans();
        return false;
    }

    public function enviar_ftp($file) {
        return true;
        /* dejo esto preparado */
        $conn = Safe\ftp_connect($host);
        if (!$conn) {
            developer_log("No pudimos conectarnos al ftp");
        }
        if (ftp_login($conn, $username, $password)) {
            if (ftp_put($conn, $remote_file, PATH_PUBLIC_FOLDER . $file)) {
                return true;
            }
            developer_log("No pudimos cargar el archivo al ftp");
        }
        developer_log("No pudimos loguearnos al ftp");
        return false;
    }

    abstract function nombrar_archivo();
}
