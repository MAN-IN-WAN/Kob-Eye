<?php
session_write_close();
$tmsStart = time()+3600;

$items = array(
    "clients"=>array(
        "parcName"=>"Clients",
        "dateFile"=>"lastCheckCli.date",
        "identifier"=>"Code",
        "request"=> 'SELECT * FROM tiers WHERE ModifTms > \'__DATE__\' ORDER BY Code;',
        "menu"=>'client'
    ),
    "taches"=>array(
        "parcName"=>"Tickets",
        "dateFile"=>"lastCheckTask.date",
        "identifier"=>"NumeroTicket",
        "request"=> 'SELECT * FROM taches WHERE ModifTms > \'__DATE__\' ORDER BY NumeroTicket;',
        "menu"=>'tache'
    ),
    "actions"=>array(
        "parcName"=>"Actions",
        "dateFile"=>"lastCheckAct.date",
        "identifier"=>"Id",
        "request"=> 'SELECT * FROM actions WHERE ModifTms > \'__DATE__\' ORDER BY Id;',
        "menu"=>'action'
    )
);


$apiKey = '3a497a64e0-5c13b60a2e9e8';


//Ouverture connection pdo
$sql_handle = new PDO('mysql:host=127.0.0.1;dbname=gestion', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$sql_handle->query("SET AUTOCOMMIT=1");
$sql_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//Ouverture connection curl
$curl_handle = curl_init('http://api.gestion.abtel.fr' );
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "POST");
$data = json_encode(array('API_KEY' => $apiKey, 'login' => 'api_gestion', 'pass' => 'D4nsT0n208'));
curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data))
);
curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
$ret = json_decode(curl_exec($curl_handle), true);
$api_token = $ret['auth_token'];

$curl_handle = null;
$ret = null;

