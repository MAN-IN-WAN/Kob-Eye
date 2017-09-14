<?php

require_once(__DIR__.'/Zabbix/ZabbixApi.class.php');

class Zabbix {

    //const RMT_HOST = '10.0.97.1';
    const RMT_HOST = '10.0.189.9';
    const RMT_PORT = 5555;
    const USR = 'ZSA';
    const PASS = 'CHAp*awR_7re';

    //Connexion à l'api zabbix
    private static function connect(){
        $zab = new \ZabbixApi\ZabbixApi('http://'.self::RMT_HOST.'/zabbix/api_jsonrpc.php',self::USR,self::PASS,self::USR,self::PASS);
        return $zab;
    }

    public static function test(){
        $zab = self::connect();

        $hosts = $zab->hostGet(array(
            'output' => 'extend',
            'templateids' => '11431',
            //'hostids' => '11476',
            'selectInterfaces' => array('interfaceid','dns','ip','port'),
            //'selectItems'=> array('name','lastvalue')
            "selectInventory" => array('os','hardware')
        ));

        echo '<pre>';
        foreach($hosts as $h){
            print_r($h);
        }
        echo'<pre>';
    }

    //Recupère les données d'un item depui une date donnée jusqu'a une date donnée
    public static function getGraphDataDuration($itemIds,$end = null,$start = null){
        if(!$end)
            $end = time();
        if(!$start)
            $start = $end - 3600;



        $zab = self::connect();

        $histories = $zab->historyGet(array(
            'output' => 'extend',
            'history' => '0',
            //'hostids' => $hostIds,
            'itemids' => $itemIds,
            'time_from'=> $start,
            'time_till' => $end,
            'sortfield' => 'clock'
        ));


        $data = array();
        echo '<pre>';
        foreach($histories as $h){
            print_r($h);


        }
        echo'<pre>';
    }

    //Recupères les x dernières donnés d'un item
    public static function getLastGraphData($itemIds,$limit =1){
        $zab = self::connect();

        $histories = $zab->historyGet(array(
            'output' => 'extend',
            'history' => '0',
            //'hostids' => $hostIds,
            'itemids' => $itemIds,
            'limit' => $limit,
            'sortfield' => 'clock',
            'sortorder' => 'DESC'
        ));


        $data = array();
        echo '<pre>';
        foreach($histories as $h){
            print_r($h);


        }
        echo'</pre>';
    }

    //Recupère les items liés à un host
    public static function getHostItems($hostIds,$search = null){
        if($search === null)
            return false;
        if(!is_array($search))
            $search = array($search);

        $zab = self::connect();
;
        $items = $zab->itemGet(array(
            'output' => 'extend',
            'history' => '0',
            'hostids' => $hostIds,
        ));


        $data = array();
        foreach($items as $i) {
            foreach ($search as $s){
                if (strpos($i->name, $s) !== false) {
                    array_push($data, $i);
                    continue 2;
                }
            }
        }

        echo '<pre>';
            print_r($data);
        echo '</pre>';

        return $data;
    }

    //Récupère un host race à son uuid
    public static function getHostFromUuid($uuid){

        $zab = self::connect();

        $host = null;
        $hosts = $zab->hostGet(array(
            "selectInventory" => array('hardware'),
            "searchInventory" => array('hardware'=>$uuid),
            "selectGroups" => 'extend',
            'selectParentTemplates' => 'extend'

        ));
        if(count($hosts)>0){
            $host = $hosts[0];
//            echo '<pre>';
//            print_r($host);
            return $host;
        }

        return false;
    }

    //Récupère les Triggers problématiques
    public static function getProblems($object =  null){
        if (isset($object->groupid))  {
            $params = array(
                'groupids'=>$object->groupid
            );
        } elseif (isset($object->templateid))  {
            $params = array(
                'templateids'=>$object->templateid
            );
        } elseif (isset($object->hostid))  {
            $params = array(
                'hostids'=>$object->hostid
            );
        } elseif (isset($object->applicationid))  {
            $params = array(
                'applicationid'=>$object->applicationids
            );
        } elseif (isset($object->itemid))  {
            $params = array(
                'itemids'=>$object->itemid
            );
        } elseif (isset($object->triggerid))  {
            $params = array(
                'triggerids'=>$object->triggerid
            );
        } else {
            $params = array();
        }
        $params['output'] = 'extend';
        $params['filter'] = array('value'=>1); //trigger mode PROBLEM
        $params['sortfield'] = "priority";
        $params['sortorder'] = "DESC";
        $params['expandDescription'] = 'true';
        $params['selectHosts'] = 'extend';
        $params['selectItems'] = 'extend';


        $zab = self::connect();

        $trigs = $zab->triggerGet($params);

        echo '<pre>';
        foreach($trigs as $t){
            print_r($t);


        }
        echo'</pre>';

        return false;
    }

