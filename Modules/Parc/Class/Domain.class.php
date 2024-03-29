<?php

require_once ROOT_DIR . "Class/Lib/MailCleaner.class.php";
require_once ROOT_DIR . "Class/Lib/Mib.class.php";

class Domain extends genericClass
{
    var $_isVerified = false;
    var $UpdateOnSave;

    /**
     * Force la vérification avant enregistrement
     * @param boolean    Enregistrer aussi sur LDAP
     * @return    bool void
     */
    public function Save($synchro = true)
    {
        $first = ($this->Id == 0);
        $old = Sys::getOneData('Parc', 'Domain/' . $this->Id);
        //test de modification du ApacheServerName
        if ($this->Id && $old->Url != $this->Url) {
            $this->addError(array("Message" => "Impossible de modifier le nom de domaine de la zone. Si c'est nécessaire, veuillez la supprimer et la recréer" . $old->Url . "!=" . $this->Url));
            return false;
        }

        parent::Save();
        // Forcer la vérification
        $this->Verify($synchro);

        // Enregistrement si pas d'erreur
        if ($this->_isVerified) {

            parent::Save();

            //Pour eviter les domaines sans NS
            $child = $this->getOneChild('NS');
            if(!$child){
                $ns = genericClass::createInstance('Parc','NS');
                $ns->Dnsdomainname = $this->Url.'.';
                $ns->Dnscname = 'ns1.abtel.fr.';
                $ns->addParent($this);
                $ns->Save();
                $ns2 = genericClass::createInstance('Parc','NS');
                $ns2->Dnsdomainname = $this->Url.'.';
                $ns2->Dnscname = 'ns2.abtel.fr.';
                $ns2->addParent($this);
                $ns2->Save();
            }

            if ($this->updateOnSave && !$this->Secondaire) {
                $this->updateOnSave = false;
                parent::Save();
                $this->AutoGenSubDomains();
            }
            if (!$this->Secondaire) {
                //mise à jour des serveur dns
                $pxs = Sys::getData('Parc', 'Server/Dns=1');
                foreach ($pxs as $px) {
                    $px->callLdap2Service();
                }
            }
            if ($this->Mail) {
                $infra = Sys::getOneData('Parc', 'Infra/Type=Mail&Default=1');
                $client = $this->getOneParent('Client');

                try {
                    $mc = new MailCleaner();
                } catch (Exception $e) {
                    $this->addError(array('Message' => 'Une erreur s\'est produite lors de l\'enregistrement du domaine, il nous est impossible de localiser un serveur MailCleaner', 'Obj' => $e->getMessage()));
                }

                if (is_object($mc) && !$mc->domainExists($this->Url)) {
                    if (!$res = $mc->addDomain($this->Url)) $this->addError(array('Message' => 'Une erreur s\'est produite lors de l\'enregistrement du domaine sur la plate-forme MailCLeaner (1)', 'Obj' => $res));

                    $params = array("supportname" => "Support Abtel","supportemail"=>"support@abtel.fr");
                    if (!$res = $mc->editDomain($this->Url, $params)) {
                        $this->addError(array('Message' => 'Une erreur s\'est produite lors de l\'enregistrement du domaine sur la plate-forme MailCLeaner (1b)', 'Obj' => $res));
                    }
                }


                //Si MIB
                if ($this->MailFilter) {
                    $ms = $infra->getChildren('Server/MailInBlack=1');

                    //Check plus creation su MIB si besoin
                    try {
                        $mib = new Mib();
                    } catch (Exception $e) {
                        $this->addError(array('Message' => 'Une erreur s\'est produite lors de l\'enregistrement du domaine, il nous est impossible de localiser un serveur MIB', 'Obj' => $e->getMessage()));
                    }

                    if (!$client) {
                        $this->addError(array('Message' => 'Impossible de filtrer les mails d\'un domaine sans client'));
                    } else {
                        $mibid = $client->IdMIB;
                        //SI le client a dejà un ID MIB
                        if (!empty($mibid)) {
                            $ret = $mib->getClient(array('clientId' => $mibid, "projection" => "clientWithAll"));
                            //Si le client est trouvé sur MIB on check si le domain existe déjà
                            if (!empty($ret[0])) {
                                $ok = false;
                                foreach ($ret[0]['domains'] as $dom) {
                                    if ($dom['domain'] == $this->Url) {
                                        $ok = true;
                                        break;
                                    }
                                }
                                //Si le domaine n'exste pas on le crée dans le MIB
                                if (!$ok) {
                                    $ret3 = $mib->addDomain($mibid, $this->Url);
                                    if (!empty($ret3['id'])) {
                                        $ms = $infra->getChildren('Server/Mail=1&(!MailType=Mta+MailType=AllInOne!)');
                                        foreach ($ms as $s) {
                                            $ret4 = $mib->addDomainServer($mibid, $this->Url, $s->InternalIP);
                                            if (empty($ret4['id'])) $this->addWarning(array('Message' => 'Impossible d\'ajouter une nouveau serveur de domaine sur la plate-forme de filtrage de mail', 'Obj' => $ret3));
                                        }
                                    } else {
                                        $this->addError(array('Message' => 'Impossible d\'ajouter une nouveau domaine sur la plate-forme de filtrage de mail', 'Obj' => $ret3));
                                    }
                                }
                            } else {
                                $this->addError(array('Message' => 'Impossible de localiser le client sur la plate-forme de filtrage de mail', 'Obj' => $ret));
                            }
                        } else {
                            //Si le client n'est pas créé dans le MIB on le crée
                            $ret = $mib->addClient($client->CodeGestion);
                            if (!empty($ret['id'])) {
                                $client->IdMIB = $ret['id'];
                                $client->Save();
                                $ret2 = $mib->addLicense($ret['id']);
                                if (!empty($ret2['id'])) {
                                    $ret3 = $mib->addDomain($ret['id'], $this->Url);
                                    if (!empty($ret3['id'])) {
                                        $ms = $infra->getChildren('Server/Mail=1&(!MailType=Mta+MailType=AllInOne!)');
                                        foreach ($ms as $s) {
                                            $ret4 = $mib->addDomainServer($ret['id'], $this->Url, $s->InternalIP);
                                            if (empty($ret4['id'])) $this->addWarning(array('Message' => 'Impossible d\'ajouter une nouveau serveur de domaine sur la plate-forme de filtrage de mail', 'Obj' => $ret3));
                                        }
                                    } else {
                                        $this->addError(array('Message' => 'Impossible d\'ajouter une nouveau domaine sur la plate-forme de filtrage de mail', 'Obj' => $ret3));
                                    }
                                } else {
                                    $this->addError(array('Message' => 'Impossible d\'ajouter une nouvelle license sur la plate-forme de filtrage de mail', 'Obj' => $ret2));
                                }
                            } else {
                                $this->addError(array('Message' => 'Impossible d\'ajouter le nouveau client sur la plate-forme de filtrage de mail', 'Obj' => $ret));
                            }
                        }
                    }
                    //Si pas d'erreur on fait pointer sur le MIB
                    //if (!count($this->Error)) {
                    if (true) {
                        //Redirect du mailcleaner su MIB
                        $ips = array();
                        foreach ($ms as $s) {
                            $ips[] = $s->InternalIP;
                        }
                        $ipl = implode(',', $ips);
                        $params = array("destination" => $ipl); //$params = array("destination"=>"10.100.200.51");
                        //Modification de l'adressage dans le mailcleaner pour faire pointer sur MIB
                        if (is_object($mc) && !$res = $mc->editDomain($this->Url, $params)) $this->addError(array('Message' => 'Une erreur s\'est produite lors de l\'enregistrement du domaine sur la plate-forme MailCLeaner (2a)', 'Obj' => $res));
                    }

                } else {
                    $ips = array();
                    $ms = $infra->getChildren('Server/Mail=1&(!MailType=Mta+MailType=AllInOne!)');
                    foreach ($ms as $s) {
                        $ips[] = $s->InternalIP;
                    }
                    $ipl = implode(',', $ips);
                    $params = array("destination" => $ipl); //$params = array("destination"=>"10.0.189.1,10.0.189.2,10.0.189.3");
                    if (is_object($mc) && !$res = $mc->editDomain($this->Url, $params)) $this->addWarning(array('Message' => 'Une erreur s\'est produite lors de l\'enregistrement du domaine sur la plate-forme MailCLeaner (2b)', 'Obj' => $res));
                }

                if (!$this->Secondaire) {
                    //$this->CheckMXs(); //TODO: Fonction inexistante à l'heure actuelle
                }
            }
        }

        return true;
    }


