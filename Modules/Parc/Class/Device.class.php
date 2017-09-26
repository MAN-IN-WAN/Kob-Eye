<?php
require_once 'Class/Lib/Zabbix.class.php';

class Device extends genericClass{

    /*
     *
     * @override
     */
    function save(){
        if ($this->Id) {
            $this->checkGuacamoleConnections();
            $port_rdp = 12000 + $this->Id;
            $port_vnc = 22000 + $this->Id;
            $this->ConnectionType = 'R' . $port_rdp . '=localhost:3389,R' . $port_vnc . '=localhost:15900';
        }
        parent::save();
        //checking port redirect
        $this->checkRedirectPort();
        return true;
    }
    private function checkRedirectPort(){
        $ports = $this->getChildren('DevicePort');
        switch ($this->OS){
            case "Windows":
                if (sizeof($ports)>=3)return true;
                //RDP
                $port = genericClass::createInstance('Parc','DevicePort');
                $port->PortRedirectLocal =  12000 + $this->Id;
                $port->PortRedirectDistant =  3389;
                $port->IpRedirectDistant = 'localhost';
                $port->addParent($this);
                $port->Save();
                //VNC
                $port = genericClass::createInstance('Parc','DevicePort');
                $port->PortRedirectLocal =  22000 + $this->Id;
                $port->PortRedirectDistant =  15900;
                $port->IpRedirectDistant = 'localhost';
                $port->addParent($this);
                $port->Save();
                //MSG
                $port = genericClass::createInstance('Parc','DevicePort');
                $port->PortRedirectLocal =  32000 + $this->Id;
                $port->PortRedirectDistant =  15902;
                $port->IpRedirectDistant = 'localhost';
                $port->addParent($this);
                $port->Save();
                break;
        }
    }
    function getVersion($uuid) {
        /*if(!isset($uuid))
            return false;*/
        $prod = true;
        $Commands = "";
        $ConnectionType = "\r\nPorts=";
        //recherche de la machine
        $dev = Sys::getOneData('Parc','Device/Uuid='.$uuid);
        if (!$dev&&isset($uuid)){
            //si machine existe pas on la créé
            $this->getConfig($uuid);
            $dev = Sys::getOneData('Parc','Device/Uuid='.$uuid);
        }
        if ($dev){
            //if(!$dev->Online) Zabbix::enableOnline($dev->Uuid);
            //mise à jour de la machine
            $dev->LastSeen = time();
            $dev->PublicIP = $_SERVER['REMOTE_ADDR'];
            $dev->Online = true;
            $dev->Save();
            if ($dev->ModeTest) $prod=false;
            if (isset($_GET['version'])&&$_GET['version']!=$dev->CurrentVersion){
                $dev->CurrentVersion = $_GET['version'];
                $dev->Save();
            }
            if (isset($_GET['bios'])&&$_GET['bios']!=$dev->CurrentVersion){
                $dev->SerialNumber = $_GET['bios'];
                $dev->Save();
            }
            if (isset($_GET['error'])&&sizeof($_GET['error'])>1){
                $dev->Erreur = true;
                $err = explode(';',$_GET['error']);
                foreach ($err as $e){
                    switch ($e){
                        case 'tunnel':
                            $this->DetailErreur.="\r\n ".date('d/m/Y H:i:s')." Le tunnel a échoué et tente de redémarrer.";
                            break;
                        case 'vnc':
                            $this->DetailErreur.="\r\n ".date('d/m/Y H:i:s')." Le service VNC a échoué et tente de redémarrer.";
                            break;
                        case 'connexion':
                            $this->DetailErreur.="\r\n ".date('d/m/Y H:i:s')." La connexion a été interrompue.";
                            break;
                        default:
                            break;
                    }
                }
                $dev->Save();
            }else{
                $dev->Erreur = false;
                $dev->Save();
            }
            //GEstion des commandes
            if ($dev->RestartTunnel){
                $Commands = "\r\nCommande=tunnel";
                $dev->RestartTunnel = false;
                $dev->Save();
            }
            $ConnectionType .= $dev->ConnectionType;
        }
        //recherche d'un tache à accomplir
        $t = $dev->getChildren('DeviceTask/Enabled=1');
        if (sizeof($t)>0){
            $task = 'http://'.Sys::$domain.'/Parc/DeviceTask/'.$t[0]->Id.'/getTask.json';
        }else $task = '';
        //recherche de la version
        $log = Sys::getOneData('Parc','LogicielVersion/Release='.$prod);
        if (!$log) $log = Sys::getOneData('Parc','LogicielVersion/Release='.!$prod);

        $dirty = '';
        if($dev->ModeTest){
            $ConnectionType='';

            $cos=$dev->getChildren('DevicePort');
            $dirty = "Ports=";
            //TODO : checker les doublons pour modif de mot de passe
            foreach ($cos as $co){
                $ip = $co->IpRedirectDistant!=''?$co->IpRedirectDistant:'localhost';
                $dirty .= 'R'.$co->PortRedirectLocal.'='.$ip.':'.$co->PortRedirectDistant.',';
            }
            $dirty = rtrim($dirty,',');
            $dev->Save();
        }



        return "Version=$log->Version
Install=http://".Sys::$domain."/$log->InstallFile
Service=http://".Sys::$domain."/$log->ServiceFile
Tunnel32=http://".Sys::$domain."/$log->TunnelFile
Tunnel64=http://".Sys::$domain."/$log->TunnelFile64
Vnc32=http://".Sys::$domain."/$log->VncFile
Vnc64=http://".Sys::$domain."/$log->VncFile64
VncDll32=http://".Sys::$domain."/$log->VncDllFile
VncDll64=http://".Sys::$domain."/$log->VncDllFile64
ZabbixAgent32=http://".Sys::$domain."/$log->ZabbixAgent32
ZabbixAgent64=http://".Sys::$domain."/$log->ZabbixAgent64$ConnectionType$Commands
Client=$dev->CodeClient
Computer=$dev->Nom
Type=$dev->DeviceType
Task=$task
".$dirty."
";

        /*Tasklist
            Ports= **Chaine régénérée**   //Reinit les redirection de ports avec les infos fournies





        */




    }

