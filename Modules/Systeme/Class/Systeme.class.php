<?php
class Systeme extends Module {
    /**
     * getToken
     * get a new token from your current connection object
     * return string
     */
    static function getToken() {
        //si connecté
        return Sys::$Session->Session;
    }
    /**
     * isLogged
     * check if session is started
     * if session exists, start session
     */
    static function isLogged(){
        if (isset($_POST['logkey']))
            $session = $_POST['logkey'];
        else return false;
        if (isset($_POST['user_id']))
            $user_id = $_POST['user_id'];
        else return false;
        if (!empty($session)) {
            $c = Sys::getOneData('Systeme', 'Connexion/Session=' . $session.'&Utilisateur='.$user_id);
            if (is_object($c)) {
                Connection::startConnection($c);
                return true;
            }
        }
        return false;
    }
    /**
     * Surcharge de la fonction Check
     * Vérifie l'existence du role PARC_CLIENT et son association à un groupe
     * Sinon génère le ROLE et créé un Group à la racine et lui affecte le ROLE
     */
    function Check () {
        parent::Check();
        $g = Sys::getCount('Systeme','Group');
        if (!$g){
            //creation du groupe public
            $g = genericClass::createInstance('Systeme','Group');
            $g->Nom = "[DEFAULT] PUBLIC";
            $g->Skin = "LoginBootstrap";
            $g->Save();

            //creation de l'utilisateur login par défaut
            $u = genericClass::createInstance('Systeme','User');
            $u->Login = 'login';
            $u->Pass = 'secret';
            $u->Mail = 'login@login.com';
            $u->Skin = 'LoginBootstrap';
            $u->Actif = true;
            $u->addParent($g);
            $u->Save();

            //creation du groupe admin
            $g = genericClass::createInstance('Systeme','Group');
            $g->Nom = "[DEFAULT] ADMIN";
            $g->Skin = "LoginBootstrap";
            $g->Save();

            //creation de l'utilisateur admin par défaut
            $u = genericClass::createInstance('Systeme','User');
            $u->Login = 'admin';
            $u->Pass = '21wyisey';
            $u->Mail = 'admin@admin.com';
            $u->Skin = 'AdminV2';
            $u->Actif = true;
            $u->Admin = true;
            $u->addParent($g);
            $u->Save();
        }
    }

    static function retrievePassword($email){
        //recherche du compte
        $u = Sys::getOneData('Systeme','User/Mail='.$email);
        if ($u){
            $np = substr($u->CodeVerif,0,8);

            $Mail = new Mail();
            $Mail->Subject("Mot de passe oublié ".Sys::$domain);
            $Mail -> From("noreply@".Sys::$domain);
            $Mail -> ReplyTo("noreply@".Sys::$domain);
            $Mail -> To($u->Mail);
            $bloc = new Bloc();
            $mailContent = "
			Bonjour ".$u->Nom." ".$u->Prenom.",<br />Vous avez effectué une demande de changement de mot de passe.<br/>
			Conservez le bien ou changez le à la prochaine connexion.<br />Nouveau mot de passe: <h1>".$np."</h1>";
            $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
            $Pr = new Process();
            $bloc -> init($Pr);
            $bloc -> generate($Pr);
            $Mail -> Body($bloc -> Affich());
            $Mail -> Send();
            $u->Set('Pass',$np);
            $u->Save();
            return true;
        }
        return false;
    }
    public static function registerDevice($KEY,$USER_ID,$TYPE="Android",$ADMIN=0)
    {
        //on vérifie l'existence de l'appareil
        if (!empty($KEY)&&!empty($USER_ID)&&!Sys::getCount('Systeme', 'Device/Key=' . $KEY)) {
            //on ajoute le device
            $d = genericClass::createinstance('Systeme', 'Device');
            $d->Set('Key', $KEY);
            $d->Set('Type', $TYPE);
            $d->Set('Admin', $ADMIN);
            $d->AddParent('Systeme/User/'.$USER_ID);
            $d->Save();
        }
    }

    public static function testNotification()
    {
        // prep the bundle
        $msg = array
        (
            'message' => 'here is a message. message',
            'title' => 'This is a title. title',
            'subtitle' => 'This is a subtitle. subtitle',
            'tickerText' => 'Ticker text here...Ticker text here...Ticker text here',
            'largeIcon' => 'large_icon',
            'smallIcon' => 'small_icon'
        );
        echo 'send message';
        print_r($msg);

        Systeme::sendNotification($msg,'all');
    }

