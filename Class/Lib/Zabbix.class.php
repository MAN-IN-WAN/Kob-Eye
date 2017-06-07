<?php

require_once(__DIR__.'/Zabbix/ZabbixApi.class.php');

class Zabbix {

    const RMT_HOST = '10.0.97.1';
    const RMT_PORT = 5555;
    const USR = 'ZSA';
    const PASS = 'CHAp*awR_7re';

    private static function connect(){
        $zab = new \ZabbixApi\ZabbixApi('https://'.self::RMT_HOST.'/zabbix/api_jsonrpc.php',self::USR,self::PASS);

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


    public static function getGraphData($itemIds,$end = null,$start = null){
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

    public static function getHostItems($hostIds,$search = null){
        $zab = self::connect();
;
        $items = $zab->itemGet(array(
            'output' => 'extend',
            'history' => '0',
            'hostids' => $hostIds,
        ));


        $data = array();
        if($search === null)
            return false;


        foreach($items as $i){
            if (strpos($i->name,$search) !== false){
                array_push($data,$i);
            }
        }

        echo '<pre>';
            print_r($data);
        echo '</pre>';

        return $data;
    }

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
}