    public function Synchroniser()
    {
        Server::ldapConnect();
        $req = ldap_search(Server::$_LDAP, $this->LdapDN, '(objectClass=*)', array('*', 'modifytimestamp', 'entryuuid'));
        $res = ldap_get_entries(Server::$_LDAP, $req);
        foreach ($res as $k => $r) :
            if ($k == 'count' or !isset($r['dnstype']) or $r['dnstype'][0] != 'A' or !isset($r['cn']) or !isset($r['dnsipaddr'])) continue;
            $url = $r['cn'][0];
            $ip = $r['dnsipaddr'][0];
            $e = Sys::$Modules['Parc']->callData('Domain/' . $this->Id . '/Subdomain/Url=' . $url, false, 0, 1, 'DESC', 'Id', 'COUNT(*)');
            if (!$e[0]['COUNT(*)']) {
                $KEObj = genericClass::createInstance('Parc', 'Subdomain');
                $KEObj->Url = $url;
                $KEObj->IP = $ip;
                $KEObj->LdapDN = 'cn=' . $url . ',' . $this->LdapDN;
                $KEObj->LdapID = $r['entryuuid'][0];
                $KEObj->LdapTms = $r['modifytimestamp'][0];
                $KEObj->AddParent($this);
                $KEObj->Save();
                echo "Sous domaine <strong>$url</strong> ($ip) ajouté.<br />";
            }
        endforeach;
        echo '<br /><a href="/Parc/Domain/' . $this->Id . '">Retour au domaine</a>';
    }