    function getConfig($uuid) {

        if (empty($uuid)) return;
        $exists = Sys::getOneData('Parc','Device/Uuid='.$uuid);
        if ($exists){
            $port_rdp = 12000+$exists->Id;
            $port_vnc = 22000+$exists->Id;
            $exists->Nom = $_GET["name"];
            $exists->Description = $_GET["os"];
            if(isset($_GET["machine"]))
                $exists->DeviceType = ($_GET["machine"]=='station')?'Poste':'Server';
            else $exists->DeviceType = 'Poste';

            if($exists->ModeTest){
                $cos=$exists->getChildren('DevicePort');
                $exists->ConnectionType = "";
                foreach ($cos as $co){
                    $ip = $co->IpRedirectDistant!=''?$co->IpRedirectDistant:'localhost';
                    $exists->ConnectionType .= 'R'.$co->PortRedirectLocal.'='.$ip.':'.$co->PortRedirectDistant.',';
                }
                $exists->ConnectionType = rtrim(',',$exists->ConnectionType);
            }else{
                $exists->ConnectionType = 'R'.$port_rdp.'=localhost:3389,R'.$port_vnc.'=localhost:15900';
            }

            $exists->Save();
            $obj = $exists;
        }else{
            //creation du device
            $obj = genericClass::createInstance('Parc','Device');
            if(!isset($_GET["name"])){
                $obj->Nom = 'Noname_'.$uuid;
            } else{
                $obj->Nom = $_GET["name"];
            }
            if(!isset($_GET["os"])){
                $obj->Description = 'OS non spécifié';
            } else{
                $obj->Description = $_GET["os"];
            }
            if(isset($_GET["machine"]))
                $obj->DeviceType = ($_GET["machine"]=='station')?'Poste':'Server';
            else $obj->DeviceType = 'Poste';
            $obj->Uuid = $uuid;
            //klog::l('$obj',$obj);
            $obj->Save();
            $port_rdp = 12000+$obj->Id;
            $port_vnc = 22000+$obj->Id;
            if ($port_rdp==12000) die('ERROR Device port rdp');
            $obj->ConnectionType = 'R'.$port_rdp.'=localhost:3389,R'.$port_vnc.'=localhost:15900';

            //$obj->addParent('Parc/Client/2');
            $obj->Save();
        }
        //affectation du client
        $client = $_GET["clientid"];
        if (!empty($client)){
            $obj->CodeClient = $client;
            if (is_int($client)) {
                $cli = Sys::getOneData('Parc', 'Client/Id=' . $client);
            }else{
                $cli = Sys::getOneData('Parc','Client/CodeGestion='.$client);
            }
            if ($cli) {
                $obj->addParent($cli);
            }
            $obj->Save();
        }
        return $obj->ConnectionType;
    }

