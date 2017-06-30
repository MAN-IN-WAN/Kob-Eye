<?php

class GestionWs {

    const RMT_HOST = '10.0.3.8';//'10.0.3.149';
    const RMT_PORT = 5555;
    const RMT_TMOT = 5;


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
        socket_set_option($sock,SOL_SOCKET,SO_RCVTIMEO,array('sec'=>self::RMT_TMOT,'usec'=>0));

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
            case 'contact_liste':
                if(isset($params['lastTms'])){
                    $tms = $params['lastTms'];
                }elseif(isset($params)&&$params!==null){
                    $tms = $params;
                }else{
                    return false;
                }
                $json = '{"requete":{"commande":"contact_liste","tms":"'.$tms.'"}}';
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
                if(isset($params['codeGestion']) && isset($params['lastTms'])) {
                    $code = $params['codeGestion'];
                    $tms = $params['lastTms'];
                }else{
                    return false;
                }
                $json = '{"requete":{"commande":"ticket_liste","code":"'.$code.'","tms":"'.$tms.'"}}';
                break;
            case 'ticket_full_liste':
                if(isset($params['codeGestion'])){
                    $code = $params['codeGestion'];
                }elseif(isset($params)&&$params){
                    $code = $params;
                }else{
                    return false;
                }
                $json = '{"requete":{"commande":"ticket_full_liste","code":"'.$code.'"}}';
                break;
            case 'ticket_last_liste':
                if(isset($params['lastTms'])){
                    $tms = $params['lastTms'];
                }elseif(isset($params)&&$params!==null){
                    $tms = $params;
                }else{
                    return false;
                }
                $json = '{"requete":{"commande":"ticket_last_liste","tms":"'.$tms.'"}}';
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
            case 'contrat_liste':
                if(isset($params['codeGestion']) && isset($params['lastTms'])) {
                    $code = $params['codeGestion'];
                    $tms = $params['lastTms'];
                }else{
                    return false;
                }
                $json = '{"requete":{"commande":"contrat_liste","code":"'.$code.'","tms":"'.$tms.'"}}';
                break;
            case 'contrat_full_liste':
                if(isset($params['codeGestion'])){
                    $code = $params['codeGestion'];
                }elseif(isset($params)&&$params){
                    $code = $params;
                }else{
                    return false;
                }
                $json = '{"requete":{"commande":"contrat_full_liste","code":"'.$code.'"}}';
                break;
            case 'contrat_last_liste':
                if(isset($params['lastTms'])){
                    $tms = $params['lastTms'];
                }elseif(isset($params)&&$params!==null){
                    $tms = $params;
                }else{
                    return false;
                }
                $json = '{"requete":{"commande":"contrat_last_liste","tms":"'.$tms.'"}}';
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
        socket_close($sock);


        //traitement du retour
        $infos = json_decode(utf8_encode($res));

        if($infos->succes && isset($infos->data)){
            return $infos->data;
        }
        if(!$infos->succes){
            print_r($infos);
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

                $nom = preg_replace('/[^a-zA-Z0-9]/','',$cli->nom);
                $kecli = Sys::getOneData('Parc','Client/CodeGestion='.Utils::KEAddSlashes($cli->code));
                if(!$kecli){
                    $kecli =  genericClass::createInstance('Parc','Client');
                    $kecli->Nom = strtoupper($cli->nom);
                    $kecli->CodeGestion = $cli->code;
                    $kecli->NomLDAP = strtolower($nom);

                    $kecli->addParent('Parc/Revendeur/1');

                    $kecli->Save();

                    echo 'Client '.$cli->nom.' créé !!!!!'.PHP_EOL;
                } else {
                    $kecli->Nom = strtoupper($cli->nom);
                    //$kecli->NomLDAP = strtolower($nom);
                    $kecli->Save();
                    echo 'Déjà '.$cli->nom.''.PHP_EOL;
                }


            }

