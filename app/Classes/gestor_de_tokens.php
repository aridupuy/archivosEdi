<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Gestor_de_tokens
 *
 * @author adupuy
 */
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Encryption\Algorithm\KeyEncryption\A256KW;
use Jose\Component\Encryption\Algorithm\ContentEncryption\A256CBCHS512;
use Jose\Component\Encryption\Compression\CompressionMethodManager;
use Jose\Component\Encryption\Compression\Deflate;
use Jose\Component\Encryption\JWEBuilder;
use Jose\Component\Core\JWT;
use Jose\Component\Encryption\Serializer\CompactSerializer;

class Gestor_de_tokens {

    private $keyEncryptionAlgorithmManager;
    private $contentEncryptionAlgorithmManager;
    private $compressionMethodManager;

    public function __construct() {
        
    }
    /*A futuro usar jwt para la autenticacion*/
    
    public function crear(\Usuario $usuario) {
        $time = time();
//        var_dump($_SERVER);
        $host = $_SERVER["HTTP_HOST"] . "";
        $addr = $_SERVER["REMOTE_ADDR"] . "";
        $server = $_SERVER["SERVER_ADDR"] . "";
//        var_dump($host,$addr);
        $jws = Jose\Easy\Build::jws()
                ->exp($time + INTERVALO_SESION*60)
                ->iat($time)
                ->nbf($time)
                ->jti($usuario->get_id(), true)
                ->alg('RS512')
                ->iss('api')
                ->typ("jwe")
                ->aud($usuario->get_id())
                ->aud($usuario->get_nombre_usuario())
                ->aud("REMOTE_HOST: " . $host)
                ->aud("REMOTE_ADDR: " . $addr)
                ->aud("SERVER_ADDR: " . $server)
                ->sub(APP_NAME)
                ->claim('roles', ['vip' => false, 'usuario' => $usuario->get_id()])
                ->crit(['alg'])
                ->sign($this->rsaKey());
        
        return $jws;
    }