foreach($items as $name=>$params){
    echo '/***************************** '.strtoupper($name).' **********************************/'.PHP_EOL;

    //Recupération des champs
    $object = Sys::getOneData('AbtelGestion','Entite/Nom='.$name);
    $fields = $object->getChildren('Champ');
    $oFields = array();
    foreach($fields as $f){
        $oFields[] = $f->Nom;
    }

    //Recup date limite
    if(is_file($params["dateFile"])) {
        $lastDate = file_get_contents($params["dateFile"]);
    } else {
        $lastDate = 0;
    }
    //Overlap sur une minute
    $lastDate -= 60;
    //Conversion format gestion
    $lastDate = date('YmdHis00',$lastDate);
    //Remplacement dans la requete
    $request = str_replace('__DATE__',$lastDate,$params["request"]);
    //echo "Request : ".$request; //log ...
    //Requetage
    $q = $sql_handle->query($request);
    $objs = $q->fetchAll(PDO::FETCH_ASSOC);

    file_put_contents($params["dateFile"],time());
    $cpt = count($objs);
    if ($cpt) {
        echo date('H:i:s',time() - $tmsStart).' > --------> '.$params["parcName"].' <--------'.PHP_EOL;
        $cptr = 0;
        $pids = array();
        $statuses = array();
        foreach($objs as $o){
            $cptr++;

            switch ($pid = pcntl_fork()) {
                case -1:
                    echo date('H:i:s', time() - $tmsStart) . ' > Erreur lors de la création du process pour l\'objet '. $o[$params['identifier']] . PHP_EOL;
                    file_put_contents('/tmp/erreurclient', $o[$params['identifier']] . PHP_EOL , 8);
                    // @fail
                    break;
                case 0:
                    // @child: Include() misbehaving code here
                    $GLOBALS['Systeme']->connectSQL(true);
                    Sys::$Modules['Systeme']->Db->clearLiteCache();
                    unset($objs);

                    $props = array();
                    foreach($oFields as $of){
                        if(!empty($o[$of]) || $o[$of] === '0' )
                            $props[$of] = $o[$of];
                    }

                    echo date('H:i:s',time() - $tmsStart).' > ******** '.$cptr.' / '.$cpt.' : '.$o[$params['identifier']].' ********'.PHP_EOL;
                    $url = 'http://api.gestion.abtel.fr/gestion/'.$params['menu'];
                    $method = 'POST';
                    //Ouverture connection curl
                    $curl_handle = curl_init($url);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $method);
                    $data = json_encode(array('API_KEY' => $apiKey, 'AUTH_TOKEN' => $api_token, "params" => array('data' => $props)));
                    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($data))
                    );
                    $rettemp = curl_exec($curl_handle);
                    $ret = json_decode($rettemp, true);
                    unset($rettemp);

                    if($ret && $ret['success']){
                        echo date('H:i:s',time() - $tmsStart).' > '.$params["parcName"].' '.$o[$params['identifier']].' créé(e) avec succès'.PHP_EOL;
                    }else{
                        $err = true;
                        if($ret && $ret["error_description"]){
                            foreach($ret["error_description"] as $err){
                                if($err['Prop'] == $params['identifier'] && strpos($err['Message'],"__ALREADY_EXISTS__")){ //Cas ou l'action exsite déjà
                                    echo date('H:i:s',time() - $tmsStart).' > '.$params['parcName'].' '.$params['identifier'].' déjà existant, mise à jour.'.PHP_EOL;
                                    curl_setopt($curl_handle,CURLOPT_URL,'http://api.gestion.abtel.fr/gestion/'.$params['menu'].'/'.rawurlencode($o[$params['identifier']]));
                                    curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'PATCH');
                                    $data =json_encode(array('API_KEY'=> $apiKey,'AUTH_TOKEN'=>$api_token,"params"=>array('data' => $props)));
                                    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
                                    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                                            'Content-Type: application/json',
                                            'Content-Length: ' . strlen($data))
                                    );
                                    $rettemp = curl_exec($curl_handle);
                                    $ret2 = json_decode($rettemp, true);
                                    if($ret2 && $ret2['success']){
                                        echo date('H:i:s',time() - $tmsStart).' > '.$params['parcName'].' '.$o[$params['identifier']].'  à jour.'.PHP_EOL;
                                        $err = false;
                                    } else {
                                        $ret = $ret2;
                                    }
                                    break;
                                }
                            }
                        }
                        if($err) {
                            echo date('H:i:s',time() - $tmsStart).' > Erreur lors de la création : ' . $params["parcName"] . ' ' . $o[$params['identifier']] . PHP_EOL;
                            file_put_contents('/tmp/erreurupdate', $params["parcName"] . ' - ' . $o[$params['identifier']] . PHP_EOL, 8);
                            //print_r($url); echo PHP_EOL;
                            //print_r($data); echo PHP_EOL;
                            //print_r($ret);
                            file_put_contents('/tmp/erreurupdateDetail', '+++++++++++++++++++++ ' . date('H:i:s', time()) . '  +++++++++++++++++++++++++++  ' . $params["parcName"] . ' - ' . $o[$params['identifier']] . PHP_EOL, 8);
                            file_put_contents('/tmp/erreurupdateDetail', print_r($url, true) . PHP_EOL, 8);
                            file_put_contents('/tmp/erreurupdateDetail', print_r($data, true) . PHP_EOL, 8);
                            file_put_contents('/tmp/erreurupdateDetail', print_r($ret, true) . PHP_EOL, 8);
                            file_put_contents('/tmp/erreurupdateDetail', PHP_EOL, 8);
                            echo PHP_EOL;
                        }
                    }
                    curl_close($curl_handle);

                    exit;
                    break;

                default:
                    $GLOBALS['Systeme']->connectSQL(true);
                    $pids[$pid] = true;
                    $statuses[$pid] = null;

                    $sql_handle = new PDO('mysql:host=127.0.0.1;dbname=gestion', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                    $sql_handle->query("SET AUTOCOMMIT=1");
                    $sql_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // @parent
                    break;
            }

            while (count($pids) >= 50) {
                usleep(50);
                while ($dPid = pcntl_waitpid(-1, $statuses[$pid], WNOHANG)) {
                    if ($dPid == -1) break;
                    //echo 'Fin : '.$dPid;
                    unset($pids[$dPid]);
                }
            }
        }

        while (count($pids) > 0) {
            usleep(50);
            while ($dPid = pcntl_waitpid(-1, $statuses[$pid], WNOHANG)) {
                if ($dPid == -1) break;
                //echo 'Fin : '.$dPid.PHP_EOL;
                unset($pids[$dPid]);
            }
        }
    }
}