    /**
     * check client name and device hostname
     */
    function checkClient($uuid) {
        //check client
        if (!Sys::getCount('Parc', 'Client/CodeGestion=' . $_GET["client"])){
            return 'client';
        }
        //check hostname
        if (Sys::getCount('Parc', 'Device/Uuid!='.$uuid.'&Nom=' . $_GET["system"])){
            return 'system';
        }
        return 'ok';
    }



    public static function getOffline(){
        //Mise à jour des devices offline
        $devs = Sys::getData('Parc','Device/Online=1&&LastSeen<'.(time()-600));
        foreach ($devs as $d) {
            $d->Online = false;
            $d->Save();
           //TODO Zabbix desactivé le temps de stabiliser l'acces au serveur via proxy
            //Zabbix::disableOffline($d->Uuid);
        }
    }


    private function checkGuacamoleConnections()    {
        $dbGuac = new PDO('mysql:host=10.0.189.12;dbname=guacamole', 'root', 'RsL5pfky', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $dbGuac->query("SET AUTOCOMMIT=1");
        $dbGuac->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //Vérif l'existence du client et le crée le cas échéant
        $cli = $this->getOneParent('Client');
        //klog::l('$cli',$cli);
        if (isset($cli->AccesUser) && $cli->AccesUser != '' && $cli->AccesUser != null && isset($cli->AccesPass) && $cli->AccesPass != '' && $cli->AccesPass != null) {
            $query = "SELECT * FROM `guacamole_user` WHERE username = '" . $cli->AccesUser . "'";
            $q = $dbGuac->query($query);
            $result = $q->fetchALL(PDO::FETCH_ASSOC);
            if (sizeof($result) > 0) {
                $usr = $result[0];
            } else {
                $query = "INSERT INTO `guacamole_user` (username,password_hash,password_date) VALUES ('" . $cli->AccesUser . "',UNHEX(SHA2('" . $cli->AccesPass . "',256)),'" . date("Y-m-d H:i:s") . "')";
                $q = $dbGuac->query($query);

                $query = "SELECT * FROM `guacamole_user` WHERE username = '" . $cli->AccesUser . "'";
                $q = $dbGuac->query($query);
                $result = $q->fetchALL(PDO::FETCH_ASSOC);
                $usr = $result[0];
            }
        } elseif (isset($cli->AccesUser) && $cli->AccesUser != '' && $cli->AccesUser != null && (!isset($cli->AccesPass) || $cli->AccesPass == '' || $cli->AccesPass == null)) {
            $cli->addError(array('Message' => 'La valeur du champ AccesPass est nulle ou non définie alors que le champ AccesUser est défini.', "Prop" => 'AccesPass'));
        }

        if(isset($cli->Nom)){

            $query = "SELECT * FROM `guacamole_connection_group` WHERE connection_group_name = '" . strtoupper(str_replace('\'',' ',$cli->Nom)) . "'";
            $q = $dbGuac->query($query);
            $result = $q->fetchALL(PDO::FETCH_ASSOC);
            if (sizeof($result) > 0) {
                $grp = $result[0];
                $gid = $grp['connection_group_id'];
            } else {
                $query = "INSERT INTO `guacamole_connection_group` (connection_group_name) VALUES ('" . strtoupper(str_replace('\'',' ',$cli->Nom)) . "')";
                $q = $dbGuac->query($query);

                $gid = $dbGuac->lastInsertId();
            }
        }


        //Connection RDP
        if ($this->GuacamoleUrlRdp == "" || $this->GuacamoleUrlRdp == null || $this->GuacamoleIdRdp == "" || $this->GuacamoleIdRdp == null) {

            $query = "INSERT INTO `guacamole_connection` (connection_name,protocol,parent_id,max_connections,max_connections_per_user) VALUES ('" . $this->Nom . "_rdp','rdp',NULL,NULL,NULL)";
            $q = $dbGuac->query($query);
            $lid = $dbGuac->lastInsertId();

            $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'hostname','127.0.0.1')";
            $q = $dbGuac->query($query);
            $port = 12000 + $this->Id;
            $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'port','$port')";
            $q = $dbGuac->query($query);

            $this->GuacamoleIdRdp = $lid;
            $this->GuacamoleUrlRdp = base64_encode($lid . "\0" . 'c' . "\0" . 'mysql');

            $this->Save();
        } else {
            $query = "UPDATE `guacamole_connection` SET connection_name ='" . $this->Nom . "_rdp' WHERE connection_id =$this->GuacamoleIdRdp";
            $q = $dbGuac->query($query);

            if(isset($gid)){
                $query = "UPDATE `guacamole_connection` SET parent_id ='" . $gid . "' WHERE connection_id =$this->GuacamoleIdRdp";
                $q = $dbGuac->query($query);
            }


            $port = 12000 + $this->Id;
            $query = "UPDATE `guacamole_connection_parameter` SET parameter_value = '$port' WHERE connection_id=$this->GuacamoleIdRdp AND parameter_name='port'";
            $q = $dbGuac->query($query);
        }