    public static function  sendNotification($msg,$target) {
        $msg['vibrate'] = 1;
        $msg['sound'] = 1;
        if (!isset($msg['alert']))$msg['alert'] = 1;
        if (!isset($msg['store']))$msg['store'] = '';
        $msg['largeIcon'] = 'large_icon';
        $msg['smallIcon'] = 'small_icon';

        //backapp
        //$API_ACCESS_KEY = 'AIzaSyCGGUR9EbkicdM7IUXp1l-Z2sHFQCnLp-A';
        // API access key from Google API's Console
        //castanet
        //define('API_ACCESS_KEY', 'AIzaSyD-WPYJ39eWmA2aWzgn6fQF1A5WOv3FG5A');
        //cours
        //$API_ACCESS_KEY = 'AIzaSyBbYtVciuBNkTX2h13sHhAvsjBRCSdtb6U';
        //ecluse
        //define('API_ACCESS_KEY', 'AIzaSyCmaDWG5O2HrKdXm4JCkJPQZAvtwqCljos');

        //recherche des périphériques à associer.
        //die('envoi utilisateur '.$target.' | '.($target>0));
        /****
         * ANDROID
         */
        if ($target>0){
            $dev = Sys::getData('Systeme','User/'.$target.'/Device/Admin=0&Type=Android');
            $API_ACCESS_KEY = 'AIzaSyBbYtVciuBNkTX2h13sHhAvsjBRCSdtb6U';
        }elseif ($target=="all"){
            $dev = Sys::getData('Systeme','Device/Admin=0&Type=Android');
            $API_ACCESS_KEY = 'AIzaSyBbYtVciuBNkTX2h13sHhAvsjBRCSdtb6U';
        }elseif($target=="admin"){
            $dev = Sys::getData('Systeme','Device/Admin=1&Type=Android');
            $API_ACCESS_KEY = 'AIzaSyCGGUR9EbkicdM7IUXp1l-Z2sHFQCnLp-A';
        }
        $registrationIds = array();
        foreach ($dev as $d){
            $registrationIds[] = $d->Key;
        }
        if (sizeof($registrationIds)) {
            $fields = array
            (
                'registration_ids' => $registrationIds,
                'data' => $msg
            );

            $headers = array
            (
                'Authorization: key=' . $API_ACCESS_KEY,
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);
        }
        /****
         * IOS
         */
        if ($target>0){
            $dev = Sys::getData('Systeme','User/'.$target.'/Device/Admin=0&Type=iOS');
        }elseif ($target=="all"){
            $dev = Sys::getData('Systeme','Device/Admin=0&Type=iOS');
        }
        foreach ($dev as $d){
            $deviceToken = $d->Key;
            $ctx = stream_context_create();
            // ck.pem is your certificate file
            stream_context_set_option($ctx, 'ssl', 'local_cert', 'Modules/Systeme/Device/dev.cours.pem');
            stream_context_set_option($ctx, 'ssl', 'passphrase', '21wyisey');
            // Open a connection to the APNS server
            $gateway = 'ssl://gateway.push.apple.com:2195';
            $gateway_dev = 'ssl://gateway.sandbox.push.apple.com:2195';
            $fp = stream_socket_client(
                $gateway_dev, $err,
                $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
            if (!$fp)
                exit("Failed to connect: $err $errstr" . PHP_EOL);
            // Create the payload body
            $body = (object)array(
                'aps' => array(
                    'alert' => array(
                        'title' => $msg['title'],
                        'body' => $msg['message']
                    ),
                    'sound' => 'default'
                ),
                'store' => $msg['store'],
                'alert' => $msg['alert']
            );

            // Encode the payload as JSON
            $payload = json_encode($body);
            // Build the binary notification
            $tmp = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
            // Send it to the server
            $result = fwrite($fp, $tmp, strlen($tmp));
//echo $tmp."\r\n";
//echo $result."\r\n";
            // Close the connection to the server
            //self::checkAppleErrorResponse($fp);
            fclose($fp);
            /*if (!$result)
                return 'Message not delivered' . PHP_EOL;
            else
                return 'Message successfully delivered' . PHP_EOL;*/
        }
   }
    //FUNCTION to check if there is an error response from Apple
//         Returns TRUE if there was and FALSE if there was not
   public static function checkAppleErrorResponse($fp) {

        //byte1=always 8, byte2=StatusCode, bytes3,4,5,6=identifier(rowID). Should return nothing if OK.
        $apple_error_response = fread($fp, 6);
        //NOTE: Make sure you set stream_set_blocking($fp, 0) or else fread will pause your script and wait forever when there is no response to be sent.

        if ($apple_error_response) {
            //unpack the error response (first byte 'command" should always be 8)
            $error_response = unpack('Ccommand/Cstatus_code/Nidentifier', $apple_error_response);

            if ($error_response['status_code'] == '0') {
                $error_response['status_code'] = '0-No errors encountered';
            } else if ($error_response['status_code'] == '1') {
                $error_response['status_code'] = '1-Processing error';
            } else if ($error_response['status_code'] == '2') {
                $error_response['status_code'] = '2-Missing device token';
            } else if ($error_response['status_code'] == '3') {
                $error_response['status_code'] = '3-Missing topic';
            } else if ($error_response['status_code'] == '4') {
                $error_response['status_code'] = '4-Missing payload';
            } else if ($error_response['status_code'] == '5') {
                $error_response['status_code'] = '5-Invalid token size';
            } else if ($error_response['status_code'] == '6') {
                $error_response['status_code'] = '6-Invalid topic size';
            } else if ($error_response['status_code'] == '7') {
                $error_response['status_code'] = '7-Invalid payload size';
            } else if ($error_response['status_code'] == '8') {
                $error_response['status_code'] = '8-Invalid token';
            } else if ($error_response['status_code'] == '255') {
                $error_response['status_code'] = '255-None (unknown)';
            } else {
                $error_response['status_code'] = $error_response['status_code'] . '-Not listed';
            }

            echo "\r\n".'<b>+ + + + + + ERROR</b> Response Command:<b>' . $error_response['command'] . '</b>&nbsp;&nbsp;&nbsp;Identifier:<b>' . $error_response['identifier'] . '</b>&nbsp;&nbsp;&nbsp;Status:<b>' . $error_response['status_code'] . '</b>'."\r\n";
            echo 'Identifier is the rowID (index) in the database that caused the problem, and Apple will disconnect you from server. To continue sending Push Notifications, just start at the next rowID after this Identifier.'."\r\n";

            return true;
        }
        return false;
    }
}