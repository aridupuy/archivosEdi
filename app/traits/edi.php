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
//            if ($container->getIterator()->current()->get_id_authstat() == Authstat::ENTRADA) {
                developer_log("Generando entrada");
                return new Codeco_entrada($variables, $container, $id);
//            }
        } 
        elseif($container->getIterator()->current()->get_id_authstat()==Authstat::POSICIONADO){
            $rs = Posiciones::select(["id_container" => $container->getIterator()->current()->get_id(), "id_authstat" => Authstat::POSICIONADO]);
            if ($rs->rowCount() > 0) {
                developer_log("Generando posicionado.");
                return new Codeco_salida($variables, $container, $id);
            } else {
                throw new Exception("El contenedor ya tiene todos sus edi generado.");
            }
        }
        elseif (!$container->getIterator()->current()->get_tiene_edi_salida()) {
            $rs = Posiciones::select(["id_container" => $container->getIterator()->current()->get_id(), "id_authstat" => Authstat::SALIDA]);
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
//        return true;
        /* dejo esto preparado */
        /*los posicionados son salidas, generar el archivo edi destino=> nombre del predio.*/
        /*posicionados genera archiv edi tambien como una salida.*/
        /*campo sello se llena a la salida. o posicionado.*/
        /*modificar el flujo para que los posicionados finalicen como una salida.*/
        /*salida va directamente al puerto.*/
        /**/
        $host = "ftp.msc.com";
        $username = "SV520-TRANSPORTES_LOU";
        $password = "ct6OnuRe";
        $SERVER_URL = "/To_MSC/CODECO/";
//        var_dump(scandir("pecl install ssh2"));
//        var_dump(exec("/usr/local/cpanel/"));
//        var_dump(exec("cd wget http://www.libssh2.org/snapshots/libssh2-1.4.0-20120319.tar.gz"));
//        var_dump(exec(" tar -xzf libssh2-1.4.0-20120319.tar.gz"));
//        var_dump(exec("cd libssh2-*"));
//        var_dump(exec("./configure"));
//        var_dump(exec("make all install"));
//        var_dump(exec("php -m | grep ssh2"));
        

///usr/local/cpanel/3rdparty/bin/pecl install ssh2


//        $conn = ftp_connect($host);
//        if (!$conn) {
//            developer_log("No pudimos conectarnos al ftp");
//        }
//        if (ftp_login($conn, $username, $password)) {
//            if (ftp_put($conn, $SERVER_URL.basename($file), PATH_PUBLIC_FOLDER . $file)) {
//                return true;
//            }
//            developer_log("No pudimos cargar el archivo al ftp");
//        }
//        developer_log("No pudimos loguearnos al ftp");
//        return false;
//        $fileContents = file_get_contents(PATH_PUBLIC_FOLDER.$file);
        $connection = ssh2_connect($host, 22);
        ssh2_auth_password($connection, $username, $password);
        $sftp = ssh2_sftp($connection);
//        $stream = @fopen("ssh2.sftp://$sftp$SERVER_URL".basename($file), 'w');
//        if (!$stream)
//            throw new Exception("Could not open file: ".$SERVER_URL.basename($file));
        return $this->uploadFile($sftp,PATH_PUBLIC_FOLDER . $file, $SERVER_URL . basename($file));
        
//        ssh2_scp_send($connection, '/local/filename', '/remote/filename', 0644);
//        Illuminate\Support\Facades\Storage::disk('sftp')->put($SERVER_URL.basename($file), $fileContents);
    }

    public function uploadFile($sftp,$local_file, $remote_file) {
        $stream = @fopen("ssh2.sftp://$sftp$remote_file", 'w');

        if (!$stream)
            throw new Exception("Could not open file: $remote_file");

        $data_to_send = @file_get_contents($local_file);
        if ($data_to_send === false)
            throw new Exception("Could not open local file: $local_file.");

        if (@fwrite($stream, $data_to_send) === false)
            throw new Exception("Could not send data from file: $local_file.");
        developer_log("Archivo Enviado");
        @fclose($stream);
        return true;
    }

    abstract function nombrar_archivo();
}