    /**
     * Verification des erreurs possibles
     * @param boolean    Verifie aussi sur LDAP
     * @return    bool Verification OK ou NON
     */
    public function Verify($synchro = false)
    {

        if (parent::Verify()) {

            $this->_isVerified = true;

            if ($synchro) {

                // Outils
                $dn = 'cn=' . $this->Url . ',ou=domains,' . PARC_LDAP_BASE;

                // Verification à jour
                $res = Server::checkTms($this);

                if ($res['exists']) {
                    if (!$res['OK']) {
                        $this->AddError($res);
                        $this->_isVerified = false;
                    } else {
                        // Déplacement
                        //$res = Server::ldapRename($this->LdapDN, 'cn='.$this->Url, 'ou=domains,'.PARC_LDAP_BASE);
                        //if($res['OK']) {
                        // Modification
                        $entry = $this->buildEntry(false);
                        $res = Server::ldapModify($this->LdapID, $entry);
                        if ($res['OK']) {
                            // Tout s'est passé correctement
                            $this->LdapDN = $dn;
                            $this->LdapTms = $res['LdapTms'];
                            $this->updateDnsSerial();
                        } else {
                            // Erreur
                            $this->AddError($res);
                            $this->_isVerified = false;
                            // Rollback du déplacement
                            /*$tab = explode(',', $this->LdapDN);
                            $leaf = array_shift($tab);
                            $rest = implode(',', $tab);
                            Server::ldapRename($dn, $leaf, $rest);*/
                        }
                        /*}
                        else {
                            $this->AddError($res);
                            $this->_isVerified = false;
                        }*/
                    }

                } else {
                    ////////// Nouvel élément
                    $entry = $this->buildEntry();
                    $res = Server::ldapAdd($dn, $entry);
                    if ($res['OK']) {
                        $this->LdapDN = $dn;
                        $this->DNSSerial = $entry['dnsserial'];
                        $this->LdapID = $res['LdapID'];
                        $this->LdapTms = $res['LdapTms'];
                    } else {
                        $this->AddError($res);
                        $this->_isVerified = false;
                    }
                }

            }

        } else {

            $this->_isVerified = false;

        }

        return $this->_isVerified;

    }

