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
     * Surcharge de la fonction postInit
     * Après l'authentification de l'utilisateur
     * Toutes les fonctionnalités sont disponibles
     * @void
     */
    function postInit (){
        parent::postInit();
        //chargement des variables globales par défaut pour le module boutique
        $this->initGlobalVars();
    }
    /**
     * Initilisation des variables globales disponibles pour la boutique
     */
    function initGlobalVars(){
        //initialisation magasin si disponible
        $GLOBALS["Systeme"]->registerVar("CurrentUser",Sys::$User);
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
     * Keyword generation
     *
     */
    static public function Keywords() {
        $mens = Sys::$User->Menus;
        $b = new BashColors();
        echo $b->getColoredString("-------------------------------------------------------------\n",'yellow');
        echo $b->getColoredString("-              GENERATION DES PAGES                         -\n",'yellow');
        echo $b->getColoredString("-------------------------------------------------------------\n",'yellow');

        foreach ($mens as $m) {
            $m->Save();
            echo $b->getColoredString($m->Module.' / '.$m->ObjectType.' / '.$m->getFirstSearchOrder().' ( '.$m->Id." )        [ OK ]\n", 'green');
            foreach ($m->Menus as $m2) {
                $m2->Save();
                echo $b->getColoredString($m2->Module.' / '.$m2->ObjectType.' / '.$m2->getFirstSearchOrder()." ( '.$m2->Id.' )        [ OK ]\n", 'green');
            }
        }

        echo $b->getColoredString("-------------------------------------------------------------\n",'yellow');
        echo $b->getColoredString("-              GENERATION DES MOTS CLEFS                    -\n",'yellow');
        echo $b->getColoredString("-------------------------------------------------------------\n",'yellow');

        foreach (Sys::$Modules as $mod){
            echo $b->getColoredString("-- ".$mod->Nom."\n",'cyan');
            foreach (Sys::$Modules[$mod->Nom]->Db->ObjectClass as $o){
                if ($o->browseable) {
                    echo $b->getColoredString("---- ".$o->titre."\n",'cyan');
                    if ($o->isReflexive()) {
                        $tmp = Sys::getData($mod->Nom, $o->titre . '/*',0, 1000000, 'Id', 'ASC');
                        $nb = Sys::getCount($mod->Nom, $o->titre . '/*');
                        $i=1;
                        echo $b->getColoredString("------> RAPPORT total: ".$nb."\n",'red');
                        foreach ($tmp as $t){
                            if ($t->Display) {
                                $t->SaveKeywords();
                                echo $b->getColoredString("------ " . $i . "/" . $nb . " ".$mod->Nom ." " . $o->titre." " . $t->getFirstSearchOrder(). "\n", 'green');
                            }else{
                                echo $b->getColoredString("---DEL " . $i . "/" . $nb . " ".$mod->Nom ." " . $o->titre." " . $t->getFirstSearchOrder(). "\n", 'red');
                                $t->deletePages();
                            }
                            $i++;
                        }
                        $GLOBALS['Systeme']->CommitTransaction();
                        Sys::$Modules[$mod->Nom]->Db->clearLiteCache();
                    } else {
                        $nb = Sys::getCount($mod->Nom, $o->titre . '/*');
                        $i=1;
                        $nbpage = floor($nb/100)+1;
                        echo $b->getColoredString("------> RAPPORT pages:  ".$nbpage." / total: ".$nb."\n",'red');
                        for ($p=0; $p<$nbpage;$p++){
                            $tmp = Sys::getData($mod->Nom, $o->titre,$p*100,100);
                            echo $b->getColoredString("------> PAGE:  ".$p." / ".$nbpage."\n",'red');
                            foreach ($tmp as $t){
                                if ($t->Display) {
                                    //$t->Save();
                                    $t->SaveKeywords();
                                    echo $b->getColoredString("------ " . $i . "/" . $nb . " ".$mod->Nom ." " . $o->titre." " . $t->getFirstSearchOrder(). "\n", 'green');
                                }else{
                                    echo $b->getColoredString("---DEL " . $i . "/" . $nb . " ".$mod->Nom ." " . $o->titre." " . $t->getFirstSearchOrder(). "\n", 'red');
                                    $t->deletePages();
                                }
                                $i++;
                            }
                            $GLOBALS['Systeme']->CommitTransaction();
                            Sys::$Modules[$mod->Nom]->Db->clearLiteCache();
                        }
                    }
                }
            }
        }
        echo $b->getColoredString("-------------------------------------------------------------\n",'yellow');
        echo $b->getColoredString("-                            FIN                            -\n",'yellow');
        echo $b->getColoredString("-------------------------------------------------------------\n",'yellow');
    }
    /**
     * Surcharge de la fonction Check
     * Vérifie l'existence du role PARC_CLIENT et son association à un groupe
     * Sinon génère le ROLE et créé un Group à la racine et lui affecte le ROLE
     * Vérifie l'existence de la tâche planifiée de clean des Events et le crée le cas échéant
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
            $u->Pass = md5('secret');
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
            $u->Pass = md5('21wyisey');
            $u->Mail = 'admin@admin.com';
            $u->Skin = 'AdminV2';
            $u->Actif = true;
            $u->Admin = true;
            $u->addParent($g);
            $u->Save();
        }

        $t = Sys::getCount('Systeme','ScheduledTask/Titre=ClearEvents');
        if (!$t) {
            //creation du groupe public
            $t = genericClass::createInstance('Systeme', 'ScheduledTask');
            $t->Titre = 'ClearEvents';
            $t->Enabled = 1;
            $t->TaskModule = 'Systeme';
            $t->TaskObject = 'Event';
            $t->TaskFunction = 'clearEvents';
            $t->Save();
        }

    }

    static function retrievePassword($email){
        @include_once('Class/Lib/Mail.class.php');
        //recherche du compte
        $u = Sys::getOneData('Systeme','User/Mail='.$email);
        if ($u){
            $np = substr($u->CodeVerif,0,8);

            //On suppose qu'on ne gere pas de domain du style xxxx.co.uk
            $sendingDom = explode('.',Sys::$domain);
            if(sizeof($sendingDom)>2){
                $baseDom = $sendingDom[sizeof($sendingDom) - 2].'.'.$sendingDom[sizeof($sendingDom) - 1];
            }else{
                $baseDom = Sys::$domain;
            }

            $Mail = new Mail();
            $Mail->Subject("Mot de passe oublié ".Sys::$domain);
            $Mail -> From("noreply@".$baseDom);
            $Mail -> ReplyTo("noreply@".$baseDom);
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
//        Systeme::sendNotification($msg,37);
    }

    //backapp
    //$API_ACCESS_KEY = 'AIzaSyCGGUR9EbkicdM7IUXp1l-Z2sHFQCnLp-A';
    // API access key from Google API's Console
    //castanet
    //define('API_ACCESS_KEY', 'AIzaSyD-WPYJ39eWmA2aWzgn6fQF1A5WOv3FG5A');
    //cours
    //$API_ACCESS_KEY = 'AIzaSyBbYtVciuBNkTX2h13sHhAvsjBRCSdtb6U';
    //ecluse
    //define('API_ACCESS_KEY', 'AIzaSyCmaDWG5O2HrKdXm4JCkJPQZAvtwqCljos');

    public static function  sendNotification($msg,$target)
    {

        if ($target > 0) {
            $dev = Sys::getData('Systeme', 'User/' . $target . '/Device/Admin=0&Type=Android');
            $API_ACCESS_KEY = 'AIzaSyCmaDWG5O2HrKdXm4JCkJPQZAvtwqCljos';
        } elseif ($target == "all") {
            $dev = Sys::getData('Systeme', 'Device/Admin=0&Type=Android');
            $API_ACCESS_KEY = 'AIzaSyCmaDWG5O2HrKdXm4JCkJPQZAvtwqCljos';
        } elseif ($target == "admin") {
            $dev = Sys::getData('Systeme', 'Device/Admin=1&Type=Android');
            $API_ACCESS_KEY = 'AIzaSyCmaDWG5O2HrKdXm4JCkJPQZAvtwqCljos';
        }

        $devios = array();
        if ($target > 0) {
            $devios = Sys::getData('Systeme', 'User/' . $target . '/Device/Admin=0&Type=iOS');
        } elseif ($target == "all") {
            $devios = Sys::getData('Systeme', 'Device/Admin=0&Type=iOS');
        } elseif ($target == "admin") {
            $devios = Sys::getData('Systeme', 'Device/Admin=1&Type=iOS');
        }

        //enregistre les changement dans la base pour rendre les modifications disponibles
        if (is_object($GLOBALS['Systeme']->Db[0])) {
            $GLOBALS['Systeme']->Db[0]->query("COMMIT");
            $GLOBALS['Systeme']->Db[0]->query("START TRANSACTION");
        }
        //$pid = pcntl_fork();
        //if (!$pid) die('ca marche po');
        register_shutdown_function('sendNotificationParallel',$dev,$devios,$msg,$API_ACCESS_KEY);
        return;
    }

    /**
     * TACHES PLANIFIES
     */
    public static function runScheduledTask() {
        //intialisation des dates
        $d = time();
        $week = array('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');
        $weekday = $week[date('w',$d)];
        $hour = date('H',$d);
        $minute = intval(date('i',$d));
        $month = intval(date('m',$d));
        $monthday = date('j',$d);
        $tasks = Sys::getData('Systeme','ScheduledTask/Enabled=1&(!Minute=*+Minute='.$minute.'!)&(!Heure=*+Heure='.$hour.'!)&(!Jour=*+Jour='.$monthday.'!)&(!Mois=*+Mois='.$month.'!)&(!(!Lundi=0&Mardi=0&Mercredi=0&Jeudi=0&Vendredi=0&Samedi=0&Dimanche=0!)+(!'.$weekday.'=1!)!)');
        foreach ($tasks as $t) {
            if ($t->TaskId>0){
                //execution objet
                $obj = Sys::getOneData($t->TaskModule,$t->TaskObject.'/'.$t->TaskId);
                $obj->{$t->TaskFunction}();
            }else{
                //execution statique
                call_user_func($t->TaskObject.'::'.$t->TaskFunction);
            }

        }
    }
}

