<?php
require_once 'Class/Lib/Zabbix.class.php';

class Parc_Technicien extends genericClass {
    var $Role = 'PARC_TECHNICIEN';


	/**
	 * Force la vérification avant enregistrement
	 * @param	boolean	Enregistrer aussi sur LDAP
	 * @return	void
	 */
	public function Save( $synchro = true ) {
	    //correction identifiant acces web
        $this->AccesUser = strtolower($this->AccesUser);
        $this->AccesUser = trim($this->AccesUser);
        $this->AccesUser = Utils::CheckSyntaxe($this->AccesUser);
		// Enregistrement si pas d'erreur
        if($this->setUser(true)){
            parent::Save();
            $this->setUser();
            return true;
        }else return false;
	}

	/**
	 * Creation de l'utilisateur pour ce client
	 */
	public function setUser($check = false) {
		//récupération du groupe de stockage des accès clients
		$u = $this->getOneParent('User');
		if (!$u) $u = Sys::getOneData('Systeme','User/Login='.$this->AccesUser);
		$grp = Group::getGroupFromRole('PARC_TECHNICIEN');
		if ($this->AccesActif){
            if(!sizeof($grp)){
                return false;
            }
            $grp = $grp[0];
            //Vérification des propriétées
			if (!empty($this->AccesUser)&&!empty($this->AccesPass)){
				if (!$u){
					//creation de l'utilisateur
					$u = genericClass::createInstance('Systeme','User');
                    $u->Initiales = $this->IdGestion;
                    $u->Nom = $this->Nom;
                    $u->Prenom = $this->Prenom;
					$u->Login = $this->AccesUser;
					$u->Pass = md5($this->AccesPass);
					$u->Mail = $this->Email;
					$u->Actif = true;
					$u->AddParent($grp);
					if ($check){
					    if ($u->Verify())
					        return true;
					    else{
					        $this->Error = array_merge($this->Error,$u->Error);
                            return false;
                        }
                    }else{
                        if ($u->Save()) {
                            $this->AddParent($u);
                            parent::Save();
                        }else{
                            $this->Error = array_merge($this->Error,$u->Error);
                            return false;
                        }
                    }
				}else{
					//mise à jour utilisateur
                    $u->Initiales = $this->IdGestion;
                    $u->Nom = $this->Nom;
                    $u->Prenom = $this->Prenom;
                    $u->Login = $this->AccesUser;
					$u->Pass = md5($this->AccesPass);
					$u->Mail = $this->Email;
					$u->Actif = true;
					$u->AddParent($grp);
                    if ($check){
                        if ($u->Verify())
                            return true;
                        else{
                            $this->Error = array_merge($this->Error,$u->Error);
                            return false;
                        }
                    }else{
                        if (!$u->Save()) {
                            $this->Error = array_merge($this->Error, $u->Error);
                            return false;
                        }else{
                            $this->AddParent($u);
                            parent::Save();
                        }
                    }
				}
				//mise à jour de l'utilisateur ldap
                $this->updateLdapUser();
                //mise à jour de l'utilisateur guacamole
                $this->updateGuacamoleUser();
                //mise à jour de l'utilisateur zabbix
                $this->updateZabbixUser();
			}else{
				//Erreur
				$this->AddError(Array("Message"=>"Veuillez saisir toutes les informations d'accès web sur la fiche client"));
                return false;
			}
		}else{
			if ($u){
				//Si utilisateur alors on désasctive son accès
				$u->Actif = false;
				$u->Save();
                //mise à jour de l'utilisateur ldap
                $this->deleteLdapUser();
                //mise à jour de l'utilisateur guacamole
                $this->deleteGuacamoleUser();
                //mise à jour de l'utilisateur zabbix
                $this->deleteZabbixUser();
			}
		}
		return true;
	}