    private function rsaKey(): \Jose\Component\Core\JWK {
        return new \Jose\Component\Core\JWK([
            'kty' => 'RSA',
            'kid' => EMAIL_USER,
            'use' => 'sig',
            'n' => 'n4EPtAOCc9AlkeQHPzHStgAbgs7bTZLwUBZdR8_KuKPEHLd4rHVTeT-O-XV2jRojdNhxJWTDvNd7nqQ0VEiZQHz_AJmSCpMaJMRBSFKrKb2wqVwGU_NsYOYL-QtiWN2lbzcEe6XC0dApr5ydQLrHqkHHig3RBordaZ6Aj-oBHqFEHYpPe7Tpe-OfVfHd1E6cS6M1FZcD1NNLYD5lFHpPI9bTwJlsde3uhGqC0ZCuEHg8lhzwOHrtIQbS0FVbb9k3-tVTU4fg_3L_vniUFAKwuCLqKnS2BYwdq_mzSnbLY7h_qixoR7jig3__kRhuaxwUkRz5iaiQkqgc5gHdrNP5zw',
            'e' => 'AQAB',
            'd' => 'bWUC9B-EFRIo8kpGfh0ZuyGPvMNKvYWNtB_ikiH9k20eT-O1q_I78eiZkpXxXQ0UTEs2LsNRS-8uJbvQ-A1irkwMSMkK1J3XTGgdrhCku9gRldY7sNA_AKZGh-Q661_42rINLRCe8W-nZ34ui_qOfkLnK9QWDDqpaIsA-bMwWWSDFu2MUBYwkHTMEzLYGqOe04noqeq1hExBTHBOBdkMXiuFhUq1BU6l-DqEiWxqg82sXt2h-LMnT3046AOYJoRioz75tSUQfGCshWTBnP5uDjd18kKhyv07lhfSJdrPdM5Plyl21hsFf4L_mHCuoFau7gdsPfHPxxjVOcOpBrQzwQ',
            'p' => '3Slxg_DwTXJcb6095RoXygQCAZ5RnAvZlno1yhHtnUex_fp7AZ_9nRaO7HX_-SFfGQeutao2TDjDAWU4Vupk8rw9JR0AzZ0N2fvuIAmr_WCsmGpeNqQnev1T7IyEsnh8UMt-n5CafhkikzhEsrmndH6LxOrvRJlsPp6Zv8bUq0k',
            'q' => 'uKE2dh-cTf6ERF4k4e_jy78GfPYUIaUyoSSJuBzp3Cubk3OCqs6grT8bR_cu0Dm1MZwWmtdqDyI95HrUeq3MP15vMMON8lHTeZu2lmKvwqW7anV5UzhM1iZ7z4yMkuUwFWoBvyY898EXvRD-hdqRxHlSqAZ192zB3pVFJ0s7pFc',
            'dp' => 'B8PVvXkvJrj2L-GYQ7v3y9r6Kw5g9SahXBwsWUzp19TVlgI-YV85q1NIb1rxQtD-IsXXR3-TanevuRPRt5OBOdiMGQp8pbt26gljYfKU_E9xn-RULHz0-ed9E9gXLKD4VGngpz-PfQ_q29pk5xWHoJp009Qf1HvChixRX59ehik',
            'dq' => 'CLDmDGduhylc9o7r84rEUVn7pzQ6PF83Y-iBZx5NT-TpnOZKF1pErAMVeKzFEl41DlHHqqBLSM0W1sOFbwTxYWZDm6sI6og5iTbwQGIC3gnJKbi_7k_vJgGHwHxgPaX2PnvP-zyEkDERuf-ry4c_Z11Cq9AqC2yeL6kdKT1cYF8',
            'qi' => '3PiqvXQN0zwMeE-sBvZgi289XP9XCQF3VWqPzMKnIgQp7_Tugo6-NZBKCQsMf3HaEGBjTVJs_jcK8-TRXvaKe-7ZMaQj8VfBdYkssbu0NKDDhjJ-GtiseaDVWt7dcH0cfwxgFUHpQh7FoCrjFJ6h6ZEpMF6xmujs4qMpPz8aaI4',
        ]);
    }

    private function octKey(): \Jose\Component\Core\JWK {
        return new \Jose\Component\Core\JWK([
            'kty' => 'oct',
            'k' => '3PiqvXQN0zwMeE-sBvZgi289XP9XCQF3VWqPzMKnIgQp7_Tugo6-NZBKCQsMf3HaEGBjTVJs_jcK8-TRXvaKe-7ZMaQj8VfBdYkssbu0NKDDhjJ-GtiseaDVWt7dcH0cfwxgFUHpQh7FoCrjFJ6h6ZEpMF6xmujs4qMpPz8aaI4',
        ]);
    }

    private function noneKey(): \Jose\Component\Core\JWK {
        return new \Jose\Component\Core\JWK([
            'kty' => 'none',
        ]);
    }

    public function leer($access_token): ?Jose\Easy\JWT {
        if (!$access_token OR (str_replace("Bearer undefined", "", $access_token) =="")) {
            developer_log("La peticion no se puede procesar sin estar autenticado.");
            return null;
        }
        if (str_contains($access_token, "Bearer")) {
            $bearer = explode("Bearer ", $access_token);
            $token = $bearer[1];
            $time = time();
         $jws = \Jose\Easy\Load::jws($token)
                    ->algs(['RS512']) // The key encryption algorithms allowed to be used
                    ->exp()
                    ->iat()
                    ->nbf()
                    ->iss('api')
                    ->sub(APP_NAME)
                    ->key($this->rsaKey()) // Key used to decrypt the token
                    ->run();
            return $jws;
        } else {
            return null;
        }
//        if()
    }

}

//class Load extends \Jose\Easy\Load{
//    
//    public static function jws($param) {
//       return Validate::token($jws);
//    }
//}
//class Validate extends \Jose\Easy\Validate{
//    public static function token($token) {
//        parent::token($token);
//    }
//    public function getClaims() {
//        return $this->claimCheckers;
//    }
//}