    /**
     * Mise à jour du DNS Serial
     * Après modification Domaine ou Sous Domaine
     * Notation : YYYYMMDDVV (annee-mois-jour-version)
     * @return    void
     */
    public function updateDnsSerial()
    {
        // Update jour ou version ?
        if (substr($this->DNSSerial, 0, 8) == date('Ymd')) $serialTms = $this->DNSSerial + 1;
        else $serialTms = date('Ymd01');
        // Mise à jour de l'entrée dnsserial
        $res = Server::ldapModify($this->LdapID, array('dnsserial' => $serialTms));
        // On enregistre si cela s'est bien passé
        if ($res['OK']) {
            $this->DNSSerial = $serialTms;
            $this->LdapTms = $res['LdapTms'];
            parent::Save();
        }
    }


    /**
     * Configuration d'une nouvelle entrée type
     * Utilisé lors du test dans Verify
     * puis lors du vrai ajout dans Save
     * @param boolean        Si FALSE c'est simplement une mise à jour
     * @return    Array
     */
    private function buildEntry($new = true)
    {
        $entry = array();
        $entry['cn'] = $this->Url;
        $entry['dnsadminmailbox'] = 'postmaster.' . $this->Url . '.';
        if (!$new) {
            $sd = Sys::$Modules["Parc"]->callData("Domain/" . $this->Id . "/NS/Nom=NS:1", false, 0, 1);
            if (isset($sd[0]) && is_array($sd[0])) {
                $sd[0] = genericClass::createInstance("Parc", $sd[0]);
                $ns = $sd[0]->getParents("Server");
                if (isset($ns[0]) && is_object($ns[0]))
                    $entry['dnszonemaster'] = $ns[0]->DNSNom;
                else
                    $entry['dnszonemaster'] = DNS_ZONE_MASTER;
            } else    $entry['dnszonemaster'] = DNS_ZONE_MASTER;
        } else    $entry['dnszonemaster'] = DNS_ZONE_MASTER;
        $entry['dnszonename'] = $this->Url;

        $entry['dnsminimum'] = $this->TTLMin ? $this->TTLMin : 60;
        $entry['dnsttl'] = $this->TTL ? $this->TTL : 86400;

        if ($new) {
            $entry['dnsclass'] = 'IN';
            $entry['dnsexpire'] = 1209600;
            $entry['dnsrefresh'] = 21600;
            $entry['dnsretry'] = 3600;
            $entry['dnsserial'] = date('Ymd01');
            $entry['dnstype'] = 'SOA';
            $entry['objectclass'][0] = 'dnszone';
            $entry['objectclass'][1] = 'top';
        }
        return $entry;
    }

    /**
     * Suppression de la BDD
     * Relai de cette suppression à LDAP
     * On utilise aussi la fonction de la superclasse
     * @return    void
     */
    public function Delete()
    {
        $KEServer = $this->getKEServer();
        //suppression des sous domaines
        $sd = $this->getChilds('Subdomain');
        if (is_array($sd)) foreach ($sd as $s) $s->Delete();
        //suppression des alias sous domaines
        $sd = $this->getChilds('CNAME');
        if (is_array($sd)) foreach ($sd as $s) $s->Delete();
        //suppression des servers de noms
        $sd = $this->getChilds('NS');
        if (is_array($sd)) foreach ($sd as $s) $s->Delete();
        //suppression des servers de mails
        $sd = $this->getChilds('MX');
        if (is_array($sd)) foreach ($sd as $s) $s->Delete();
        //suppression des champs de textes
        $sd = $this->getChilds('TXT');
        if (is_array($sd)) foreach ($sd as $s) $s->Delete();
        //suppression des utilisateurs ftp
        $sd = $this->getChilds('Ftpuser');
        if (is_array($sd)) foreach ($sd as $s) $s->Delete();
        Server::ldapDelete($this->LdapID);
        parent::Delete();
    }


