<?php
ini_set('memory_limit','9000M');

$tmsStart = time()+3600;

$client = Sys::getOneData('AbtelGestion','Entite/Nom=clients');
$fields = $client->getChildren('Champ');
$cFields = array();
foreach($fields as $f){
    $cFields[] = $f->Nom;
}

$apiKey = '3a497a64e0-5c13b60a2e9e8';


//Ouverture connection pdo
$sql_handle = new PDO('mysql:host=127.0.0.1;dbname=gestion', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$sql_handle->query("SET AUTOCOMMIT=1");
$sql_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$reqClients = 'SELECT * FROM tiers WHERE EstClient = 1 ORDER BY Id;';
$q = $sql_handle->query($reqClients);
$clis = $q->fetchAll(PDO::FETCH_ASSOC);


//on note la dernière fois qu'on a checké
file_put_contents('lastCheckCli.date', time());

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
$cpt = count($clis);
if ($cpt) {
    $cptr = 0;
    foreach ($clis as $c) {
        $cptr++;
        echo date('H:i:s',time() - $tmsStart).' > ******** '.$cptr.' / '.$cpt.' : '.$c['Code'].' ********'.PHP_EOL;
        $props = array();
        foreach($cFields as $cf){
            if(!empty($c[$cf]) || $c[$cf] === '0')
                $props[$cf] = $c[$cf];
        }

        $url = 'http://api.gestion.abtel.fr/gestion/client';
        $method = 'POST';


        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $method);
        $data = json_encode(array('API_KEY' => $apiKey, 'AUTH_TOKEN' => $api_token, "params" => array('data' => $props)));
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        $rettemp = curl_exec($curl_handle);
        $ret = json_decode($rettemp, true);
        if($ret && $ret['success']){
            echo date('H:i:s',time() - $tmsStart).' > Client '.$c['Code'].' créé avec succès'.PHP_EOL;
        }else{
            $err = true;
            if($ret && $ret["error_description"]){
                foreach($ret["error_description"] as $err){
                    if($err['Prop'] == 'Code' && strpos($err['Message'],"__ALREADY_EXISTS__")){ //Cas ou le client exsite déjà
                        echo date('H:i:s',time() - $tmsStart).' > Client '.$c['Code'].' déjà existant, mise à jour.'.PHP_EOL;
                        $url = 'http://api.gestion.abtel.fr/gestion/client/'.rawurlencode($c['Code']);
                        $method = 'PATCH';
                        curl_setopt($curl_handle, CURLOPT_URL, $url);
                        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $method);
                        $data = json_encode(array('API_KEY' => $apiKey, 'AUTH_TOKEN' => $api_token, "params" => array('data' => $props)));
                        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
                        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Content-Length: ' . strlen($data))
                        );
                        $rettemp = curl_exec($curl_handle);
                        $ret2 = json_decode($rettemp, true);
                        if($ret2 && $ret2['success']){
                            echo date('H:i:s',time() - $tmsStart).' > Client '.$c['Code'].' mis à jour avec succès'.PHP_EOL;
                            $err = false;
                        } else {
                            $ret = $ret2;
                        }
                        break;
                    }
                }
            }
            if($err){
                echo date('H:i:s',time() - $tmsStart).' > Erreur lors de la création du client '.$c['Code'].PHP_EOL;
                file_put_contents('/tmp/erreurclient',$c['Code'].PHP_EOL,8);
                print_r($url); echo PHP_EOL;
                print_r($data); echo PHP_EOL;
                print_r($ret);
                echo PHP_EOL;
            }
        }



    }
}




