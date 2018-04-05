<?php

class Parc_Contact extends genericClass {
    var $Role = 'PARC_CONTACT';


	/**
	 * Force la vérification avant enregistrement
	 * @param	boolean	Enregistrer aussi sur LDAP
	 * @return	void
	 */
	public function Save( $synchro = true ) {
        $this->AccesUser = strtolower($this->AccesUser);
        $this->AccesUser = trim($this->AccesUser);
        $this->AccesUser = Utils::CheckSyntaxe($this->AccesUser);

        // Enregistrement si pas d'erreur
        parent::Save();
        if($this->setUser()){
            return true;
        }

        return false;
	}

	/**
	 * Creation de l'utilisateur pour ce client
	 */
	public function setUser() {
		//récupération du groupe de stockage des accès clients
		$u = $this->getOneParent('User');
		$cli = $this->getOneParent('Client');

		if ($this->AccesActif){
            if($cli){
                //récupération du groupe de stockage des accès clients
                $grp = Group::getGroupFromRole($this->Role);
                if (!sizeof($grp)){
                    //Erreur
                    $this->AddError(Array("Message"=>"Veuillez mettre le module à jour, les roles ne sont pas définis"));
                    return;
                }else $grp = $grp[0];

                $sGrp = $grp->getOneChild('Group/Nom='.strtoupper(Utils::KEAddSlashes($cli->Nom)));
                if($sGrp){
                    $grp = $sGrp;
                }else{
                    $sGrp = genericClass::createInstance('Systeme','Group');
                    $sGrp->Nom = strtoupper($cli->Nom);
                    $sGrp->addParent($grp);
                    $sGrp->Save();

                    $grp = $sGrp;
                }

            }else{
                return false;
            }

			//Vérification des propriétées
			if (!empty($this->AccesUser)&&!empty($this->AccesPass)){
				if (!$u || !sizeof($u)){
					//creation de l'utilisateur
					$u = genericClass::createInstance('Systeme','User');
					$u->Login = $this->AccesUser;
					$u->Pass = md5($this->AccesPass);
					$u->Mail = $this->Email;
					$u->Actif = true;
					$u->AddParent($grp);
					$u->Save();
					$this->AddParent($u);
					parent::Save();
				}else{

					//mise à jour utilisateur
					$u->Login = $this->AccesUser;
					$u->Pass = md5($this->AccesPass);
					$u->Mail = $this->Email;
					$u->Actif = true;
					$u->AddParent($grp);
					$u->Save();
				}
                $this->updateGuacamoleUser();
			}else{
				//Erreur
				$this->AddError(Array("Message"=>"Veuillez saisir toutes les informations d'accès web sur la fiche client"));
				
			}
		}else{
			if ($u){
				//Si utilisateur alors on désasctive son accès
				$u->Actif = false;
				$u->Save();
			}
		}
		return true;
	}

