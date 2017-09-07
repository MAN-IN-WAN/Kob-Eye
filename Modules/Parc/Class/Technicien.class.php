<?php

class Parc_Technicien extends genericClass {
    var $Role = 'PARC_TECHNICIEN';


	/**
	 * Force la vérification avant enregistrement
	 * @param	boolean	Enregistrer aussi sur LDAP
	 * @return	void
	 */
	public function Save( $synchro = true ) {
		// Enregistrement si pas d'erreur
        parent::Save();
        $this->setUser();

	}

	/**
	 * Creation de l'utilisateur pour ce client
	 */
	public function setUser() {
		//récupération du groupe de stockage des accès clients
		$u = $this->getOneParent('User');
		$grp = Group::getGroupFromRole('PARC_TECHNICIEN');

		if ($this->AccesActif){
            if(!$grp){
                return false;
            }
			//Vérification des propriétées
			if (!empty($this->AccesUser)&&!empty($this->AccesPass)){
				if (!$u || !sizeof($u)){
					//creation de l'utilisateur
					$u = genericClass::createInstance('Systeme','User');
                    $u->Initiales = $this->IdGestion;
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
                    $u->Initiales = $this->IdGestion;
					$u->Login = $this->AccesUser;
					$u->Pass = md5($this->AccesPass);
					$u->Mail = $this->Email;
					$u->Actif = true;
					$u->AddParent($grp);
					$u->Save();
				}
				//mise à jour de l'utilisateur ldap
                $this->updateLdapUser();
                //mise à jour de l'utilisateur guacamole
                //mise à jour de l'utilisateur zabbix

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
			}
		}
	}

    private function updateLdapUser() {
        $KEServer = $this->getKEServer();
        $dn = 'cn='.$this->AccessUser.',ou=users,'.PARC_LDAP_BASE;

        // Verification à jour
        $res = Server::checkTms($this);
        if($res['exists']) {
            if(!$res['OK']) {
                $this->AddError($res);
                $this->_isVerified = false;
            }
            else {
                // Déplacement
                $res = Server::ldapRename($this->LdapDN, 'cn='.$this->AccessUser, 'cn='.$this->AccessUser.',ou=users,'.PARC_LDAP_BASE);
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
        $entry['userPassword'] = '{MD5}'.md5($this->AccesPass);
        $entry['displayname'] = $this->Nom.' '.$this->Prenom;
        $entry['homedirectory'] = '/home/'.$this->AccessUser;
        $entry['uid'] = $this->AccessUser;
        if($new) {
            $entry['objectclass'][0] = 'top';
            $entry['objectclass'][1] = 'inetOrgPerson';
            $entry['objectclass'][2] = 'posixAccount';
            $entry['uidnumber'] = $this->_KEServer->getNextUid();
            $entry['gidnumber'] = 1000;
            $entry['cn'] = $this->AccessUser;
            $entry['sn'] = $this->AccessUser;
        }
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
        parent::Delete();
    }
}