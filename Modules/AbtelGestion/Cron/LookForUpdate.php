<?php
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
    $q = $sql_handle->query($reqTickets);
    $cpt = $q->fetch();
    if($cpt['cpt']){
        $change = true;
        $reqTickets = 'SELECT * FROM taches WHERE ModifTms > \'' . $lastDate . '\' ORDER BY NumeroTicket;';
        $q = $sql_handle->query($reqTickets);
        $tickets = $q->fetchAll(PDO::FETCH_ASSOC);
    }

    $reqActions = 'SELECT COUNT(*) as cpt FROM actions WHERE ModifTms > \''.$lastDate.'\';';
    $q = $sql_handle->query($reqActions);
    $cpt = $q->fetch();
    if($cpt['cpt']){
        $change = true;
        $reqActions = 'SELECT * FROM actions WHERE ModifTms > \'' . $lastDate . '\' ORDER BY Id;';
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

        if(count($tickets)){
            foreach($tickets as $t){
                if($t['ModifTms'] == $t['CreatTms']){
                    $url = 'http://api.gestion.abtel.fr/gestion/tache';
                    $method = 'POST';
                } else {
                    $url = 'http://api.gestion.abtel.fr/gestion/tache/'.$t['NumeroTicket'];
                    $method = 'PATCH';
                }


                curl_setopt($curl_handle,CURLOPT_URL,$url);
                curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $method);
                $data =json_encode(array('API_KEY'=> $apiKey,'AUTH_TOKEN'=>$api_token,"params"=>array('data' => $t)));
                curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data))
                );

                $ret = json_decode(curl_exec($curl_handle),true);
            }
        }

        if(count($acts)){
            foreach($acts as $a){
                if($a['ModifTms'] == $a['CreatTms']){
                    $url = 'http://api.gestion.abtel.fr/gestion/action';
                    $method = 'POST';
                } else {
                    $url = 'http://api.gestion.abtel.fr/gestion/action/'.$a['Id'];
                    $method = 'PATCH';
                }


                curl_setopt($curl_handle,CURLOPT_URL,$url);
                curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $method);
                $data =json_encode(array('API_KEY'=> $apiKey,'AUTH_TOKEN'=>$api_token,"params"=>array('data' => $t)));
                curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data))
                );

                $ret = json_decode(curl_exec($curl_handle),true);
            }
        }

    } else {
        usleep(600000);
        $iteration++;
    }
}



