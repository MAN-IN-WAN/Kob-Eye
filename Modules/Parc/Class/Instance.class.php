<?php

class Instance extends genericClass{
    private $_plugin = null;
    public function Save($force=false){
        $old = Sys::getOneData('Parc', 'Instance/' . $this->Id);
        if (!$old) $new = true; else $new = false;
        if (!$new&&$old->SousDomaine!=$this->SousDomaine){
            $this->addError(Array("Message" => 'Impossible de modifier le sous domaine d\'une instance. Veuillez dupliquer ou créer une nouvelle instance.'));
            return false;
        }
        //serveurs par défaut
        $apachesrv = Sys::getOneData('Parc', 'Server/Web=1&defaultWebServer=1');
        $proxysrv = Sys::getOneData('Parc', 'Server/Proxy=1');
        $mysqlsrv = Sys::getOneData('Parc', 'Server/Sql=1&defaultSqlServer=1');
        $dom = Sys::getOneData('Parc','Domain/defaultDomain=1',0,1,'','','','',true);

        if (!$apachesrv) {
            $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Il n\'y a pas de serveur apache par défaut. Veuillez en définir un.'));
            return false;
        }
        if (!$mysqlsrv) {
            $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Il n\'y a pas de serveur mysql par défaut. Veuillez contacter votre administrateur.'));
            return false;
        }
        if (!$dom) {
            $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Il n\'y a pas de domaine par défaut. Veuillez contacter votre administrateur.'));
            return false;
        }

        //Verification du nom
        if ($this->SousDomaine != Instance::checkName($this->SousDomaine)) {
            $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Le sous-domaine de l\'instance ne doit pas contenir de caractère spéciaux. '.$this->SousDomaine.'!='.Instance::checkName($this->SousDomaine)));
            return false;
        }
        parent::Save();

        //Check du client
        $client = $this->getOneParent('Client');
        if (!$client) {
            /*$GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Une instance doit être liée à un client. Si il n\'existe pas , veuillez le créer au préalable.'));
            return false;*/
            //création d'un client par défaut
            //on vérifie que le client n'existe pas déjà
            $client = Sys::getOneData('Parc','Client/NomLdap='.Utils::CheckSyntaxe($this->Nom));
            if (!$client) {
                $client = genericClass::createInstance('Parc', 'Client');
                $client->Nom = $this->Nom;
                if (!$client->Save()) {
                    $this->Delete();
                    $this->Error = array_merge($this->Error,$client->Error);
                    return false;
                }
            }
            $this->addParent($client);
        }

        //creation du nom temporaire
        if (empty($this->InstanceNom))
            $this->InstanceNom = 'instance-'.$client->NomLDAP;

        //Vérification du mot de passe
        if (empty($this->Password)){
            $this->Password = str_shuffle(bin2hex(openssl_random_pseudo_bytes(12)));
        }

        $tmpname = $this->InstanceNom;

        //vérification de l'existence
        $as = Sys::getOneData('Parc','Domain/'.$dom->Id.'/Subdomain/Url='.$tmpname);
        if (!$as){
            $as = genericClass::createInstance('Parc','Subdomain');
            $as->Url = $tmpname;
            $as->IP = $proxysrv->IP;
            $as->addParent($dom);
            $as->Save();
        }else{
            $as->IP = $proxysrv->IP;
            $as->addParent($dom);
            $as->Save();
        }
        $this->FullDomain = $as->Url.'.'.$dom->Url;
        parent::Save();

        //Check des domaine
        $dom = $this->getParents('Domain');
        if (!is_array($dom) || !sizeof($dom)) {
            /*$GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Une instance doit être liée à un ou plusieurs domaines'));
            return false;*/
        } else {
            $defaulturl = false;
            $otherurls = array();
            foreach ($dom as $d) {
                //vérification des sous domaines
                $www = $d->getOneChild('Subdomain/Url=A:' . $this->InstanceNom);
                if (!$www) {
                    //création du A
                    $www = genericClass::createInstance('Parc', 'Subdomain');
                    $www->Url = $this->SousDomaine;
                    $www->IP = $proxysrv->IP;
                } else {
                    $www->IP = $proxysrv->IP;
                }
                $www->addParent($d);
                $www->Save();
                if (!$defaulturl) $defaulturl = $this->SousDomaine . '.' . $d->Url;
                else $otherurls[] = $this->SousDomaine . '.' . $d->Url;
            }
        }

        //Check de l'hébergement
        $heb = $this->getOneParent('Host');
        if (!$heb) {
            //alors création de l'hébergement
            $heb = genericClass::createInstance('Parc', 'Host');
            $heb->Nom = $tmpname;
            $heb->Password = $this->Password;
            $heb->Production = true;
            $heb->PHPVersion = $this->PHPVersion;
            $heb->addParent($apachesrv);
            $heb->addParent($client);
            if (!$heb->Save()) {
                $this->Delete();
                $this->Error = array_merge($this->Error,$heb->Error);
                return false;
            }
            $this->addParent($heb);
        } else {
            $heb->Production = true;
            $heb->Password = $this->Password;
            $heb->PHPVersion = $this->PHPVersion;
            $heb->Save();
        }

        //check apache
        $apache = Sys::getOneData('Parc','Host/'.$heb->Id.'/Apache',0,1,'ASC','Id');
        if (!$apache) {
            //alors création du apache
            $apache = genericClass::createInstance('Parc', 'Apache');
            $apache->DocumentRoot = $this->SousDomaine;
            $apache->ApacheServerName = $this->FullDomain;
            $apache->ApacheServerAlias = $this->ServerAlias;
            $apache->Actif = true;
            $apache->addParent($heb);
        } else {
            $apache->ApacheServerName = $this->FullDomain;
            if (empty($this->ServerAlias)){
                $this->ApacheServerAlias = $apache->ServerAlias;
            }else $apache->ApacheServerAlias = $this->ServerAlias;

            $apache->Actif = true;
            $apache->addParent($heb);
        }
        //Test
        if ($this->Type=='prod'){
            $apache->ProxyCache=true;
        }else{
            $apache->ProxyCache=false;
        }
        $apache->Save();

        //check bdd
        $bdd = $heb->getOneChild('Bdd');
        if (!$bdd) {
            //alors création du apache
            $bdd = genericClass::createInstance('Parc', 'Bdd');
            $bdd->Nom = $tmpname;
            $bdd->addParent($heb);
            $bdd->addParent($mysqlsrv);
            $bdd->Save();
        } else {
            $bdd->addParent($heb);
            $bdd->Save();
        }
        $this->addParent($bdd);

        //check ftp
        /*$bdd = $heb->getOneChild('Ftpuser');
        if (!$bdd) {
            //alors création du apache
            $bdd = genericClass::createInstance('Parc', 'Ftpuser');
            $bdd->Nom = $tmpname;
            $bdd->addParent($heb);
            $bdd->addParent($mysqlsrv);
            $bdd->Save();
        } else {
            $bdd->addParent($heb);
            $bdd->Save();
        }*/
        //$this->addParent($bdd);
        parent::Save();
        //if (!$this->Enabled||$old->VersionId!=$this->VersionId||$old->Type!=$this->Type){
        //$this->Enabled = false;
        $apachesrv->callLdap2Service();
        if (intval($this->Status) <= 1)
            $this->createInstallTask();
        parent::Save();
        //}
        if ($this->EnableSsl)
            $apache->enableSsl($force,$this);
        $this->Error = $apache->Error;

        //execution postinit plugin
        $plugin = $this->getPlugin();
        $plugin->postInit();
        //redemarrages de proxys
        Server::createRestartProxyTask();
        return true;
    }
    public function softSave(){
        return parent::Save();
    }
    /**
     * Retourne un plugin Parc / Instance
     * @return	Implémentation d'interface
     */
    public function getPlugin() {
        if (!$this->_plugin) {
            $this->_plugin = Plugin::createInstance('Parc', 'Instance', $this->Plugin);
            $this->_plugin->setConfig($this->PluginConfig, $this);
        }
        return $this->_plugin;
    }


    /**
     * createInstallTask
     * Creation de la tache d'installation de l'applicatif
     */
    public function createInstallTask(){
        //on vérifie que la tache n'est pas déjà crée
        $nb = Sys::getCount('Parc','Instance/'.$this->Id.'/Tache/Termine=0&Erreur=0');
        if ($nb) return true;
        $plugin = $this->getPlugin();
        $this->Status=1;
        parent::Save();
        return $plugin->createInstallTask();
    }
    /**
     * createUpdateTask
     * Creation de la tache d'installation de l'applicatif
     */
    public function createUpdateTask($orig = null){
        $plugin = $this->getPlugin();
        return $plugin->createUpdateTask($orig);
    }
    /**
     * createCheckStateTask
     * Creation de la tache de vérification
     */
    public function createCheckStateTask($orig=null){
        //gestion depuis le plugin
        $task = genericClass::createInstance('Parc', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Vérification de l\'instance ' . $this->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Instance';
        $task->TaskId = $this->Id;
        $task->TaskFunction = 'checkState';
        $task->addParent($this);
        $host = $this->getOneParent('Host');
        if (!$host) return false;
        $task->addParent($host);
        $task->addParent($host->getOneParent('Server'));
        if (is_object($orig)) $task->addParent($orig);
        $task->Save();
    }

    /**
     * Delete
     * Supprime une instance
     * @return bool
     */
    public function Delete(){
        //suppression hébergement
        $host = $this->getOneParent('Host');
        if ($host)
            $host->Delete();
        parent::Delete();
        return true;
    }

    /**
     * createActivity
     * créé une activité en liaison avec l'esx
     * @param $title
     * @param null $obj
     * @param int $jPSpan
     * @param string $Type
     * @return genericClass
     */
    public function createActivity($title, $Type = 'Exec', $Task = null)
    {
        $act = genericClass::createInstance('Parc', 'Activity');
        $host = $this->getOneParent('Host');
        $srv = $host->getOneParent('Server');
        $act->addParent($this);
        $act->addParent($host);
        $act->addParent($srv);
        if ($Task) $act->addParent($Task);
        $act->Titre = $this->tag . date('d/m/Y H:i:s') . ' > ' . $this->Titre . ' > ' . $title;
        $act->Started = true;
        $act->Type = $Type;
        $act->Progression = 0;
        $act->Save();
        return $act;
    }

    /**
     * installSoftware
     * Fonction d'installation ou de mise à jour de l'applicatif
     * @param Object Tache
     */
    public function installSoftware($task = null){
        $plugin = $this->getPlugin();
        return $plugin->installSoftware($task);
    }

    /**
     * updateSoftware
     * Fonction de mise à jour de l'applicatif
     * @param Object Tache
     */
    public function updateSoftware($task = null){
        $plugin = $this->getPlugin();
        return $plugin->updateSoftware($task);
    }

    /**
     * setStatus
     * Définit le status de l'instance
     * @param bool $force
     *
     */
    public function setStatus($nb){
        $this->Status = $nb;
        parent::Save();
    }

    /**
     * enableSsl
     * Active le ssl pour l'ensemble des hôtes virtuels
     * @param bool $force
     */
    public function enableSsl($force = false){
        //recherche du apache correspondant
        $host = $this->getOneParent('Host');
        $apache = $host->getOneChild('Apache');
        $out =  $apache->enableSsl($force,$this);
        $this->Error = $apache->Error;
    }
    /**
     * getHttpCode
     * Retourne le code http pour un domaine en particulier.
     */
    public static function getHttpCode($url,$https=false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,$https);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $rt = curl_exec($ch);
        $info = curl_getinfo($ch);
        return $info;
        //return $info["http_code"];
    }
    /**
     * checkName
     * Vérifie le nom de l'instance
     * @param $chaine
     * @return mixed|string
     */
    static function checkName($chaine) {
        $chaine=utf8_decode($chaine);
        $chaine=stripslashes($chaine);
        $chaine = preg_replace('`\s+`', '-', trim($chaine));
        $chaine = str_replace("'", "-", $chaine);
        $chaine = str_replace("&", "et", $chaine);
        $chaine = str_replace('"', "-", $chaine);
        $chaine = str_replace("?", "", $chaine);
        $chaine = str_replace("!", "", $chaine);
        $chaine = str_replace(".", "", $chaine);
        $chaine = preg_replace('`[\,\ \(\)\+\'\/\:_\;]`', '-', trim($chaine));
        $chaine=strtr($chaine,utf8_decode("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ?"),"aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn-");
        $chaine = preg_replace('`[-]+`', '-', trim($chaine));
        $chaine =  utf8_encode($chaine);
        $chaine = preg_replace('`[\/]`', '-', trim($chaine));

        return $chaine;
    }
    /**
     * checkSssl
     * @param $url
     */
    public static function checkSsl($url){
        $orignal_parse = parse_url($url, PHP_URL_HOST);
        $get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
        $read = stream_socket_client("ssl://".$orignal_parse.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
        $cert = stream_context_get_params($read);
        $certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);
        return $certinfo;
    }
    /**
     * checkState
     * Vérifie l'état d'une instance
     */
    public function checkState($task=null) {
        $host = $this->getOneParent('Host');
        $aps = $host->getChildren('Apache');
        //vérifie le retour http sur page accueil
        foreach ($aps as $ap){
            $domains = explode(' ',$ap->getDomains());
            foreach ($domains as $domain){
                $domain = trim($domain);
                if (empty($domain)) continue;
                if (preg_match('#azko.site#',$domain)) continue;
                //test http
                $code = self::getHttpCode('http://'.$domain);
                if (!in_array($code["http_code"],array(200,301,302))){
                    //alors incident
                    $incident = Incident::createIncident('Le domaine '.$domain.' ne répond pas correctement en http.','Le code de retour est '.print_r($code,true),$this,'HTTP_CODE',4,false);
                }else Incident::createIncident('Le domaine '.$domain.' ne répond pas correctement en http.','Le code de retour est '.print_r($code,true),$this,'HTTP_CODE',4,true);
                //si ssl vérifie l'état du certificat et le code retour
                if ($ap->Ssl){
                    $code = self::getHttpCode('https://'.$domain,true);
                    if (!in_array($code["http_code"],array(200,301,302))){
                        //alors incident
                        $incident = Incident::createIncident('Le domaine '.$domain.' ne répond pas correctement en https.','Le code de retour est '.print_r($code,true),$this,'HTTPS_CODE',4,false);
                    }else $incident = Incident::createIncident('Le domaine '.$domain.' ne répond pas correctement en https.','Le code de retour est '.print_r($code,true),$this,'HTTPS_CODE',4,true);
                    if ($code["ssl_verify_result"]!==0){
                        //alors incident
                        $incident = Incident::createIncident('Le certificat du domaine '.$domain.' n\'est pas valide.','Le code de retour est '.print_r($code,true),$this,'SSL_ERROR',4,false);
                    }else $incident = Incident::createIncident('Le certificat du domaine '.$domain.' n\'est pas valide.','Le code de retour est '.print_r($code,true),$this,'SSL_ERROR',4,true);
                }
            }
        }
        $plugin = $this->getPlugin();
        //appel checkState du plugin
        return $plugin->checkState($task);
    }
}