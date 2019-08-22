<?php
session_write_close();
$tmsStart = time() + 3600;

$action = Sys::getOneData('AbtelGestion', 'Entite/Nom=actions');
$fields = $action->getChildren('Champ');
$aFields = array();
foreach ($fields as $f) {
    $aFields[] = $f->Nom;
}

$apiKey = '3a497a64e0-5c13b60a2e9e8';


//Ouverture connection pdo
$sql_handle = new PDO('mysql:host=127.0.0.1;dbname=gestion', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$sql_handle->query("SET AUTOCOMMIT=1");
$sql_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$reqActions = 'SELECT * FROM actions WHERE NumeroTicket > \'I00000\' ORDER BY NumeroTicket;';//'SELECT * FROM actions WHERE CreatTms > \'2018010100000000\' ORDER BY Id;';
$q = $sql_handle->query($reqActions);
$acts = $q->fetchAll(PDO::FETCH_ASSOC);

//Clean mémoire
$sql_handle = null;
$q = null;

//on note la dernière fois qu'on a checké
file_put_contents('lastCheckAct.date', time());

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

$curl_handle = null;
$ret = null;

$cpt = count($acts);
if ($cpt) {
    $cptr = 0;
    $pids = array();
    $statuses = array();
    foreach ($acts as $a) {
        $cptr++;
        echo date('H:i:s', time() - $tmsStart) . ' > ******** ' . $cptr . ' / ' . $cpt . ' : ' . $a['Id'] .'-'. $a['NumeroTicket'] . ' ********' . PHP_EOL;
        switch ($pid = pcntl_fork()) {
            case -1:
                echo date('H:i:s', time() - $tmsStart) . ' > Erreur lors de la création du process pour le client ' . $c['Code'] . PHP_EOL;
                file_put_contents('/tmp/erreurclient', $c['Code'] . PHP_EOL, 8);
                // @fail
                break;
            case 0:
                // @child: Include() misbehaving code here
                $GLOBALS['Systeme']->connectSQL(true);
                Sys::$Modules['Systeme']->Db->clearLiteCache();
                unset($acts);

                $props = array();
                foreach ($aFields as $af) {
                    if (!empty($a[$af]) || $a[$af] === '0')
                        $props[$af] = $a[$af];
                }

                $url = 'http://api.gestion.abtel.fr/gestion/action';
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

                if ($ret && $ret['success']) {
                    echo date('H:i:s', time() - $tmsStart) . ' > Action ' . $a['Id'] . ' créée avec succès' . PHP_EOL;
                } else {
                    $err = true;
                    if ($ret && $ret["error_description"] && is_array($ret["error_description"])) {
                        foreach ($ret["error_description"] as $err) {
                            if (!empty( $err['Prop']) && $err['Prop'] == 'Id' && strpos($err['Message'], "__ALREADY_EXISTS__")) { //Cas ou l'action exsite déjà
                                echo date('H:i:s', time() - $tmsStart) . ' > Action ' . $a['Id'] . ' déjà existant, mise à jour.' . PHP_EOL;
                                $url = 'http://api.gestion.abtel.fr/gestion/action/' . rawurlencode($a['Id']);
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
                                if ($ret2 && !empty($ret2['success'])) {
                                    echo date('H:i:s', time() - $tmsStart) . ' > Action ' . $a['Id'] . ' mise à jour avec succès' . PHP_EOL;
                                    $err = false;
                                } else {
                                    $ret = $ret2;
                                }
                                break;
                            }
                        }
                    }
                    if ($err) {
                        echo date('H:i:s', time() - $tmsStart) . ' > Erreur lors de la création de l\'action ' . $a['Id'] . PHP_EOL;
                        file_put_contents('/tmp/erreuraction', $a['Id'] . PHP_EOL, 8);
                        //print_r($url); echo PHP_EOL;
                        //print_r($data); echo PHP_EOL;
                        //print_r($ret);
                        file_put_contents('/tmp/erreuractionDetail', '+++++++++++++++++++++ ' . date('H:i:s', time()) . '  +++++++++++++++++++++++++++  ' . $a['Id'] . PHP_EOL, 8);
                        file_put_contents('/tmp/erreuractionDetail', print_r($url, true) . PHP_EOL, 8);
                        file_put_contents('/tmp/erreuractionDetail', print_r($data, true) . PHP_EOL, 8);
                        file_put_contents('/tmp/erreuractionDetail', print_r($ret, true) . PHP_EOL, 8);
                        file_put_contents('/tmp/erreuractionDetail', PHP_EOL, 8);
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