        //Connection VNC
        if ($this->GuacamoleUrlVnc == "" || $this->GuacamoleUrlVnc == null || $this->GuacamoleIdVnc == "" || $this->GuacamoleIdVnc == null) {

            $query = "INSERT INTO `guacamole_connection` (connection_name,protocol,parent_id,max_connections,max_connections_per_user) VALUES ('" . $this->Nom . "_vnc','vnc',NULL,NULL,NULL)";


            $q = $dbGuac->query($query);
            $lid = $dbGuac->lastInsertId();

            $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'hostname','127.0.0.1')";
            $q = $dbGuac->query($query);
            $port = 22000 + $this->Id;
            $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'port','$port')";
            $q = $dbGuac->query($query);
            $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'password','secret')";
            $q = $dbGuac->query($query);

            $this->GuacamoleIdVnc = $lid;
            $this->GuacamoleUrlVnc = base64_encode($lid . "\0" . 'c' . "\0" . 'mysql');

            $this->Save();
        } else {
            $query = "UPDATE `guacamole_connection` SET connection_name ='" . $this->Nom . "_vnc' WHERE connection_id =$this->GuacamoleIdVnc";
            $q = $dbGuac->query($query);

            if(isset($gid)){
                $query = "UPDATE `guacamole_connection` SET parent_id ='" . $gid . "' WHERE connection_id =$this->GuacamoleIdVnc";
                $q = $dbGuac->query($query);
            }

            $port = 22000 + $this->Id;
            $query = "UPDATE `guacamole_connection_parameter` SET parameter_value = '$port' WHERE connection_id=$this->GuacamoleIdVnc AND parameter_name='port'";
            $q = $dbGuac->query($query);

        }

        if (isset($usr)) {
            $query = "INSERT IGNORE INTO `guacamole_connection_permission` (user_id,connection_id,permission) VALUES ('" . $usr['user_id'] . "','" . $this->GuacamoleIdVnc . "','READ'),('" . $usr['user_id'] . "','" . $this->GuacamoleIdRdp . "','READ')";
            $q = $dbGuac->query($query);
        }

        return true;
    }

    public function createBaseConnection(){
        $ports = explode(',',$this->ConnectionType);
        array_walk($ports,function(&$a){
            $a = ltrim($a,'R');
            $temp = explode('=',$a);
            $a = array('local'=>$temp[0],'distant'=>$temp[1]);
            $a['distant']=explode(':',$a['distant'])[1];
        });


        if($this->GuacamoleIdRdp != "" || $this->GuacamoleIdRdp != null)
            $rdp = $this->getOneChild('DeviceConnexion/GuacamoleId='.$this->GuacamoleIdRdp);

        if($this->GuacamoleIdVnc != "" || $this->GuacamoleIdVnc != null)
            $vnc = $this->getOneChild('DeviceConnexion/GuacamoleId='.$this->GuacamoleIdVnc);


        if(!isset($rdp) || !$rdp){
            $coRdp = genericClass::createInstance('Parc','DeviceConnexion');
            $coRdp->Nom = $this->Nom . "_rdp";
            $coRdp->Type = 'RDP';
            $coRdp->PortRedirectLocal = $ports[0]['local'];
            $coRdp->PortRedirectDistant = $ports[0]['distant'];
            $coRdp->GuacamoleUrl = $this->GuacamoleUrlRdp;
            $coRdp->GuacamoleId = $this->GuacamoleIdRdp;
            $coRdp->addParent($this);
            $coRdp->Save();
        }


        if(!isset($vnc) || !$vnc){
            $coVnc = genericClass::createInstance('Parc','DeviceConnexion');
            $coVnc->Nom = $this->Nom . "_vnc";
            $coVnc->Type = 'VNC';
            $coVnc->PortRedirectLocal = $ports[1]['local'];
            $coVnc->PortRedirectDistant = $ports[1]['distant'];
            $coVnc->GuacamoleUrl = $this->GuacamoleUrlVnc;
            $coVnc->GuacamoleId = $this->GuacamoleIdVnc;
            $coVnc->addParent($this);
            $coVnc->Save();
        }
    }
    /**
     * getIps
     * retourne la liste des ips actives du poste
     */
    public function getIps() {
        $out = Zabbix::getInterface($this->CodeClient.' '.$this->Nom);
        //print_r($out);
        return $out;
    }
}