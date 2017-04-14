<?php
class GestionParc extends genericClass {
        private $dbAbtel = null;
        
        public function __construct($o,$i){
                try{
                        $this->dbAbtel = new PDO('mysql:host=10.0.3.8;dbname=gestion','gestion','',array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                        $this->dbAbtel->query("SET AUTOCOMMIT=1");
                } catch (Exception $e){
                        print_r($e);
                }
                parent::__construct($o,$i);
        }
        
        public function check(){
                $query='DROP TABLE IF EXISTS parc_client ;
                        CREATE TABLE parc_client (pc_Id BIGINT  AUTO_INCREMENT NOT NULL,
                        pc_Nom VARCHAR(255),
                        pc_CodeClient VARCHAR(20),
                        pc_AccesActif BOOL,
                        pc_AccesUser VARCHAR(255),
                        pc_AccesPass VARCHAR(255),
                        pc_Email VARCHAR(255),
                        pc_Tel VARCHAR(255),
                        pc_Fax VARCHAR(255),
                        pc_Dirty BOOL,
                        PRIMARY KEY (pc_Id) ) ENGINE=InnoDB;
                        
                        DROP TABLE IF EXISTS parc_domain ;
                        CREATE TABLE parc_domain (pd_Id BIGINT  AUTO_INCREMENT NOT NULL,
                        pd_Url VARCHAR(255),
                        pd_Dirty BOOL,
                        parc_client_pc_id BIGINT,
                        PRIMARY KEY (pd_Id) ) ENGINE=InnoDB;
                        
                        DROP TABLE IF EXISTS parc_server ;
                        CREATE TABLE parc_server (ps_Id BIGINT  AUTO_INCREMENT NOT NULL,
                        ps_Nom VARCHAR(255),
                        ps_IP VARCHAR(20),
                        ps_NbCpu INT,
                        ps_NbRam INT,
                        ps_EspaceProvisionne INT,
                        ps_Dirty BOOL,
                        parc_client_pc_id BIGINT,
                        PRIMARY KEY (ps_Id) ) ENGINE=InnoDB;
                        
                        DROP TABLE IF EXISTS parc_host ;
                        CREATE TABLE parc_host (ph_Id BIGINT  AUTO_INCREMENT NOT NULL,
                        ph_Nom VARCHAR(255),
                        ph_Commentaires TEXT,
                        ph_Quota INT,
                        ph_Dirty BOOL,
                        parc_server_ps_id BIGINT,
                        parc_client_pc_id BIGINT,
                        PRIMARY KEY (ph_Id) ) ENGINE=InnoDB;
                        
                        DROP TABLE IF EXISTS parc_mail ;
                        CREATE TABLE parc_mail (pm_Id BIGINT  AUTO_INCREMENT NOT NULL,
                        pm_Adresse VARCHAR(255),
                        pm_Quota INT,
                        pm_Used INT,
                        pm_Status INT,
                        pm_Dirty BOOL,
                        parc_client_pc_id BIGINT,
                        parc_server_ps_id BIGINT,
                        PRIMARY KEY (pm_Id) ) ENGINE=InnoDB;
                        ';

                try{
                         $this->dbAbtel->query($query);
                } catch (Exception $e){
                        print_r($e);
                }
                
                $this->sync();
        }
        
        /**
         * sync
         * Synchronise les tables du module parc avec les tables de communications
         * Seules les lignes Dirty = true seront executé en modification sur le module Parc.
         */
        public function sync() {
                echo "SYNC CLIENT \r\n";
                $this->syncClient();
                $this->syncHost();
                $this->syncServer();
                $this->syncDomain();
                $this->syncMail();
                echo "SYNC CLIENT DONE \r\n";
        }
        /**
         * syncClient
         * Synchronise la table client
         */
        private function syncClient() {
        
                //client
                //test du dirty
                $query = 'select * from parc_client where pc_Dirty = 1;';
                $res = $this->dbAbtel->query($query);
                $result = $res->fetchALL ( PDO::FETCH_ASSOC );
                if ($result) foreach ($result as $r) {
                        $o = Sys::getOneData('Parc','Client/'.$r['pc_Id']);
                        $o->Nom = $r['pc_Nom'];
                        $o->CodeClient = $r['pc_CodeClient'];
                        $o->AccesActif = $r['pc_AccesActif'];
                        $o->AccesUser = $r['pc_AccesUser'];
                        $o->AccesPass = $r['pc_AccesPass'];
                        $o->Email = $r['pc_Email'];
                        $o->Tel = $r['pc_Tel'];
                        $o->Fax = $r['pc_Fax'];
                        //Attention ne marche que si connexion LDAP !!!!
                        //$o->Save();
                        echo "\tedit client ".$o->Id."\r\n";
                }
                
                //synchro des données
                $client = Sys::getData('Parc','Client',0,99999);
                $virg = '';
                $query = 'TRUNCATE TABLE parc_client;INSERT INTO parc_client (pc_Id,pc_Nom,pc_CodeClient,pc_AccesActif,pc_AccesUser,pc_AccesPass,pc_Email,pc_Tel,pc_Fax) VALUES ';
                foreach ($client as $c){
                       $query .= $virg.'('.$c->Id.', "'.$c->Nom.'", "'.$c->CodeClient.'", "'.$c->AccesActif.'", "'.$c->AccesUser.'", "'.$c->AccesPass.'", "'.$c->Email.'", "'.$c->Tel.'", "'.$c->Fax.'")';
                       $virg = ',';
                }
                $query .= ';';
                
                $this->dbAbtel->exec($query);
        }
        /**
         * syncServer
         * Synchronise la table server
         */
        private function syncServer() {
        
                //host
                //test du dirty
                $query = 'select * from parc_server where ps_Dirty = 1;';
                $res = $this->dbAbtel->query($query);
                if ($res)
                        $result = $res->fetchALL ( PDO::FETCH_ASSOC );
                if ($result) foreach ($result as $r) {
                        $o = Sys::getOneData('Parc','Server/'.$r['ps_Id']);
                        $o->Nom = $r['ps_Nom'];
                        $o->IP = $r['ps_IP'];
                        $o->NbCpu = $r['ps_NbCpu'];
                        $o->NbRam = $r['ps_NbRam'];
                        $o->EspaceProvisionne = $r['ps_EspaceProvisionne'];
                        //Attention rattache le parent client !!!
                        $o->addParent('Client',$r['parc_client_pc_id']);
                        
                        //Attention ne marche que si connexion LDAP !!!!
                        //$o->Save();
                        echo "\tedit server ".$o->Id."\r\n";
                }
                
                //synchro des données
                $server = Sys::getData('Parc','Server',0,99999);
                
                $virg = '';
                $query = 'TRUNCATE TABLE parc_server;INSERT INTO parc_server (ps_Id,ps_Nom,ps_NbCpu,ps_NbRam,ps_EspaceProvisionne,parc_client_pc_id) VALUES ';
                foreach ($server as $s){
                        $padre = $s->getOneParent('Client');
                        $query .= $virg.'('.$s->Id.', "'.$s->Nom.'", "'.$s->NbCpu.'", "'.$s->NbRam.'", "'.$s->EspaceProvisionne.'", "'.$padre->Id.'")';
                        $virg = ',';
                }
                $query .= ';';
                
                $this->dbAbtel->exec($query);
        }
        
        /**
         * syncHost
         * Synchronise la table host
         */
        private function syncHost() {
        
                //host
                //test du dirty
                $query = 'select * from parc_host where ph_Dirty = 1;';
                $res = $this->dbAbtel->query($query);
                if ($res)
                        $result = $res->fetchALL ( PDO::FETCH_ASSOC );
                if ($result) foreach ($result as $r) {
                        $o = Sys::getOneData('Parc','Host/'.$r['ph_Id']);
                        $o->Nom = $r['ph_Nom'];
                        $o->Commentaires = $r['ph_Commentaires'];
                        $o->Quota = $r['ph_Quota'];
                        //Attention rattache le parent client et le parent serveur !!!
                        $o->addParent('Client',$r['parc_server_ps_id']);
                        $o->addParent('Server',$r['parc_client_pc_id']);
                        //Attention ne marche que si connexion LDAP !!!!
                        //$o->Save();
                        echo "\tedit host ".$o->Id."\r\n";
                }
                
                //synchro des données
                $host = Sys::getData('Parc','Host',0,99999);
                $virg = '';
                $query = 'TRUNCATE TABLE parc_host;INSERT INTO parc_host (ph_Id,ph_Nom,ph_Commentaires,ph_Quota,parc_server_ps_id,parc_client_pc_id) VALUES ';
                foreach ($host as $h){
                        $padreC = $h->getOneParent('Client');
                        $padreS = $h->getOneParent('Server');
                        $query .= $virg.'('.$h->Id.', "'.$h->Nom.'", "'.$h->Commentaires.'", "'.$h->Quota.'", "'.$padreS->Id.'", "'.$padreC->Id.'")';
                        $virg = ',';
                }
                $query .= ';';
                
                $this->dbAbtel->exec($query);
        }
        
        /**
         * syncDomain
         * Synchronise la table domain
         */
        private function syncDomain() {
        
                //host
                //test du dirty
                $query = 'select * from parc_domain where pd_Dirty = 1;';
                $res = $this->dbAbtel->query($query);
                if ($res)
                        $result = $res->fetchALL ( PDO::FETCH_ASSOC );
                if ($result) foreach ($result as $r) {
                        $o = Sys::getOneData('Parc','Domain/'.$r['pd_Id']);
                        $o->Url = $r['pd_Url'];
                        //Attention rattache le parent client !!!
                        $o->addParent('Client',$r['parc_client_pc_id']);
                        //Attention ne marche que si connexion LDAP !!!!
                        //$o->Save();
                        echo "\tedit domain ".$o->Id."\r\n";
                }
                
                //synchro des données
                $domain = Sys::getData('Parc','Domain',0,99999);
                $virg = '';
                $query = 'TRUNCATE TABLE parc_domain;INSERT INTO parc_domain (pd_Id,pd_Url,parc_client_pc_id) VALUES ';
                foreach ($domain as $d){
                        $padre = $d->getOneParent('Client');
                        $query .= $virg.'('.$d->Id.', "'.$d->Url.'", "'.$padre->Id.'")';
                        $virg = ',';
                }
                $query .= ';';
                $this->dbAbtel->exec($query);
        }
        
        /**
         * syncMail
         * Synchronise la table mail
         */
        private function syncMail() {
        
                //host
                //test du dirty
                $query = 'select * from parc_mail where pm_Dirty = 1;';
                $res = $this->dbAbtel->query($query);
                if ($res)
                        $result = $res->fetchALL ( PDO::FETCH_ASSOC );
                if ($result) foreach ($result as $r) {
                        $o = Sys::getOneData('Parc','CompteMail/'.$r['pm_Id']);
                        $o->Adresse = $r['pm_Adresse'];
                        $o->Quota = $r['pm_Quota'];
                        //Attention rattache le parent client !!!
                        $o->addParent('Client',$r['parc_client_pc_id']);
                        
                        if(isset($r['parc_server_ps_id']) && $r['parc_server_ps_id'] != null){
                                $o->addParent('Server',$r['parc_server_ps_id']);
                        }
                        
                        //Attention ne marche que si connexion LDAP !!!!
                        //$o->Save();
                        echo "\tedit mail ".$o->Id."\r\n";
                }
                
                //synchro des données
                $mails = Sys::getData('Parc','CompteMail',0,99999);
                
                $virg = '';
                $query = 'TRUNCATE TABLE parc_mail;INSERT INTO parc_mail (pm_Id,pm_Adresse,pm_Quota,pm_Used,pm_Status,parc_server_ps_id,parc_client_pc_id) VALUES ';
                foreach ($mails as $m){
                        $padreC = $m->getOneParent('Client');
                        $padreS = $m->getOneParent('Server');
                        $query .= $virg.'('.$m->Id.', "'.$m->Adresse.'", '.$m->Quota.', '.$m->EspaceUtilise.', "'.$m->Status.'", '.$padreS->Id.', '.$padreC->Id.')';
                        $virg = ',';
                }
                $query .= ';';
                
                
                $this->dbAbtel->exec($query);
        }
        
        
        /**
         * getMail
         * Récupère la liste des mails depuis les serveurs
         */
        public function getMail() {
                require_once '//var/www/html/Class/Lib/SplClassLoader.php'; // The PSR-0 autoloader from https://gist.github.com/221634
                @include_once '/var/www/html/Class/Lib/SimpleXmlDebug/simplexml_dump.php';
                @include_once '/var/www/html/Class/Lib/SimpleXmlDebug/simplexml_tree.php';
                
                $classLoader = new SplClassLoader('Zimbra', realpath('/var/www/html/Class/Lib/')); // Point this to the src folder of the zcs-php repo
                $classLoader->register();
                
                
                $servers = array(array('mx2.abtel.fr','AbT04D72eDtAI741'),array('mx3.abtel.fr','AbT04D72eDtAI951'));

                // Define some constants we're going to use
                define('ZIMBRA_PORT', '7071');
                
                //TODO : Truncate la table ?
                
                foreach($servers as $server){
                        //TODO Verif que les comptes admin sont bien actifs avant !!!
                        
                        
                        // Create a new Admin class and authenticate
                        $zimbra = new \Zimbra\ZCS\Admin($server[0], ZIMBRA_PORT);
                        $zimbra->auth('admin', $server[1]);
                        
                        
                        $kServ = Sys::getOneData('Parc','Server/DNSNom='.$server[0]);
                        
                        try{
                                $domaines = $zimbra->getDomains();
                                $quotas = $zimbra->getQuotas(array());
                                //echo '<pre>';
                                //print_r($quotas);
                                //echo '</pre>';

                                foreach($domaines as $domain){
                                        //echo '<pre>';
                                        //print_r($domain);
                                        //echo '</pre>';
                                        
                                        $dname = $domain->get('name');
                                        //print_r($dname.'<br/>');
                                        $kDom = Sys::getOneData('Parc','Domain/Url='.$dname);
                                        if(!is_object($kDom)){
                                                echo '<b>Domaine</b> "'.$dname.'" absent du Parc. Les adresse appartenant à ce domaine seront ignorées car impossible à relier à un client.<br>';
                                                continue;
                                        }
                                        $kCli = $kDom->getOneParent('Client');
                                        if(!is_object($kCli)){
                                                echo '<b>Client</b> introuvable pour le domaine "'.$dname.'". Les adresse appartenant à ce domaine seront ignorées car impossible à relier à un client.<br>';
                                                continue;
                                        }
                                        
                                        $accList = $zimbra->getAccounts(array(
                                                               'domain'=>$dname,
                                                               'limit'=> 200,
                                                               'offset'=> 0
                                                               ));
                                        foreach($accList as $account){
                                                //echo '<pre>';
                                                //print_r($account);
                                                //echo '</pre>';
                                                //exit;
                                                
                                                $accId = $account->get('id');
                                                $accName = $account->get('name');
                                                $userNom = $account->get('sn');
                                                $userPrenom = $account->get('givenName');
                                                //print_r($accId.' : '.$accName.'<br>');
                                                //print_r($quotas[$accId]['limit'].' / '.$quotas[$accId]['used'] .'<br>');
                                                $userQuota = $quotas[$accId]['limit'];
                                                $userUsed = $quotas[$accId]['used'];
                                                $accStatus = $account->get('zimbraMailStatus');
                                                
                                                $o = Sys::getOneData('Parc','CompteMail/Adresse='.$accName);
                                                if(!is_object($o)){
                                                   $o = genericClass::createInstance('Parc','CompteMail');
                                                   $o->Adresse = $accName;
                                                } 
                                                $o->Nom = $userNom;
                                                $o->Prenom = $userPrenom;
                                                $o->Quota = floor($userQuota/1048576); //En Mo
                                                $o->EspaceUtilise =floor($userUsed/1048576); //En Mo
                                                $o->Status = $accStatus;
                                                
                                                if(is_object($kServ)){
                                                        $o->addParent($kServ);
                                                }
                                                $o->addParent($kCli);
                                                
                                                $o->Save();
                                        }
                                }
                        } catch (Exception $e){
                                print_r ($e);
                        }
                        
                }
                
        }
        
        
}