    private function updateLdapUser() {
        $dn = 'cn='.$this->AccesUser.',ou=users,'.PARC_LDAP_BASE;

        // Verification à jour
        $res = Server::checkTms($this);
        if($res['exists']) {
            if(!$res['OK']) {
                $this->AddError($res);
                $this->_isVerified = false;
            }
            else {
                // Déplacement
                $res = Server::ldapRename($this->LdapDN, 'cn='.$this->AccesUser, 'ou=users,'.PARC_LDAP_BASE);
                if($res['OK']) {
                    // Modification
                    $entry = $this->buildEntry(false);
                    $res = Server::ldapModify($this->LdapID, $entry);

                    if($res['OK']) {
                        // Tout s'est passé correctement
                        $this->LdapDN = $dn;
                        $this->LdapTms = $res['LdapTms'];
                    }
                    else {
                        // Erreur
                        $this->AddError($res);
                        $this->_isVerified = false;
                        // Rollback du déplacement
                        $tab = explode(',', $this->LdapDN);
                        $leaf = array_shift($tab);
                        $rest = implode(',', $tab);
                        Server::ldapRename($dn, $leaf, $rest);
                    }
                }
                else {
                    $this->AddError($res);
                    $this->_isVerified = false;
                }
            }

        }
        else {
            ////////// Nouvel élément
            $entry = $this->buildEntry();
            $res = Server::ldapAdd($dn, $entry);
            if($res['OK']) {
                $this->LdapDN = $dn;
                $this->LdapID = $res['LdapID'];
                $this->LdapTms = $res['LdapTms'];
            }
            else {
                $this->AddError($res);
                $this->_isVerified = false;
            }
        }
        parent::Save();
        return true;
    }
    /**
     * Configuration d'une nouvelle entrée type
     * Utilisé lors du test dans Verify
     * puis lors du vrai ajout dans Save
     * @param	boolean		Si FALSE c'est simplement une mise à jour
     * @return	Array
     */
    private function buildEntry( $new = true ) {
        $entry = array();
        $entry['userPassword'] = '{MD5}'.base64_encode(pack("H*",md5($this->AccesPass)));
        $entry['displayname'] = $this->Nom.' '.$this->Prenom;
        $entry['homedirectory'] = '/home/'.$this->AccesUser;
        $entry['uid'] = $this->AccesUser;
        $entry['cn'] = $this->AccesUser;
        $entry['sn'] = $this->AccesUser;
        if($new) {
            $entry['objectclass'][0] = 'top';
            $entry['objectclass'][1] = 'inetOrgPerson';
            $entry['objectclass'][2] = 'posixAccount';
            $entry['uidnumber'] = Server::getNextUid();
            $entry['gidnumber'] = 1000;
        }
        return $entry;
    }

    /**
     * deleteLdapUser
     * Supprime un utilisateur ldap
     */
    private function deleteLdapUser() {
        if (!empty($this->LdapID)) {
            Server::ldapDelete($this->LdapID);
        }
    }