    /**
     * Récupère une référence vers l'objet KE "Server"
     * pour effectuer des requetes LDAP
     * On conserve une référence vers le serveur
     * pour le cas d'une utilisation ultérieure
     * @return    L'objet Kob-Eye
     */
    private function getKEServer()
    {
        if (!isset($this->_KEServer) || !is_object($this->_KEServer)) {
            $Tab = Sys::$Modules["Parc"]->callData('Parc/Server/1', "", 0, 1, null, null, null, null, true);
            $this->_KEServer = genericClass::createInstance('Parc', $Tab[0]);
        }
        return $this->_KEServer;
    }

    /**
     * Retrouve les parents lors d'une synchronisation
     * @return    void
     */
    public function findParents()
    {
        $Parts = explode(',', $this->LdapDN);
        foreach ($Parts as $i => $P) $Parts[$i] = explode('=', $P);
    }

    /**
     * Génère automatiquement des sous domains si besoin
     * @return    void
     */
    private function AutoGenSubDomains($fromsave = true)
    {
        $out = '<ul><li><div style="color:green;font-weight:bold" class="debug">Auto gen  ' . $this->Url . '</div><ul>';
        //vérification de l'existence d'une template
        $dt = Sys::getOneData('Parc', 'DomainTemplate/Domain/' . $this->Id);
        if ($dt) {
            $Obj = new xml2array($dt->Contenu);
            $TabXml[0] = $Obj->Tableau["TEMPLATE"];
            //Traitement des donn�es du tableau
            $conf = Conf::parseOnly($TabXml);
        } else {
            $obj = empty($GLOBALS) ? Sys::$Conf : $GLOBALS["Systeme"]->Conf;
            $conf = $obj->get("MODULE::PARC::AUTO_DOMAIN");
        }
        //sous domaines
        $out .= "<li>Check <strong>subdomains</strong><ul>\r\n";
        foreach ($conf['SOUS_DOMAINE'] as $sub) {
            $KEObj = genericClass::createInstance('Parc', $sub['TYPE']);
            switch ($sub['TYPE']) {
                case "Subdomain":
                    //test existence
                    $e = Sys::$Modules['Parc']->callData('Domain/' . $this->Id . '/Subdomain/Url=' . $sub['CN'], false, 0, 1, 'DESC', 'Id', 'COUNT(*)');
                    if (!$e[0]['COUNT(*)']) {
                        $e = Sys::$Modules['Parc']->callData('Domain/' . $this->Id . '/CNAME/Dnsdomainname=' . substr($sub['CN'], 2), false, 0, 1, 'DESC', 'Id', 'COUNT(*)');
                        if (!$e[0]['COUNT(*)']) {
                            $KEObj->Url = $sub['CN'];
                            $KEObj->IP = $sub['IP'];
                            $KEObj->AddParent($this);
                            $KEObj->Save();
                            $out .= '<li><div style="color:red" class="debug">Add A ' . $KEObj->Url . '</div> </li>';
                        }
                    }
                    break;
                case "CNAME":
                    //test existence
                    $e = Sys::$Modules['Parc']->callData('Domain/' . $this->Id . '/Subdomain/Url=A:' . $sub['DNSDOMAINNAME'], false, 0, 1, 'DESC', 'Id', 'COUNT(*)');
                    if (!$e[0]['COUNT(*)']) {
                        $e = Sys::$Modules['Parc']->callData('Domain/' . $this->Id . '/CNAME/Dnsdomainname=' . $sub['DNSDOMAINNAME'], false, 0, 1, 'DESC', 'Id', 'COUNT(*)');
                        if (!$e[0]['COUNT(*)']) {
                            $KEObj->Nom = $sub['CN'];
                            $KEObj->Dnscname = $sub['DNSCNAME'];
                            $KEObj->Dnsdomainname = $sub['DNSDOMAINNAME'];
                            $KEObj->AddParent($this);
                            $KEObj->Save();
                            $out .= '<li><div style="color:red" class="debug">Add CNAME ' . $KEObj->Nom . '</div> </li>';
                        }
                    }
                    break;
            }
        }
        $out .= "</ul></li>\r\n";

        $out .= "<li>Check <strong>nameservers</strong><ul>\r\n";
        //name server
        foreach ($conf['NAME_SERVER'] as $sub) {
            //test existence
            $e = Sys::$Modules['Parc']->callData('Domain/' . $this->Id . '/' . $sub['TYPE'] . '/Nom=' . $sub['CN'], false, 0, 1, 'DESC', 'Id', 'COUNT(*)');
            if (!$e[0]['COUNT(*)']) {
                $KEObj = genericClass::createInstance('Parc', $sub['TYPE']);
                $KEObj->Nom = $sub['CN'];
                $KEObj->Dnscname = $sub['DNSCNAME'];
                $KEObj->Dnsdomainname = $this->Url . '.';
                $KEObj->AddParent($this);
                //recherche du serveur de nom associé
//				$Sn = Sys::$Modules['Parc']->callData('Server/DNSNom='.$sub['DNSCNAME']);
//				$Sn = genericClass::createInstance('Parc',$Sn[0]);
//				$KEObj->AddParent($Sn);

                $KEObj->Save();
                $out .= '<li><div style="color:red" class="debug">Add Nameserver ' . $KEObj->Nom . '</div> </li>';
            }
        }
        $out .= "</ul></li>\r\n";

        $out .= "<li>Check <strong>mailservers</strong><ul>\r\n";
        //mail server
        if (is_array($conf['MAIL_SERVER'])) {
            if (isset($conf['MAIL_SERVER']['TYPE'])) {
                //test existence
                $e = Sys::$Modules['Parc']->callData('Domain/' . $this->Id . '/' . $conf['MAIL_SERVER']['TYPE'] . '/Nom=' . $conf['MAIL_SERVER']['CN'], false, 0, 1, 'DESC', 'Id', 'COUNT(*)');
                if (!$e[0]['COUNT(*)']) {
                    $KEObj = genericClass::createInstance('Parc', $conf['MAIL_SERVER']['TYPE']);
                    $KEObj->Nom = $conf['MAIL_SERVER']['CN'];
                    $KEObj->Dnscname = $conf['MAIL_SERVER']['DNSCNAME'];
                    $KEObj->Poids = (isset($conf['MAIL_SERVER']['WEIGHT']) && $conf['MAIL_SERVER']['WEIGHT'] > 0) ? $conf['MAIL_SERVER']['WEIGHT'] : '10';
                    $KEObj->AddParent($this);
                    $KEObj->Save();
                    $out .= '<li><div style="color:red" class="debug">Add Mailserver ' . $KEObj->Nom . '</div> </li>';
                }
            } else {
                foreach ($conf['MAIL_SERVER'] as $ms) {
                    $e = Sys::$Modules['Parc']->callData('Domain/' . $this->Id . '/' . $ms['TYPE'] . '/Nom=' . $ms['CN'], false, 0, 1, 'DESC', 'Id', 'COUNT(*)');
                    if (!$e[0]['COUNT(*)']) {
                        $KEObj = genericClass::createInstance('Parc', $ms['TYPE']);
                        $KEObj->Nom = $ms['CN'];
                        $KEObj->Dnscname = $ms['DNSCNAME'];
                        $KEObj->Poids = (isset($ms['WEIGHT']) && $ms['WEIGHT'] > 0) ? $ms['WEIGHT'] : '10';
                        $KEObj->AddParent($this);
                        $KEObj->Save();
                        $out .= '<li><div style="color:red" class="debug">Add Mailserver ' . $KEObj->Nom . '</div> </li>';
                    }
                }
            }
        }
        $out .= "</ul></li>\r\n";
        $out .= "</ul></li></ul>";
        if (!$fromsave) echo $out;
    }


