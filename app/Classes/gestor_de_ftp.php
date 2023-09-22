<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of gestor_de_ftp
 *
 * @author adupuy
 */
class Gestor_de_ftp {
    
    /**
    * @throws Exception
    */
    public function enviar_ftp($file): bool {

        $host = getenv("FTP_HOST");
        $username = getenv("USERNAME");
        $password = getenv("PASSWORD");
        $SERVER_URL = "/To_MSC/CODECO/";

        $connection = ssh2_connect($host, 22);
        ssh2_auth_password($connection, $username, $password);
        $sftp = ssh2_sftp($connection);
        try {
            return $this->uploadFile($sftp,PATH_PUBLIC_FOLDER . $file, $SERVER_URL . basename($file));
        } catch(Exception $e) {
            if(strstr($e->getMessage(),"Could not open file")){
                throw new Exception("Error al conectar al ftp.");
            }
            if(strstr($e->getMessage(),"Could not open local file")){
                throw new Exception("Error, no se puede acceder al archivo generado.");
            }
            if(strstr($e->getMessage(),"Could not send data from file")){
                throw new Exception("Error, no se puede escribir en el ftp.");
            }
            throw $e;
            
        }
    } 
    
    /**
    * @throws Exception
    */
    private function uploadFile($sftp,$local_file, $remote_file): bool {
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
}