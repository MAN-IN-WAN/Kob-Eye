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
        //socket_set_nonblock($sock);

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
            case 'client_liste':
                    if(isset($params['tms'])){
                        $tms = $params['tms'];
                    }elseif(isset($params) && $params !== null && $params !== ''){
                        $tms = $params;
                    }else{
                        return false;
                    }
                    $json = '{"requete":{"commande":"client_liste","tms":"'.$tms.'"}}';
                break;
            case 'client_full_liste':
                $json = '{"requete":{"commande":"client_full_liste"}}';
                break;
            case 'ticket_liste':
                if(isset($params['codeGestion'])){
                    $code = $params['codeGestion'];
                }elseif(isset($params)&&$params){
                    $code = $params;
                }else{
                    return false;
                }
                $json = '{"requete":{"commande":"ticket_liste","code":"'.$code.'"}}';
                break;
            case 'action_liste':
                if(isset($params['numTick'])){
                    $num = $params['numTick'];
                }elseif(isset($params) && $params !== null && $params !== ''){
                    $num = $params;
                }else{
                    return false;
                }
                $json = '{"requete":{"commande":"action_liste","numTick":"'.$num.'"}}';
                break;
            case 'getContrat':
                if(isset($params['codeGestion'])){
                    $code = $params['codeGestion'];
                }elseif(isset($params)&&$params){
                    $code = $params;
                }else{
                    return false;
                }
                $json = '{"requete":{"commande":"getContrat","code":"'.$code.'"}}';
                break;
            default:
                return false;
        }


        //on envoie le json
        $toto = socket_send($sock,$json.PHP_EOL,strlen($json.PHP_EOL),MSG_EOF)
            or die("Unable to send data on socket\n");

        if($toto === false || $toto === null)  {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            die("Could not send data: [$errorcode] $errormsg \n");
        }

        //On lit le résultat
        $res = '';
        //$test = 0;



        //while($test <= 3)
        while(true){
            $return = @socket_recv($sock, $buf, 1024, 0);
            $res .= $buf;

            if($return === false) break;
            if(strpos($buf, PHP_EOL) !== FALSE) break;

//            if( strlen($buf)<=1 ){ //Pas de retour un test après une seconde
//                usleep(100000);
//                $test++;
//            } else{
//                $test = 0;
//            }
        }

        if($buf === false || $return === false)  {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            die("Could not receive data: [$errorcode] $errormsg \n");
        }

        //Fermeture du socket
        @socket_close($sock);


        //traitement du retour
        $infos = json_decode(utf8_encode($res));

        if($infos->succes && isset($infos->data)){
            return $infos->data;
        }

        return false;

    }


    //Check les derniers clients créés dans la gestion et mets a jour le parc
    public static function getClients($full = false){
        $tmsFile = 'Data/tmsCli.time';

        if(!is_file($tmsFile)){
            $file = fopen($tmsFile, 'w') or die('Cannot open file:  '.$tmsFile);
            fclose($file);
        }

        $tms = file_get_contents($tmsFile);

        if ($tms == '' || $tms == null){
            $tms =0;
        } else {
            $tms =(int)$tms;
        }


        if(!$full){
            $clis = self::queryGestion('client_liste',$tms);
        } else {
            $clis = self::queryGestion('client_full_liste');
        }



        if( $clis && count($clis) ) {
            foreach ($clis as $cli) {

                if(strpos($cli->nom,'*') !== false)
                    continue;

                $kecli = Sys::getOneData('Parc','Client/CodeGestion='.$cli->code);
                if(!$kecli){
                    $kecli =  genericClass::createInstance('Parc','Client');
                    $kecli->NomLDAP = $cli->nom;
                    $kecli->CodeGestion = $cli->code;
                    $kecli->NomLDAP = strtolower($cli->code);

                    $kecli->addParent('Parc/Revendeur/1');

                    $kecli->Save();

                    echo 'Client '.$cli->nom.' créé !!!!!'.PHP_EOL;
                } else {
                    echo 'Déjà '.$cli->nom.''.PHP_EOL;
                }


            }
        }

        $date = date('YmdHis');
        file_put_contents($tmsFile,$date.'00');

        return false;
    }

}