    //Recupère la liste des groupes d'host
    public static function getGroups()
    {
        $zab = self::connect();

        $groups = $zab->hostgroupGet(array(
            'real_hosts'=>'true',
            "output"=>array("name"),
            "sortfield"=>"name"
        ));

        foreach ($groups as $g) {
            echo '<pre>';
            print_r($g);
        }
    }

    //Recupère ls groupe d'host du client
    public static function getClientGroup($name)
    {
        $zab = self::connect();

        $groups = $zab->hostgroupGet(array(
            'real_hosts'=>'true',
            "output"=>'extend',
            "filter"=>array("name"=>$name)
        ));

        if(sizeof($groups)){
//            echo '<pre>';
//            print_r($groups);
            return $groups[0];
        }

        return false;
    }


    //Cale les hosts zabbix dans le groupe client auquel il appartient
    public static function updateGroup($cli, $uuid){

        $zab = self::connect();

        $host = null;
        $hosts = $zab->hostGet(array(
            "selectInventory" => array('hardware'),
            "searchInventory" => array('hardware'=>$uuid),
            "selectGroups" => 'extend'
        ));
        if(count($hosts)>0){
            $host = $hosts[0];
        } else {
            return false;
        }


        $groups = $zab->hostgroupGet(array());
        $group = null;
        foreach($groups as $g){
            $name = strtolower($g->name);
            $cli = strtolower($cli);

            if(strcmp($name,$cli) === 0){
                $group = $g;
                break;
            }
        }

        if(!$group){
            $grp = $zab->hostgroupCreate(array(
                'name' => ucfirst($cli)
            ));
            //print_r($grp);
            $group = $zab->hostgroupGet(array(
                'groupids' => $grp->groupids
            ));
            $group = $group[0];
        }

        if(isset($group->groupid)){
            $hgroups = $host->groups;
            array_push($hgroups,$group);

            $uHost = $zab->hostUpdate(array(
                'hostid' => $host->hostid,
                'groups' => $hgroups
            ));
            //print_r($uHost);
            return $uHost;
        }




        return false;
    }


    //Disable les hosts dont la machine est hors ligne dans la parc et qui sont des postes
    public static function disableOffline($uuids){
        if(!$uuids ) return false;
        if(!is_array($uuids)) $uuids = array($uuids);

        $zab = self::connect();

        //On recup les hosts a modifier
        foreach($uuids as $uuid){
            $host = self::getHostFromUuid($uuid);
            if(!is_object($host))
                continue;

            //On verifie qu'il est bien enregistré comme poste
            foreach ($host->parentTemplates as $tpl){
                if($tpl->templateid == 11523){ //Template OS Windows active Poste

                    //TODO : Eventuellement le mettre dans un groupe disabled
                    $zab->hostUpdate(array(
                        'hostid' => $host->hostid,
                        'status' => 1
                    ));
                    break;
                }
            }
        }

    }
    //Disable les hosts dont la machine est hors ligne dans la parc et qui sont des postes
    public static function disableOfflineName($names){
        if(!$names ) return false;
        if(!is_array($names)) $names = array($names);

        $zab = self::connect();

        //On recup les hosts a modifier
        foreach($names as $name){
            $hosts = $zab->hostGet(array(
                'selectParentTemplates' => 'extend',
                "filter" => array('name'=>$name)
            ));
            if(count($hosts)>0){
                $host = $hosts[0];
            } else{
                continue;
            }
//            echo PHP_EOL.'++++++++++++'.PHP_EOL;
//            echo PHP_EOL.$name.PHP_EOL;
//            print_r($host);

            //On verifie qu'il est bien enregistré comme poste
            foreach ($host->parentTemplates as $tpl){
                if($tpl->templateid == 11523){ //Template OS Windows active Poste

                    //TODO : Eventuellement le mettre dans un groupe disabled
                    $zab->hostUpdate(array(
                        'hostid' => $host->hostid,
                        'status' => 1
                    ));

                    break;
                }
            }
        }

    }