    function checkIntegrity()
    {
        $error = 0;
        echo '<ul><li><div style="color:green;font-weight:bold" class="debug">Check domain  ' . $this->Url . '</div><ul>';
        //Verification des données erronées
        $f = Sys::$Modules['Parc']->callData('Domain/' . $this->Id . '/CNAME', false, 0, 100);
        if (is_array($f)) foreach ($f as $cn) {
            $cn = genericClass::createInstance('Parc', $cn);
            if ($cn->Nom == "" && $cn->Dnscname == "") {
                echo '<li><div style="color:red" class="debug">Delete bad entry</div> </li>';
                $error++;
                $cn->Delete();
            }
        }

        echo "<li>Search <strong>doublons</strong><ul>\r\n";
        //on recense chaque adresse A et on verifie qu'il n'y est pas un doublon en CNAME
        $e = Sys::$Modules['Parc']->callData('Domain/' . $this->Id . '/Subdomain', false, 0, 1000);
        if (is_array($e)) foreach ($e as $d) {
            if (substr($d['Url'], 2) != "") {
                $f = Sys::$Modules['Parc']->callData('Domain/' . $this->Id . '/CNAME/Dnsdomainname=' . substr($d['Url'], 2), false, 0, 2);
                if (is_array($f)) foreach ($f as $cn) {
                    $cn = genericClass::createInstance('Parc', $cn);
                    echo '<li><div style="color:red" class="debug">Delete doublon CNAME ' . $cn->Nom . '</div> </li>';
                    $error++;
                    $cn->Delete();
                }
            }
            $f = Sys::$Modules['Parc']->callData('Domain/' . $this->Id . '/Subdomain/Url=' . $d['Url'], false, 0, 20);
            if (is_array($f) && sizeof($f) > 1) foreach ($f as $k => $cn) {
                if ($k > 0) {
                    $cn = genericClass::createInstance('Parc', $cn);
                    $cn->Delete();
                    $error++;
                    echo '<li><div style="color:red" class="debug">Delete doublon A ' . $cn->Nom . '</div> </li>';
                }
            }
        }
        echo "</ul></li>\r\n";

        echo "<li>Check <strong>ldap</strong><ul>\r\n";
        //Verification de la liaison ldap pour chacun des éléments
        $e = Sys::$Modules['Parc']->callData('Domain/' . $this->Id . '/Subdomain', false, 0, 1000);
        if (is_array($e)) foreach ($e as $d) {
            $cn = genericClass::createInstance('Parc', $d);
            if (!$cn->checkIntegrity()) {
                echo '<li><div style="color:red" class="debug">Ajout ' . $cn->Url . '</div></li>';
                $error++;
                $cn->Save();
            }
        }
        echo "</ul></li>\r\n";

        if ($error) {
            echo '<li><div style="color:red" class="debug">' . $error . ' errors found</div></li>';
            //sauvegarde du domaine et incrementation du numero de serie
            $this->Save();
        } else {
            echo '<li><div style="color:green" class="debug">no error found</div></li>';
        }

        echo "</ul></li></ul>";
        if ($error) {
            $this->updateOnSave = false;
            $this->Save(false);
        }

        $this->AutoGenSubDomains(false);
    }