            $date = date('YmdHis');
            file_put_contents($tmsFile,$date.'00');
        }



        return false;
    }

    //Check les derniers clients créés dans la gestion et mets a jour le parc
    public static function getContacts(){

        $tmsFile = 'Data/tmsContactLast.time';

        if(!is_file($tmsFile)){
            $file = fopen($tmsFile, 'w') or die('Cannot open file:  '.$tmsFile);
            fclose($file);
        }

        $tms = file_get_contents($tmsFile) != '' ? file_get_contents($tmsFile) : 0;

        $contacts = self::queryGestion('contact_liste', $tms);


        if( $contacts && count($contacts) ) {
            foreach ($contacts as $contact) {
                //Si on a passé juste un/des code(s) gestion on recup l'objet KE
                $clitemp = Sys::getOneData('Parc','Client/CodeGestion='.$contact->codeGestion);
                if(!$clitemp){
                    echo 'Client '.$contact->codeGestion.' introuvable ';
                    continue;
                } else {
                    $cli= $clitemp;
                }


                $kecontact = Sys::getOneData('Parc','Contact/IdGestion='.$contact->id);
                if(!$kecontact){
                    $kecontact =  genericClass::createInstance('Parc','Contact');
                    $kecontact->IdGestion = $contact->id;
                    $kecontact->Nom = $contact->nom;
                    $kecontact->Prenom = $contact->prenom;
                    $kecontact->Tel = $contact->tel;
                    $kecontact->Mobile = $contact->gsm;
                    $kecontact->Fax = $contact->fax;
                    $kecontact->Email = $contact->mail;

                    $kecontact->addParent('Parc/Client/'.$cli->Id);

                    $kecontact->Save();

                    echo 'Client '.$cli->Nom.' Contact: '.$kecontact->Nom.' '.$kecontact->Prenom.' !!!!!'.PHP_EOL;
                } else {
                    $kecontact->Nom = $contact->nom;
                    $kecontact->Prenom = $contact->prenom;
                    $kecontact->Tel = $contact->tel;
                    $kecontact->Mobile = $contact->gsm;
                    $kecontact->Fax = $contact->fax;
                    $kecontact->Email = $contact->mail;

                    $kecontact->Save();

                    echo 'Client '.$cli->Nom.' Contact: '.$kecontact->Nom.' '.$kecontact->Prenom.' UPDATE !'.PHP_EOL;
                }
            }

            $date = date('YmdHis');
            file_put_contents($tmsFile,$date.'00');
        }

        return false;
    }


    //Check les derniers contrats créés dans la gestion et mets a jour le parc
    public static function getContrats( $clients = null, $forcefull = false){
        if(!$clients){
            $clients = Sys::getData('Parc','Client');
        }


        if(!is_array($clients)){
            $clients = array($clients);
        }

        foreach ($clients as $cli) {
            //Si on a passé juste un/des code(s) gestion on recup l'objet KE
            if(!is_object($cli)){
                $clitemp = Sys::getOneData('Parc','Client/CodeGestion='.Utils::KEAddSlashes($cli));
                if(!$clitemp){
                    echo 'Client '.$cli.' introuvable: ';
                    continue;
                } else {
                    $cli= $clitemp;
                }
            }

            $tmsFile = 'Data/tmsContrat_'.$cli->Id.'.time';

            if(!is_file($tmsFile)){
                $file = fopen($tmsFile, 'w') or die('Cannot open file:  '.$tmsFile);
                fclose($file);
            }

            $tms = file_get_contents($tmsFile);


            if ($tms == '' || $tms == null || $forcefull){
                $cons = self::queryGestion('contrat_full_liste', $cli->CodeGestion);
            } else {
                $cons = self::queryGestion('contrat_liste', array('codeGestion'=>$cli->CodeGestion,'lastTms'=>(int)$tms));
            }



            //Etats gestions
//            $states = array(
//                1=>'A facturer'
//            ,2=>'En cours'
//            ,3=>'Terminé'
//            ,4=>'Abandonné'
//            ,5=>'Commercial'
//            );

            if( $cons && count($cons) ) {
                foreach ($cons as $contrat) {

                    $kecont = Sys::getOneData('Abtel','Contrat/Code='.$contrat->code);
                    if(!$kecont){
                        $kecont =  genericClass::createInstance('Abtel','Contrat');
                        $kecont->IdGestion = $contrat->id;
                        $kecont->Code = $contrat->code;
                        $kecont->Type = $contrat->type;
                        $kecont->Libelle = $contrat->libelle;

                        $dateDeb = new DateTime($contrat->dateDebut);
                        $kecont->DateDebut = $dateDeb->getTimestamp();
                        $contratFin = new DateTime($contrat->contratFin);
                        $kecont->ContratFin = $contratFin->getTimestamp();

                        $kecont->Duree = $contrat->duree;
                        $kecont->EngagementInit = $contrat->engagementInit;
                        $kecont->Preavis = $contrat->preavis;
                        $kecont->FrequenceFactu = $contrat->frequenceFactu;

                        $finFactu = new DateTime($contrat->finFactu);
                        $kecont->FinFactu = $finFactu->getTimestamp();
                        $dateEcheance = new DateTime($contrat->dateEcheance);
                        $kecont->DateEcheance = $dateEcheance->getTimestamp();

                        $kecont->MontantAnnu = $contrat->montantAnnu;
                        $kecont->MontantMensu = $contrat->montantMensu;
                        $kecont->TaciteRecond = $contrat->taciteRecond;
                        $kecont->Commentaire = $contrat->commentaire;

                        $kecont->addParent('Parc/Client/'.$cli->Id);

                        $kecont->Save();

                        echo 'Client '.$cli->Nom.' Contrat: '.$kecont->Code.' !!!!!'.PHP_EOL;
                    } else {

                        $dateDeb = new DateTime($contrat->dateDebut);
                        $kecont->DateDebut = $dateDeb->getTimestamp();
                        $contratFin = new DateTime($contrat->contratFin);
                        $kecont->ContratFin = $contratFin->getTimestamp();
                        $finFactu = new DateTime($contrat->finFactu);
                        $kecont->FinFactu = $finFactu->getTimestamp();
                        $dateEcheance = new DateTime($contrat->dateEcheance);
                        $kecont->DateEcheance = $dateEcheance->getTimestamp();

                        $kecont->Duree = $contrat->duree;
                        $kecont->FrequenceFactu = $contrat->frequenceFactu;
                        $kecont->MontantAnnu = $contrat->montantAnnu;
                        $kecont->MontantMensu = $contrat->montantMensu;
                        $kecont->TaciteRecond = $contrat->taciteRecond;
                        $kecont->Commentaire = $contrat->commentaire;
                        $kecont->Duree = $contrat->duree;

                        $kecont->Save();

                        echo 'Client '.$cli->Nom.' Contrat: '.$kecont->Code.' UPDATE !'.PHP_EOL;
                    }


                }

                $date = date('YmdHis');
                //file_put_contents($tmsFile,$date.'00');
            }



        }

        return false;
    }

    //Check les derniers contrats créés dans la gestion et mets a jour le parc
    public static function altGetContrats(){

        $tmsFile = 'Data/tmsContratLast.time';

        if(!is_file($tmsFile)){
            $file = fopen($tmsFile, 'w') or die('Cannot open file:  '.$tmsFile);
            fclose($file);
        }

        $tms = file_get_contents($tmsFile) != '' ? file_get_contents($tmsFile) : 0;


        $cons = self::queryGestion('contrat_last_liste', $tms);


        if( $cons && count($cons) ) {

            foreach ($cons as $contrat) {

                $clitemp = Sys::getOneData('Parc','Client/CodeGestion='.Utils::KEAddSlashes($contrat->codeGestion));
                if(!$clitemp){
                    echo 'Client '.$contrat->codeGestion.' introuvable: ';
                    continue;
                } else {
                    $cli= $clitemp;
                }

                $kecont = Sys::getOneData('Abtel','Contrat/Code='.$contrat->code);
                if(!$kecont){
                    $kecont =  genericClass::createInstance('Abtel','Contrat');
                    $kecont->IdGestion = $contrat->id;
                    $kecont->Code = $contrat->code;
                    $kecont->Type = $contrat->type;
                    $kecont->Libelle = $contrat->libelle;

                    $dateDeb = new DateTime($contrat->dateDebut);
                    $kecont->DateDebut = $dateDeb->getTimestamp();
                    $contratFin = new DateTime($contrat->contratFin);
                    $kecont->ContratFin = $contratFin->getTimestamp();

                    $kecont->Duree = $contrat->duree;
                    $kecont->EngagementInit = $contrat->engagementInit;
                    $kecont->Preavis = $contrat->preavis;
                    $kecont->FrequenceFactu = $contrat->frenquenceFactu;

                    $finFactu = new DateTime($contrat->finFactu);
                    $kecont->FinFactu = $finFactu->getTimestamp();
                    $dateEcheance = new DateTime($contrat->dateEcheance);
                    $kecont->DateEcheance = $dateEcheance->getTimestamp();

                    $kecont->MontantAnnu = $contrat->montantAnnu;
                    $kecont->MontantMensu = $contrat->montantMensu;
                    $kecont->TaciteRecond = $contrat->taciteRecond;
                    $kecont->Commentaire = $contrat->commentaire;

                    $kecont->addParent('Parc/Client/'.$cli->Id);

                    $kecont->Save();

                    echo 'Client '.$cli->Nom.' Contrat: '.$kecont->Code.' !!!!!'.PHP_EOL;
                } else {

                    $dateDeb = new DateTime($contrat->dateDebut);
                    $kecont->DateDebut = $dateDeb->getTimestamp();
                    $contratFin = new DateTime($contrat->contratFin);
                    $kecont->ContratFin = $contratFin->getTimestamp();
                    $finFactu = new DateTime($contrat->finFactu);
                    $kecont->FinFactu = $finFactu->getTimestamp();
                    $dateEcheance = new DateTime($contrat->dateEcheance);
                    $kecont->DateEcheance = $dateEcheance->getTimestamp();

                    $kecont->Duree = $contrat->duree;
                    $kecont->FrequenceFactu = $contrat->frenquenceFactu;
                    $kecont->MontantAnnu = $contrat->montantAnnu;
                    $kecont->MontantMensu = $contrat->montantMensu;
                    $kecont->TaciteRecond = $contrat->taciteRecond;
                    $kecont->Commentaire = $contrat->commentaire;
                    $kecont->Duree = $contrat->duree;

                    $kecont->Save();

                    echo 'Client '.$cli->Nom.' Contrat: '.$kecont->Code.' UPDATE !'.PHP_EOL;
                }


            }

            $date = date('YmdHis');
            file_put_contents($tmsFile,$date.'00');
        }


        return false;
    }


    //Check les derniers clients créés dans la gestion et mets a jour le parc
    public static function altGetTickets(){

        $tmsFile = 'Data/tmsTicketLast.time';

        if(!is_file($tmsFile)){
            $file = fopen($tmsFile, 'w') or die('Cannot open file:  '.$tmsFile);
            fclose($file);
        }

        $tms = file_get_contents($tmsFile) != '' ? file_get_contents($tmsFile) : 0;

        $tix = self::queryGestion('ticket_last_liste', $tms);




        //Etats gestions
        $states = array(
            1=>'A facturer'
            ,2=>'En cours'
            ,3=>'Terminé'
            ,4=>'Abandonné'
            ,5=>'Commercial'
        );

        //Prios gestions
        $prios = array(
            0=>'Info'
            ,1=>'Warning'
            ,2=>'Moyenne'
            ,3=>'Haute'
        );

        if( $tix && count($tix) ) {
            foreach ($tix as $ticket) {
                //Si on a passé juste un/des code(s) gestion on recup l'objet KE
                $clitemp = Sys::getOneData('Parc','Client/CodeGestion='.Utils::KEAddSlashes($ticket->codeGestion));
                if(!$clitemp){
                    echo 'Client '.$ticket->codeGestion.' introuvable ';
                    continue;
                } else {
                    $cli= $clitemp;
                }


                $ketick = Sys::getOneData('Parc','Ticket/Numero='.$ticket->numTicket);
                if(!$ketick){
                    $ketick =  genericClass::createInstance('Parc','Ticket');
                    $ketick->IdGestion = $ticket->id;
                    $ketick->Numero = $ticket->numTicket;
                    $ketick->Type = $ticket->type=='DEM'?'Demande':($ticket->type=='INC'?'Incident':'NC');
                    $ketick->Titre = $ticket->titre;

                    $dateCrea = new DateTime($ticket->dateEcheance);
                    $ketick->DateCrea = $dateCrea->getTimestamp();
                    $dateEcheance = new DateTime($ticket->dateEcheance);
                    $ketick->DateEcheance = $dateEcheance->getTimestamp();

                    $ketick->Etat = $states[$ticket->etat];
                    $ketick->UserCrea = $ticket->userCrea;
                    $ketick->UserNext = $ticket->userNext;
                    $ketick->Priorite = $prios[$ticket->urgence];

                    $ketick->addParent('Parc/Client/'.$cli->Id);

                    $ketick->Save();

                    echo 'Client '.$cli->Nom.' Ticket: '.$ketick->Numero.' !!!!!'.PHP_EOL;
                } else {
                    $ketick->Titre = $ticket->titre;
                    $ketick->UserNext = $ticket->userNext;
                    $ketick->Type = $ticket->type=='DEM'?'Demande':$ticket->type=='INC'?'Incident':'NC';
                    $ketick->Etat = $states[$ticket->etat];
                    $ketick->Priorite = $prios[$ticket->urgence];

                    $dateEcheance = new DateTime($ticket->dateEcheance);
                    $ketick->DateEcheance = $dateEcheance->getTimestamp();
                    $dateCrea = new DateTime($ticket->dateEcheance);
                    $ketick->DateCrea = $dateCrea->getTimestamp();



                    $ketick->Save();

                    echo 'Client '.$cli->Nom.' Ticket: '.$ketick->Numero.' UPDATE !'.PHP_EOL;
                }
            }

            $date = date('YmdHis');
            file_put_contents($tmsFile,$date.'00');
        }

        return false;
    }




    //Check les derniers clients créés dans la gestion et mets a jour le parc
    public static function getTickets( $clients = null, $forcefull = false, $all = false){
        if($clients === null && !$all) {
            return false;
        } elseif ($all){
            $clients = Sys::getData('Parc','Client');
        }


        if(!is_array($clients)){
            $clients = array($clients);
        }


        foreach ($clients as $cli) {
            //Si on a passé juste un/des code(s) gestion on recup l'objet KE
            if(!is_object($cli)){
                $clitemp = Sys::getOneData('Parc','Client/CodeGestion='.$cli);
                if(!$clitemp){
                    echo 'Client '.$cli.' introuvable: ';
                    continue;
                } else {
                    $cli= $clitemp;
                }
            }

            $tmsFile = 'Data/tmsTicket_'.$cli->Id.'.time';

            if(!is_file($tmsFile)){
                $file = fopen($tmsFile, 'w') or die('Cannot open file:  '.$tmsFile);
                fclose($file);
            }

            $tms = file_get_contents($tmsFile);

            if ($tms == '' || $tms == null || $forcefull){
                $tix = self::queryGestion('ticket_full_liste', $cli->CodeGestion);

            } else {
                $tix = self::queryGestion('ticket_liste', array('codeGestion'=>$cli->CodeGestion,'lastTms'=>(int)$tms));
            }



            //Etats gestions
            $states = array(
                1=>'A facturer'
            ,2=>'En cours'
            ,3=>'Terminé'
            ,4=>'Abandonné'
            ,5=>'Commercial'
            );

            //Prios gestions
            $prios = array(
                0=>'Info'
            ,1=>'Warning'
            ,2=>'Moyenne'
            ,3=>'Haute'
            );

            if( $tix && count($tix) ) {
                foreach ($tix as $ticket) {

                    $ketick = Sys::getOneData('Parc','Ticket/Numero='.$ticket->numTicket);
                    if(!$ketick){
                        $ketick =  genericClass::createInstance('Parc','Ticket');
                        $ketick->IdGestion = $ticket->id;
                        $ketick->Numero = $ticket->numTicket;
                        $ketick->Type = $ticket->type=='DEM'?'Demande':($ticket->type=='INC'?'Incident':'NC');
                        $ketick->Titre = $ticket->titre;

                        $dateCrea = new DateTime($ticket->dateEcheance);
                        $ketick->DateCrea = $dateCrea->getTimestamp();
                        $dateEcheance = new DateTime($ticket->dateEcheance);
                        $ketick->DateEcheance = $dateEcheance->getTimestamp();

                        $ketick->Etat = $states[$ticket->etat];
                        $ketick->UserCrea = $ticket->userCrea;
                        $ketick->UserNext = $ticket->userNext;
                        $ketick->Priorite = $prios[$ticket->urgence];

                        $ketick->addParent('Parc/Client/'.$cli->Id);

                        $ketick->Save();

                        echo 'Client '.$cli->Nom.' Ticket: '.$ketick->Numero.' !!!!!'.PHP_EOL;
                    } else {
                        $ketick->Titre = $ticket->titre;
                        $ketick->UserNext = $ticket->userNext;
                        $ketick->Type = $ticket->type=='DEM'?'Demande':$ticket->type=='INC'?'Incident':'NC';
                        $ketick->Etat = $states[$ticket->etat];
                        $ketick->Priorite = $prios[$ticket->urgence];

                        $dateEcheance = new DateTime($ticket->dateEcheance);
                        $ketick->DateEcheance = $dateEcheance->getTimestamp();
                        $dateCrea = new DateTime($ticket->dateEcheance);
                        $ketick->DateCrea = $dateCrea->getTimestamp();



                        $ketick->Save();

                        echo 'Client '.$cli->Nom.' Ticket: '.$ketick->Numero.' UPDATE !'.PHP_EOL;
                    }


                }

                $date = date('YmdHis');
                file_put_contents($tmsFile,$date.'00');
            }

        }

        return false;
    }


}