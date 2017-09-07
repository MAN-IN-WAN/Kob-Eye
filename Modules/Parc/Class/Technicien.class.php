<?php

class Parc_Contact extends genericClass {
    var $Role = 'PARC_CLIENT';


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
		$cli = $this->getOneParent('Client');

		if ($this->AccesActif){
            if($cli){
                $uCli = Sys::getOneData('Systeme','User/Client/'.$cli->Id);
                if($uCli){
                    $grp = $uCli->getOneParent('Group');
                    if(!$grp){
                        $base = Group::getGroupFromRole($this->Role);
                        $base = $base[0];
                        $uGrp = $base->getOneChild('Group/Nom='.strtoupper(Utils::KEAddSlashes($cli->Nom)));
                        if($uGrp){
                            $grp = $uGrp;
                        } else {
                            $grp = genericClass::createInstance('Systeme','Group');
                            $grp->Nom = strtoupper($cli->Nom);
                            $grp->addParent($base);
                            $grp->Save();
                        }
                    }
                } else{
                    $base = Group::getGroupFromRole($this->Role);
                    $base = $base[0];
                    $uGrp = $base->getOneChild('Group/Nom='.strtoupper(Utils::KEAddSlashes($cli->Nom)));
                    if($uGrp){
                        $grp = $uGrp;
                    } else {
                        $grp = genericClass::createInstance('Systeme','Group');
                        $grp->Nom = strtoupper($cli->Nom);
                        $grp->addParent($base);
                        $grp->Save();
                    }
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
	}




}