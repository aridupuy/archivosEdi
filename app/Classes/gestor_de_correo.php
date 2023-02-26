<?php
// namespace Classes;
# Arreglar trycatches
class Gestor_de_correo{
    const MAIL_INFO=EMAIL_USER_INFO;
    const MAIL_NORESPONDER=EMAIL_USER_NOREPPLY;
    const MAIL_ATENCION_AL_CLIENTE=EMAIL_USER_ATC;
    const MAIL_DESARROLLO=EMAIL_USER_DESARROLLO;
    const VALIDAR_CORREO=false;
    const SERVIDOR_HOST=EMAIL_HOST;
            //'smtp.gmail.com'; //'10.132.254.222' Obsoleta
    const SERVIDOR_PORT=EMAIL_PORT;
    const SERVIDOR_USER=EMAIL_USER;
    const SERVIDOR_PASS=EMAIL_PASS;
    const SERVIDOR_AUTH=true;
    const ACTIVAR_TEST=false;
    const NOMBRE_APP=APP_NAME;
    public static function enviar($emisor, $destinatario, $asunto, $mensaje,$file_path=false)
    {   
        if(self::ACTIVAR_TEST=='true')
            return true;
        if(self::VALIDAR_CORREO AND !validar_correo($destinatario)) return false;
        if(self::VALIDAR_CORREO AND !validar_correo($emisor)) return false;
        # Si tiene archivos adjuntos
        //if($file_path) 
            //return self::enviar_con_adjunto($emisor, $destinatario, $asunto, $mensaje,$file_path);
        # Si no tiene archivos adjuntos
        $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $cabeceras .= 'FROM: '.$emisor . "\r\n";
        $cabeceras.= "BCC:".$emisor . "\r\n";
        if(!mb_detect_encoding($mensaje, 'UTF-8', true))
            $mensaje=utf8_encode($mensaje);
        $mensaje = wordwrap($mensaje, 70, "\r\n");
        if(mail($destinatario, $asunto, $mensaje,$cabeceras)){
          if(ACTIVAR_LOG_APACHE_DE_CORREO) developer_log('Correo correctamente enviado a: '.$destinatario.'. ');
          return true;
        }
        if(ACTIVAR_LOG_APACHE_DE_CORREO) developer_log('Ha ocurrido un error al intentar enviar un correo a: '.$destinatario.'. ');
        return false;
    }
    
    public static function enviar_con_adjunto($emisor, $destinatario, $asunto, $mensaje,$file_path){
        if(self::VALIDAR_CORREO AND !validar_correo($emisor)) return false;
        if(self::VALIDAR_CORREO AND !validar_correo($destinatario)) return false;
//        if(!is_file($file_path)) {
//            if(ACTIVAR_LOG_APACHE_DE_CORREO){
//                developer_log('No existe el archivo: '.$file_path);
//            }
//            return false;
//        }
        developer_log(PATH_PUBLIC.'PHPMailer/class.phpmailer.php');
//        if(!@include_once PATH_PUBLIC.'PHPMailer/class.phpmailer.php'){
//            developer_log('Fallo al abrir la clase PHPMailer');
//            return false;
//        }
//    
//        
//        if(!@include_once PATH_PUBLIC.'PHPMailer/class.smtp.php'){
//            developer_log('Fallo al abrir la clase smtp');
//            return false;
//        }
        $email = new \PHPMailer\PHPMailer\PHPMailer();
        $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $cabeceras .= 'FROM: '.$emisor . "\r\n";
        $cabeceras.= "BCC:".$emisor . "\r\n";
        if(!mb_detect_encoding($mensaje, 'UTF-8', true))
            $mensaje=utf8_encode($mensaje);
        $mensaje = wordwrap($mensaje, 70, "\r\n");
     //   $email->SMTPDebug = 3;
     var_dump(self::SERVIDOR_USER);
     var_dump(self::SERVIDOR_PASS);
     var_dump(self::SERVIDOR_HOST);
     
        $email->isSMTP();
        $email->Host      =self::SERVIDOR_HOST;
        $email->Port      =  self::SERVIDOR_PORT; 
        $email->Username      =self::SERVIDOR_USER;
        $email->Password      =self::SERVIDOR_PASS;
        $email->From      = $emisor;
        $email->FromName  = self::NOMBRE_APP;
        $email->Body=$mensaje;
//        $email->msgHTML($mensaje);
        $email->ContentType="text/html";
        $email->CharSet ='UTF-8';
        $email->Subject   = $asunto;
        $email->isHTML(true);
        $email->SMTPAuth = self::SERVIDOR_AUTH;  
//        $email->SMTPDebug = 2;
//        $email->Body= $mensaje;
        $email->AddAddress($destinatario);
        if($file_path){
            $email->AddAttachment( $file_path , basename($file_path) );
        }
        
        if($email->Send()){
	   error_log("EMAIL SALE BIEN");
            if(ACTIVAR_LOG_APACHE_DE_CORREO) developer_log('Correo correctamente enviado a: '.$destinatario.' con un archivo adjunto. ');
            return true;
        }else{
	    error_log("EMAIL SALE MAL");
            if(ACTIVAR_LOG_APACHE_DE_CORREO){ 
                $mesErr= "Ha ocurrido un error al intentar enviar un correo a: ".$destinatario;
                $mesErr.= $email->ErrorInfo;  
                developer_log($mesErr);
                    
            }
                    return false ;
        }
        if(ACTIVAR_LOG_APACHE_DE_CORREO) developer_log('Ha ocurrido un error al intentar enviar un correo a: '.$destinatario.' con un archivo adjunto. ');
        return false;

    }

}