    private function updateGuacamoleUser(){
        $servs = Sys::getData('Parc','Server/Guacamole=1');
        foreach ($servs as $serv) {
            $dbGuac = new PDO('mysql:host=' . $serv->IP . ';dbname=guacamole', $serv->SshUser, $serv->SshPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $dbGuac->query("SET AUTOCOMMIT=1");
            $dbGuac->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (isset($this->AccesUser) && $this->AccesUser != '' && $this->AccesUser != null && isset($this->AccesPass) && $this->AccesPass != '' && $this->AccesPass != null) {
                $query = "SELECT * FROM `guacamole_user` WHERE username = '" . $this->AccesUser . "'";
                $q = $dbGuac->query($query);
                $result = $q->fetchALL(PDO::FETCH_ASSOC);
                if (sizeof($result) > 0) {
                    $usr = $result;
                    $query = "UPDATE `guacamole_user` SET password_hash=UNHEX(UPPER(SHA2('" . $this->AccesPass . "',256))),password_date='" . date("Y-m-d H:i:s") . "',password_salt=null  WHERE username='" . $this->AccesUser . "'";
                    $q = $dbGuac->query($query);
                } else {
                    $query = "INSERT INTO `guacamole_user` (username,password_hash,password_date) VALUES ('" . $this->AccesUser . "',UNHEX(UPPER(SHA2('" . $this->AccesPass . "',256))),'" . date("Y-m-d H:i:s") . "')";
                    $q = $dbGuac->query($query);
                }
                $query = "SELECT * FROM `guacamole_user` WHERE username = '" . $this->AccesUser . "'";
                $q = $dbGuac->query($query);
                $result = $q->fetchALL(PDO::FETCH_ASSOC);
                $usr = $result;
                //creation des droits
                $query = "INSERT IGNORE INTO `guacamole_system_permission` (user_id,permission) VALUES ('" . $usr[0]['user_id'] . "','ADMINISTER')";
                $q = $dbGuac->query($query);
            } else if (isset($this->AccesUser) && $this->AccesUser != '' && $this->AccesUser != null && (!isset($this->AccesPass) || $this->AccesPass == '' || $this->AccesPass == null)) {
                //$this->addError(array('Message' => 'La valeur du champ AccesPass est nulle ou non définie alors que le champ AccesUser est défini.', "Prop" => 'AccesPass'));
            }
            return true;
        }
    }
    /**
     * deleteGuacamole
     *
     */
    private function deleteGuacamoleUser() {
        $servs = Sys::getData('Parc','Server/Guacamole=1');
        foreach ($servs as $serv) {
            $dbGuac = new PDO('mysql:host=' . $serv->IP . ';dbname=guacamole', $serv->SshUser, $serv->SshPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $dbGuac->query("SET AUTOCOMMIT=1");
            $dbGuac->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if (isset($this->AccesUser) && $this->AccesUser != '' && $this->AccesUser != null && isset($this->AccesPass) && $this->AccesPass != '' && $this->AccesPass != null) {
                $query = "DELETE FROM `guacamole_user` WHERE username = '" . $this->AccesUser . "'";
                $q = $dbGuac->query($query);
            }
        }
    }
    /**
     * updateZabbixUser
     * Create and modify zabbix user
     */
    private function updateZabbixUser() {
        $servs = Sys::getData('Parc','Server/Zabbix=1');
        foreach ($servs as $serv) {

            //vérification de l'existence
            $out = Zabbix::getUser(array(
                'filter' => array(
                    'alias' => "$this->AccesUser"
                )
            ));
            if (!sizeof($out)) {
                //création de l'utilisateur
                Zabbix::createUser($this->AccesUser, array(
                    "alias" => "$this->AccesUser",
                    "name" => "$this->Nom",
                    "surname" => "$this->Prenom",
                    "type" => "3",
                    "passwd" => "$this->AccesPass",
                    "usrgrps" => array(
                        array('usrgrpid' => 7)
                    ),
                ));
            } else {
                //mise à jour de l'utilisateur
                Zabbix::updateUser(array(
                    "userid" => $out[0]->userid,
                    "passwd" => "$this->AccesPass"
                ));
            }
        }
    }
    /**
     * deleteZabbixUser
     *
     */
    private function deleteZabbixUser() {
        $servs = Sys::getData('Parc','Server/Zabbix=1');
        foreach ($servs as $serv) {

            //vérification de l'existence
            $out = Zabbix::getUser(array(
                'filter' => array(
                    'alias' => "$this->AccesUser"
                )
            ));
            if (sizeof($out)) {
                //mise à jour de l'utilisateur
                Zabbix::deleteUser(array(
                    "userid" => $out[0]->userid
                ));
            }
        }
    }

    /**
     * Suppression de la BDD
     * Relai de cette suppression à LDAP
     * On utilise aussi la fonction de la superclasse
     * @return	void
     */
    public function Delete() {
        //suppression ldap
        Server::ldapDelete($this->LdapID);
        //suppression de guacamole
        $this->deleteGuacamoleUser();
        //suppression de zabbix
        $this->deleteZabbixUser();
        //suppression user
        $usr = $this->getOneParent('User');
        if (is_object($usr))
            $usr->Delete();
        parent::Delete();
    }


}