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

if(is_file('lastCheck.date')) {
    $lastDate = file_get_contents('lastCheck.date');
} else {
    $lastDate = 0;
}

$lastDate = date('YmdHis00',$lastDate);
$change = false;
$iteration = 0;

while($change == false && $iteration <= 60){

    $reqTickets = 'SELECT COUNT(*) as cpt FROM taches WHERE ModifTms > \''.$lastDate.'\';';
    echo $reqTickets.PHP_EOL;
    $q = $sql_handle->query($reqTickets);
    $cpt = $q->fetch();
    if($cpt['cpt']){
        $change = true;
        $reqTickets = 'SELECT * FROM taches WHERE ModifTms > \'' . $lastDate . '\' ORDER BY NumeroTicket;';
        echo $reqTickets.PHP_EOL;
        $q = $sql_handle->query($reqTickets);
        $tickets = $q->fetchAll(PDO::FETCH_ASSOC);
    }

    $reqActions = 'SELECT COUNT(*) as cpt FROM actions WHERE ModifTms > \''.$lastDate.'\';';
    echo $reqActions.PHP_EOL;
    $q = $sql_handle->query($reqActions);
    $cpt = $q->fetch();
    if($cpt['cpt']){
        $change = true;
        $reqActions = 'SELECT * FROM actions WHERE ModifTms > \'' . $lastDate . '\' ORDER BY Id;';
        echo $reqActions.PHP_EOL;
        $q = $sql_handle->query($reqActions);
        $acts = $q->fetchAll(PDO::FETCH_ASSOC);
    }

    file_put_contents('lastCheck.date',time());


    if($change){
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

        echo 'Curl Initialisé'.PHP_EOL;

        if(count($tickets)){
            echo 'Tickets :'.PHP_EOL;
            foreach($tickets as $t){
                $props = array();
                foreach($tFields as $tf){
                    if(!empty($t[$tf]))
                        $props[$tf] = $t[$tf];
                }

                echo '-'.$t['NumeroTicket'].PHP_EOL;
                if($t['ModifTms'] == $t['CreatTms']){
                    $url = 'http://api.gestion.abtel.fr/gestion/tache';
                    $method = 'POST';
                } else {
                    $url = 'http://api.gestion.abtel.fr/gestion/tache/'.$t['NumeroTicket'];
                    $method = 'PATCH';
                }


                curl_setopt($curl_handle,CURLOPT_URL,$url);
                curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $method);
                $data =json_encode(array('API_KEY'=> $apiKey,'AUTH_TOKEN'=>$api_token,"params"=>array('data' => $props)));
                curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data))
                );

                $ret = json_decode(curl_exec($curl_handle),true);
                if($ret && $ret['success']){
                    echo 'Ticket '.$t['NumeroTicket'].' créé avec succès'.PHP_EOL;
                }else{
                    echo 'Erreur lors de la création du ticket '.$t['NumeroTicket'].PHP_EOL;
                    file_put_contents('/tmp/erreurticket',$t['NumeroTicket'].PHP_EOL,8);
                    print_r($method);
                    print_r($url);
                    print_r($data);
                    print_r($ret);
                    echo PHP_EOL;
                }
            }
        }

        if(count($acts)){
            echo 'Actions :'.PHP_EOL;
            foreach($acts as $a){
                $props = array();
                foreach($aFields as $af){
                    if(!empty($a[$af]))
                        $props[$af] = $a[$af];
                }

                echo '-'.$a['Id'].PHP_EOL;
                if($a['ModifTms'] == $a['CreatTms']){
                    $url = 'http://api.gestion.abtel.fr/gestion/action';
                    $method = 'POST';
                } else {
                    $url = 'http://api.gestion.abtel.fr/gestion/action/'.$a['Id'];
                    $method = 'PATCH';
                }


                curl_setopt($curl_handle,CURLOPT_URL,$url);
                curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $method);
                $data =json_encode(array('API_KEY'=> $apiKey,'AUTH_TOKEN'=>$api_token,"params"=>array('data' => $props)));
                curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data))
                );

                $ret = json_decode(curl_exec($curl_handle),true);

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

    } else {
        usleep(600000);
        $iteration++;
    }
}