    /**
     * Verification des erreurs possibles
     * @param	boolean	Verifie aussi sur LDAP
     * @return	Verification OK ou NON
     */
    public function Verify( $synchro = true ) {
        if(!$this->NomLDAP || empty($this->NomLDAP) || $this->NomLDAP == ''){
            $chaine = $this->Email;
            $chaine = str_replace("°", "-", $chaine);
            $chaine = utf8_decode($chaine);
            $chaine = stripslashes($chaine);
            $chaine = preg_replace('`\s+`', '-', trim($chaine));
            $chaine = str_replace("'", "-", $chaine);
            $chaine = str_replace("&", "et", $chaine);
            $chaine = str_replace('"', "-", $chaine);
            $chaine = str_replace("?", "", $chaine);
            $chaine = str_replace("+", "-", $chaine);
            $chaine = str_replace("=", "-", $chaine);
            $chaine = str_replace("!", "", $chaine);
            $chaine = str_replace(".", "", $chaine);
            $chaine = str_replace("%", "", $chaine);
            $chaine = str_replace("²", "", $chaine);
            $chaine = preg_replace('`[\,\ \(\)\+\'\/\:]`', '-', trim($chaine));
            $chaine = strtr($chaine,utf8_decode("ÀÁÂÃÄÅàáâãäå@ÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ?>#<+;,²³°"),"aaaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn-------23o");
            $chaine = preg_replace('`[-]+`', '-', trim($chaine));
            $chaine = utf8_encode($chaine);
            $this->NomLDAP = $chaine;
        }

        if(parent::Verify()) {
            $this->_isVerified = true;
            //si acces web alors il faut vérifier identifiant / moty de passe et email
            if ($this->AccesActif){
                if (empty($this->AccesUser)){
                    $this->AddError(Array("Message"=>"Veuillez renseigner l'identifiant pour l'accès web"));
                    $this->_isVerified = false;
                }
                if (empty($this->AccesPass)){
                    $this->AddError(Array("Message"=>"Veuillez renseigner le mot de passe pour l'accès web"));
                    $this->_isVerified = false;
                }
                if (empty($this->Email)){
                    $this->AddError(Array("Message"=>"Veuillez renseigner l'adresse mail pour l'accès web"));
                    $this->_isVerified = false;
                }
                if (!$this->_isVerified)
                    return false;
            }



            if($synchro) {

                // Outils
                $dn = 'cn='.$this->NomLDAP.',ou=clients,'.PARC_LDAP_BASE;

                // Verification à jour
                $res = Server::checkTms($this);
                if($res['exists']) {
                    if(!$res['OK']) {
                        $this->AddError($res);
                        $this->_isVerified = false;
                    }
                    else {
                        // Déplacement
                        $res = Server::ldapRename($this->LdapDN, 'cn='.$this->NomLDAP, 'ou=clients,'.PARC_LDAP_BASE);
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
                        $this->LdapGid = $entry['gidnumber'];
                        $this->LdapTms = $res['LdapTms'];
                    }
                    else {
                        $this->AddError($res);
                        $this->_isVerified = false;
                    }
                }

            }

        }
        else {

            $this->_isVerified = false;

        }

        return $this->_isVerified;

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
        $entry['cn'] = $this->NomLDAP;
        //$entry['sn'] = $this->NomLDAP;
        if ($this->AccesActif) {
            $entry['uid'] = $this->AccesUser;
            $entry['userPassword'] = '{MD5}' . base64_encode(pack("H*", md5($this->AccesPass)));
        }else{
            $entry['uid'] = $this->NomLDAP;
            $entry['userPassword'] = '{MD5}' . base64_encode(pack("H*", md5('ZOBzobzoboizojhfdslhj')));
        }
        //$entry['displayname'] = $this->Nom.' '.$this->Prenom;
//        if($new) {
        $entry['homedirectory'] = '/home/'.$this->AccesUser;
        $entry['objectclass'][0] = 'posixGroup';
        $entry['objectclass'][1] = 'top';
        $entry['objectclass'][2] = 'posixAccount';
        $entry['objectclass'][3] = 'shadowAccount';
        $entry['uidnumber'] = Server::getNextUid();
        $entry['gidnumber'] = Server::getNextGid();
//        }
        return $entry;
    }
    /**
     * Suppression de la BDD
     * Relai de cette suppression à LDAP
     * On utilise aussi la fonction de la superclasse
     * @return	void
     */
    public function Delete() {
        Server::ldapDelete($this->LdapID);
        $this->deleteGuacamoleUser();
        parent::Delete();
    }

    private function updateGuacamoleUser(){
        //test des serveurs guacamole
        $servs = Sys::getData('Parc','Server/Guacamole=1');
        foreach ($servs as $serv) {
            $dbGuac = new PDO('mysql:host='.$serv->IP.';dbname=guacamole', $serv->guacAdminUser, $serv->guacAdminPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
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
//                $query = "INSERT IGNORE INTO `guacamole_system_permission` (user_id,permission) VALUES ('" . $usr[0]['user_id'] . "','ADMINISTER')";
//                $q = $dbGuac->query($query);

                //Maj de ses connexions
                $query = "";
                $cons = $this->getChildren('DeviceConnexion');
                foreach ($cons as $con) {
                    $query .= "INSERT IGNORE INTO `guacamole_connection_permission` (user_id,connection_id,permission) VALUES ('" . $usr[0]['user_id'] . "','" . $con->GuacamoleId . "','READ');";
                }
                if($query != '')
                    $q = $dbGuac->query($query);
            } else if (isset($this->AccesUser) && $this->AccesUser != '' && $this->AccesUser != null && (!isset($this->AccesPass) || $this->AccesPass == '' || $this->AccesPass == null)) {
                //$this->addError(array('Message' => 'La valeur du champ AccesPass est nulle ou non définie alors que le champ AccesUser est défini.', "Prop" => 'AccesPass'));
            }
        }
        return true;
    }
    /**CurrentVersion
     * deleteGuacamole
     *
     */
    private function deleteGuacamoleUser() {
        $servs = Sys::getData('Parc','Server/Guacamole=1');
        foreach ($servs as $serv) {

            $dbGuac = new PDO('mysql:host='.$serv->IP.';dbname=guacamole',  $serv->guacAdminUser, $serv->guacAdminPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $dbGuac->query("SET AUTOCOMMIT=1");
            $dbGuac->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if (isset($this->AccesUser) && $this->AccesUser != '' && $this->AccesUser != null && isset($this->AccesPass) && $this->AccesPass != '' && $this->AccesPass != null) {
                $query = "DELETE FROM `guacamole_user` WHERE username = '" . $this->AccesUser . "'";
                $q = $dbGuac->query($query);
            }
        }
    }

}