<?php
ini_set('memory_limit','9000M');

$tache = Sys::getOneData('AbtelGestion','Entite/Nom=taches');
$fields = $tache->getChildren('Champ');
$tFields = array();
foreach($fields as $f){
    $tFields[] = $f->Nom;
}

$action = Sys::getOneData('AbtelGestion','Entite/Nom=actions');
$fields = $action->getChildren('Champ');
$aFields = array();
foreach($fields as $f){
    $aFields[] = $f->Nom;
}


$apiKey = '3a497a64e0-5c13b60a2e9e8';


//Ouverture connection pdo
$sql_handle = new PDO('mysql:host=127.0.0.1;dbname=gestion', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$sql_handle->query("SET AUTOCOMMIT=1");
$sql_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


//$reqTickets = 'SELECT COUNT(*) as cpt FROM taches;';
//$q = $sql_handle->query($reqTickets);
//$cpt = $q->fetch();
//if ($cpt['cpt']) {
    $reqTickets = 'SELECT * FROM taches WHERE CreatTms > \'2018010100000000\' ORDER BY NumeroTicket;';
    $q = $sql_handle->query($reqTickets);
    $tickets = $q->fetchAll(PDO::FETCH_ASSOC);
//}

//$reqActions = 'SELECT COUNT(*) as cpt FROM actions;';
//$q = $sql_handle->query($reqActions);
//$cpt = $q->fetch();
//if ($cpt['cpt']) {
    $reqActions = 'SELECT * FROM actions WHERE CreatTms > \'2018010100000000\' ORDER BY Id;';
    $q = $sql_handle->query($reqActions);
    $acts = $q->fetchAll(PDO::FETCH_ASSOC);
//}

//on note la dernière fois qu'on a checké
file_put_contents('lastCheck.date', time());

//Ouverture connection curl
$curl_handle = curl_init('http://api.gestion.abtel.fr');
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

if (count($tickets)) {
    foreach ($tickets as $t) {
        $props = array();
        foreach($tFields as $tf){
            $props[$tf] = $t[$tf];
        }

        $url = 'http://api.gestion.abtel.fr/gestion/tache';
        $method = 'POST';


        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $method);
        $data = json_encode(array('API_KEY' => $apiKey, 'AUTH_TOKEN' => $api_token, "params" => array('data' => $props)));
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($curl_handle), true);
        if($ret && $ret['success']){
            echo 'Ticket '.$t['NumeroTicket'].' créé avec succès'.PHP_EOL;
        }else{
            echo 'Erreur lors de la création du ticket '.$t['NumeroTicket'].PHP_EOL;
            file_put_contents('/tmp/erreurticket',$t['NumeroTicket'].PHP_EOL,8);
            print_r($data);
            print_r($ret);
            echo PHP_EOL;
        }
    }
}

if (count($acts)) {
    foreach ($acts as $a) {
        $props = array();
        foreach($aFields as $af){
            if(!empty($a[$af]))
                $props[$af] = $a[$af];
        }

        $url = 'http://api.gestion.abtel.fr/gestion/action';
        $method = 'POST';


        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $method);
        $data = json_encode(array('API_KEY' => $apiKey, 'AUTH_TOKEN' => $api_token, "params" => array('data' => $props)));
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $ret = json_decode(curl_exec($curl_handle), true);
        if($ret && $ret['success']){
            echo 'Action '.$a['Id'].' créée avec succès'.PHP_EOL;
        }else{
            echo 'Erreur lors de la création de l\'action '.$a['Id'].PHP_EOL;
            file_put_contents('/tmp/erreuraction',$a['Id'].PHP_EOL,8);
            print_r($data);
            print_r($ret);
            echo PHP_EOL;
        }
    }
}