function sendNotificationParallel($dev,$devios,$msg,$API_ACCESS_KEY) {
    $pid = pcntl_fork();
    if (!$pid) die();

    $msg['vibrate'] = 1;
    if (isset($msg['sound'])) unset($msg['sound']);
    /*$msg['soundname'] = 'www/res/raw/sound.mp3';*/
    /*$msg['sound'] = "true";*/
    if (!isset($msg['notify'])) $msg['notify'] = 1;
    if (!isset($msg['alert'])) $msg['alert'] = 1;
    if (!isset($msg['store'])) $msg['store'] = '';
    if (!isset($msg['message'])) $msg['message'] = '';
    $msg['largeIcon'] = 'large_icon';
    $msg['smallIcon'] = 'small_icon';

    //recherche des périphériques à associer.
    //die('envoi utilisateur '.$target.' | '.($target>0));
    /****
     * ANDROID
     */
    $registrationIds = array();
    foreach ($dev as $d) {
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
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        //curl_setopt($ch, CURLOPT_SSLVERSION,3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        echo 'curl --header "Authorization: key='.$API_ACCESS_KEY.'" --header "Content-Type: application/json" -v https://android.googleapis.com/gcm/send -d "'.addslashes(json_encode($fields)).'"';
        $result = curl_exec($ch);
        // Error handling
        if ( curl_errno( $ch ) )
        {
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            echo 'GCM error: '.$API_ACCESS_KEY.' => ' . curl_error( $ch ).' => ' . curl_errno( $ch ).' => '.$http_status;
        }
        curl_close($ch);

        //gestion des erreurs
        $out = json_decode($result);
        if ($out->failure){
            //on doit un supprimer un
            foreach ($out->results as $k=>$r){
                if (isset($r->error)){
                    //suppression du device
                    $dev = Sys::getOneData('Systeme','Device/Key='.$registrationIds[$k]);
                    $dev->Delete();
                }
            }
        }
    }
    /****
     * IOS
     */
    $ctx = stream_context_create();
    // ck.pem is your certificate file
    stream_context_set_option($ctx, 'ssl', 'local_cert', realpath(dirname(__FILE__)).'/../Device/dev.ecluse.pem');
    stream_context_set_option($ctx, 'ssl', 'passphrase', '');
    // Open a connection to the APNS server
    //$gateway = 'ssl://gateway.push.apple.com:2195';
    $gateway = 'ssl://gateway.sandbox.push.apple.com:2195';
    $fp = stream_socket_client(
        $gateway, $err,
        $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
    stream_set_blocking ($fp, 0);

    $apple_expiry = time() + (90 * 24 * 60 * 60);

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


    foreach ($devios as $d) {
        //echo "<li>- sending to $d->Id</li>\r\n";
        $deviceToken = $d->Key;

        $apple_identifier = $d->Id;
        // Encode the payload as JSON
        $payload = json_encode($body);
        // Send it to the server
        // Enhanced Notification
        $msg = pack("C", 1) . pack("N", $apple_identifier) . pack("N", $apple_expiry) . pack("n", 32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n", strlen($payload)) . $payload;

        // SEND PUSH
        fwrite($fp, $msg);
        checkAppleErrorResponse($fp);
    }
    // Workaround to check if there were any errors during the last seconds of sending.
    // Pause for half a second.
    // Note I tested this with up to a 5 minute pause, and the error message was still available to be retrieved
    usleep(500000);

    checkAppleErrorResponse($fp);

    fclose($fp);

    $GLOBALS['Systeme']->Db[0]->query("COMMIT");
}
// FUNCTION to check if there is an error response from Apple
// Returns TRUE if there was and FALSE if there was not
function checkAppleErrorResponse($fp) {
    //byte1=always 8, byte2=StatusCode, bytes3,4,5,6=identifier(rowID).
    // Should return nothing if OK.

    //NOTE: Make sure you set stream_set_blocking($fp, 0) or else fread will pause your script and wait
    // forever when there is no response to be sent.

    $apple_error_response = fread($fp, 6);
    if ($apple_error_response) {
        // unpack the error response (first byte 'command" should always be 8)
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
            $error_response['status_code'] = $error_response['status_code'].'-Not listed';
        }

        echo '<br><b>+ + + + + + ERROR</b> Response Command:<b>' . $error_response['command'] . '</b>&nbsp;&nbsp;&nbsp;Identifier:<b>' . $error_response['identifier'] . '</b>&nbsp;&nbsp;&nbsp;Status:<b>' . $error_response['status_code'] . '</b><br>';

        echo 'Identifier is the rowID (index) in the database that caused the problem, and Apple will disconnect you from server. To continue sending Push Notifications, just start at the next rowID after this Identifier.<br>';

        //elmination des périphériques invalides
        if ($error_response['status_code']=='5'||$error_response['status_code']=='8'){
            //suppression du périphérique
            $dev = Sys::getOneData('Systeme','Device/'.$error_response['identifier']);
            $dev->Delete();
            echo 'suppression du périphérique '.$error_response['identifier']."\r\n";
        }

        return true;
    }
    return false;
}

