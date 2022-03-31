<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of logger
 *
 * @author adupuy
 */
class Logger {
    public static function log(?String $mensaje="",$array,$path){
//        "/log/api.log"
//        $arch=fopen(),"w");
//        console.log($arch);
        if(is_array($array)){
            $array= json_encode($array,true);
        }
        if(is_object($path)){
            $path = get_class($path);
        }
//        developer_log($array);
        $log = "PID: ".getmypid()." | ". $mensaje." | ".json_encode($array)." ---> ".$path;
//        developer_log(storage_path(getenv("apilog")));
        if(!file_exists(storage_path(getenv("apilog")))){
            developer_log("creando archivo nuevo");
            touch(storage_path(getenv("apilog")));
            chmod(storage_path(getenv("apilog")), 0777);
        }
        developer_log("Logueando");
//        $file=fopen(storage_path(getenv("apilog")), "w+");
//        fwrite($file, $log, strlen($log));
//        fclose($file);
        developer_log(storage_path(getenv("apilog")));
        file_put_contents(storage_path(getenv("apilog")),$log."\n\n\n" ,FILE_APPEND);
    }
}
