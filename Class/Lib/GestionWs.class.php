<?php

class GestionWs {

    const RMT_HOST = '10.0.3.8';
    const RMT_PORT = 5555;
    const RMT_TMOT = 10;


    //Crée un socket et s'y connecte
    public static function openSocket(){

        $socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP)
            or die("Unable to create socket\n");

//        socket_set_nonblock($socket)
//            or die("Unable to set nonblock on socket\n");

        $time = time();
        while (!socket_connect($socket, self::RMT_HOST, self::RMT_PORT)) {
            $err = socket_last_error($socket);
            if ($err == 115 || $err == 114) {
                if ((time() - $time) >= self::RMT_TMOT) {
                    socket_close($socket);
                    die("Connection timed out.\n");
                }
                sleep(1);
                continue;
            }
            echo 'error '.$err.'<br/>'.PHP_EOL;
            die(socket_strerror($err) . "\n");
        }

//        socket_set_block($socket)
//            or die("Unable to set block on socket\n");


        return $socket;
    }


    //Utilise une connection socket pour query le webservice permettant de recuprer des inforamtion de la gestion
    public static function queryGestion($func,$params=null){
        //Creation et connection au socket
        $sock = self::openSocket();

        //construction du json adequat
        switch ($func){
            //Nb de ticket pour un client donné ou total de tickets ouverts
            case 'ticket_nombre':
                    $code = isset($params['codeGestion'])? $params['codeGestion'] : (isset($params)&&$params? $params : '%');
                    $json = '{"requete":{"commande":"ticket_nombre","code":"'.$code.'"}}';
                break;
            //Retourne les infos du client demandé
            case 'tiers_fiche':
                    if(isset($params['codeGestion'])){
                        $code = $params['codeGestion'];
                    }elseif(isset($params)&&$params){
                            $code = $params;
                    }else{
                        return false;
                    }

                    $json = '{"requete":{"commande":"tiers_fiche","code":"'.$code.'"}}';
                break;
            //Retourne les contacts du client demandé
            case 'tiers_contacts':
                    if(isset($params['codeGestion'])){
                        $code = $params['codeGestion'];
                    }elseif(isset($params)&&$params){
                        $code = $params;
                    }else{
                        return false;
                    }
                    $json = '{"requete":{"commande":"tiers_contacts","code":"'.$code.'"}}';
                break;

            default:
                return false;
        }

        //on envoie le json
        socket_send($sock,$json,strlen($json),0)
            or die("Unable to send data on socket\n");

        //On lit le résultat
        $res = '';
        $test = 0;

        socket_set_nonblock($sock);

        while($test <= 3)        {
            @socket_recv($sock, $buf, 1024, 0);
            $res .= $buf;

            if( strlen($buf)<=1 ){ //Pas de retour un test après une seconde
                sleep(1);
                $test++;
            } else{
                $test = 0;
            }
        }

        if($buf === false)  {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            die("Could not receive data: [$errorcode] $errormsg \n");
        }

        //Fermeture du socket
        socket_close($sock);

        //traitement du retour
        $infos = json_decode($res);

        return false;

    }

}