    //Enable les hosts dont la machine est en ligne dans la parc et qui sont des postes
    public static function enableOnline($uuids){
        if(!$uuids ) return false;
        if(!is_array($uuids)) $uuids = array($uuids);

        $zab = self::connect();

        //On recup les hosts a modifier
        foreach($uuids as $uuid){
            $host = self::getHostFromUuid($uuid);
            if(!$host) return false;
            //On verifie qu'il est bien enregistré comme poste
            foreach ($host->parentTemplates as $tpl){
                if($tpl->templateid == 11523){ //Template OS Windows active Poste
                    //TODO : Eventuellement le sortir du groupe disabled
                    $zab->hostUpdate(array(
                        'hostid' => $host->hostid,
                        'status' => 0
                    ));
                    break;
                }
            }
        }

    }

    //Disable les hosts dont la machine est hors ligne dans la parc et qui sont des postes
    public static function enableOnlineName($names){
        if(!$names ) return false;
        if(!is_array($names)) $names = array($names);

        $zab = self::connect();

        //On recup les hosts a modifier
        foreach($names as $name){
            $hosts = $zab->hostGet(array(
                'selectParentTemplates' => 'extend',
                "filter" => array('name'=>$name)
            ));
            if(count($hosts)>0){
                $host = $hosts[0];
            } else{
                continue;
            }

            //On verifie qu'il est bien enregistré comme poste
            foreach ($host->parentTemplates as $tpl){
                if($tpl->templateid == 11523){ //Template OS Windows active Poste

                    //TODO : Eventuellement le sortir du groupe disabled
                    $zab->hostUpdate(array(
                        'hostid' => $host->hostid,
                        'status' => 0
                    ));

                    break;
                }
            }
        }

    }

    /**
     * createUser
     * creation d'un utilisateur admin
     */
    public static function createUser($name,$params) {
        if(!$name ) return false;
        $zab = self::connect();

        return $zab->userCreate($params, $arrayKeyProperty='');
    }

    /**
     * updateUser
     * creation d'un utilisateur admin
     */
    public static function updateUser($params) {
        $zab = self::connect();
        return $zab->userUpdate($params);
    }

    /**
     * getUser
     * mise à jour d'un utilisateur admin
     */
    public static function getUser($params) {
        $zab = self::connect();
        return $zab->userGet($params);
    }
    /**
     * deleteUser
     * suppression d'un utilisateur admin
     */
    public static function deleteUser($params) {
        $zab = self::connect();
        return $zab->userDelete($params);
    }
    /**
     * getInterface
     * récupère les interfaces active d'un poste
     */
    public static function getInterface($name){
        $zab = self::connect();
        $hosts = $zab->hostGet(array(
            'selectParentTemplates' => 'extend',
            "filter" => array('name'=>$name)
        ));
        if (!sizeof($hosts)) return false;
        return $zab->hostinterfaceGet(array(
            "output" => "extend",
            "hostids" => $hosts[0]->hostid
        ));
    }
    //Cale les hosts zabbix dans le groupe client auquel il appartient
    /*public static function updateUser($login, $pass, $mail = "support@abtel.fr"){

        $zab = self::connect();

        $usr = null;
        $usrs = $zab->userGet(array(
            "output"=>'extend',
            "filter"=>array("alias"=>$login)
        ));
        if(count($usrs)>0){
            $usr = $usrs[0];
            $zab->userUpdate(array(
                "userid"=>$usr->userid,
                "passwd"=>$pass
            ));
            //Eventuellement mettre a jour l'adresse mail mais ce demande de recup les media liés au user pour les maj
        } else {
            $zab->userCreate(array(
                "alias"=> $login,
                "type"=> 2,
                "passwd"=>$pass,
                "usrgrps"=>array(
                    array("usrgrpid"=> "7")//Groupe Zabbix Administrators
                ),
                "user_medias" => array(
                    array(
                        "mediatypeid"=> "1", //Email
                        "sendto"=> $mail,
                        "active"=> 1,
                        "severity"=> 63,
                        "period"=> "1-7,00:00-24:00"
                    )
                )
            ));
        }

        return false;
    }*/


}