    public function Redirect ($params){
        $step = 0;
        if(!empty($params['step']))
            $step = $params['step'];

        switch($step) {
            case 1 : //Evenements sur toute une journée avec  jours d'ouverture
                $nd = $params['redirect'];

                $A = $this->getOneChild('Subdomain/Nom=A:');
                if (!$A) {
                    $A =genericClass::createInstance("Parc",'Subdomain');
                    $A->TTL = 3600;
                }
                $A->IP = '158.255.102.117';
                $A->Save();

                $host = Sys::getOneData('Parc','Host/Interne=1&&Redirect=1');
                if(empty($host)) return false;

                $ap = $host->getOneChild('Apache/ApacheServerName=' . $this->Url);
                if(empty($ap)){
                    $ap = genericClass::createInstance('Parc','Apache');
                    $ap->ApacheServerName = $this->Url;

                }
                $ap->ApacheConfig = 'RedirectPermanent / http://abc.example.com/';
                $ap->Save();

                break;
            default: //Initialisation
                return array (
                    'template'=>"redirectDomain",
                    'step'=>1,
                    'callNext'=>array (
                        'nom'=>'Redirect',
                        'title'=>'Redirection'
                    ),
                    'funcTempVars' => array(
                        'step'=> $step
                    )
                );

        }

    }
}