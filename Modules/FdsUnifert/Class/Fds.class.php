<?PHP

class Fds extends genericClass{
        
        /**
	 * getVersion
	 * Retourne la version de le fiche de sécurité courante à l'aide du nom du FichierFds qui lui à été ajouté le plus récement
	 * @return String l'identifiant de la version
	 */
        public function getVersion(){
                
                $file = Sys::getData('FdsUnifert','Fds/'.$this->Id.'/FichierFDS',0,1,'DESC','tmsCreate');
                if(!sizeof($file))
                        return false;
                
                $fVersion = $file[0]->Titre;
                //TODO traitement du nom pour en extraire le numéro de version
                
                return  $fVersion;
        }
        
        
        /**
	 * checkSendings
	 * Verifié les envois liés à cette fiche
	 * @return Boolean / Liste des client à qui cela a été envoyé
	 */
        public function checkSendings(){
                if (!$this->Obligatoire)
                        return false;
                
                //On recup la version et les clients liés a cette fiche de sécurité
                $version = $this->getVersion();
                $clients = $this->getChildren('Client');
                $first = 1;
                
		$rapp = array();
                //Pour chaque client on vérifie si un envoi a été fait pour cette version si non on effectue le premier envoi
                foreach ($clients as $cli){
                        $sendings = $cli->getChildren('EnvoiFds/Obsolete=0');
                        foreach($sendings as $sending){
                                $fdsP = $sending->getParents('Fds');
                                $fdsP = $fdsP[0];
                                if($fdsP->Id == $this->Id) $first = 0;
                                break;
                        }
                        $res = array_walk($sendings,function(&$send){
                                $send = $send->VersionFDS;
                        });
                        if (!in_array($version,$sendings)){
                                //ENVISAGER Test du retour send et gestion des erreurs
                                $this->send($cli,$version,$first);
				$rapp[] = $cli;
                                
                                //Gestion de l'envoi au manager du groupe
                                $res = $this->groupManagerSend($cli,$version);
                                if($res)
                                        $rapp[] = $res;
                        }
                }
                return $rapp;
        }
        
        
        /**
	 * groupManagerSend
	 * Check si le client a un parent et donc appartiens à un groupe.
	 * Si c'est le cas on verifie qu'aucun membre du groupe n'a eu d'envoi pour cette fiche/version
	 * Si aucun n'envoi n'a été fait on envoi au manager du groupe un mail.
	 *
	 * @param Object $client Objet Client (Client extends genericClass)
	 * @param String $version Version de la FDS
	 * @return bool / Object client du manager à stocker dans le rapport.
	 */
        public function groupManagerSend($cli,$version){
                $manager = $cli->getParents('Client');
                $manager = isset($manager[0])? $manager[0]:false;
                if (!is_object($manager)) return false; //Si pas de manager on est pas dans un groupe donc on zappe
                
                $siblings = $manager->getChildren('Client'); //Recup des membres du groupe
                foreach($siblings as $sib){
                        if($sib->Id == $cli->Id) continue; 
                        $infoSib = $this->getInfosCl($sib);
                        echo $version;
                        if($infoSib['version'] === $version) {
                                return false; //Si un membre à déja un envoi pour cette version on skip
                        }
                }
                
                //Si premier envoi pour cette version on envoi un mail au manager:
                $adminMail = $GLOBALS['Systeme']->Conf->get('GENERAL::INFO::ADMIN_MAIL');
                $mailTo = '';
                $temp = array();
                
                $contacts = $manager->getChildren('Contact');
                foreach ($contacts as $contact){
                        if($contact->Fds){
                                $temp[] = $contact->Mail;
                        }
                }
                $mailTo = implode(', ',$temp);
                $mailTo = $mailTo != '' ? $mailTo : $manager->Mail;
                
                $sujet = 'Mise à disposition de la fiche de sécurité '.$this->getVersion();
                
                $content = '    Madame, Monsieur, Cher client,<br />
                                <br />
                                Nous vous informons de la mise à disposition  pour vos adhérents concernés de la fiche de sécurité ' ;
				$content .=  $version.' : '. $this->Description;  
				$content .= '<br /><br />Pour plus de détails merci de vous connecter à  votre espace Client.<br /><br />
                               Vous en souhaitant bonne réception, nous vous prions de bien vouloir agréer, Madame, Monsieur, Cher client, l’expression de nos salutations distinguées.<br />
                                <br /> 
                                L’équipe d’UNIFERT France SAS<br />
                                Ce mail est envoyé automatiquement, merci de ne pas y répondre.<br />
                                Pour nous contacter : <a href="mailto:'.$adminMail.'">'.$adminMail.'</a> <br />';
                        
                
                //Prise en compte de Mail.bl
                $bloc = new Bloc();
                $bloc->setFromVar("Mail",$content,array("BEACON"=>"BLOC"));
                $Pr = new Process();
                $bloc->init($Pr);
                $bloc->generate($Pr);
                
                
                //Creation du mail.
                $mailMan = new Mail();
                $mailMan->Subject($sujet);
                $mailMan->From($adminMail);
                //$mailMan->To('gcandella@abtel.fr'); //TEST
                
                $mailMan->To($mailTo); //PROD
                $mailMan->ReplyTo($adminMail);
                //$mail->Body($content);
                $mailMan->Body($bloc->Affich());
                $mailMan->Priority('1');
                $mailMan->BuildMail();
                $mailMan->Send();
                                
                // Ajout création d'un compte pour garder une preuve
                //Creation du mail special
                $mail2 = new Mail();
                $sujet .=" <br /> Mail envoyé à " . $mailTo . " <br />";
                $mail2->Subject($sujet);
                $mail2->From($adminMail);
                $mail2->To('fds@unifert.fr'); 
                $mail2->Body($bloc->Affich());
                $mail2->Priority('1');
                $mail2->BuildMail();
                $mail2->Send();
               
               return $manager;
        }
	
        
        /**
	 * send
	 * Crée l'envoi pour le client passé en argument
	 * @param Object $client Objet Client (Client extends genericClass)
	 * @param String $version Numero de version de le fds
	 * @param Boolean $first Premier envoi ?
	 * @return 
	 */
        public function send($client,$version,$first){
                $env = genericClass::createInstance('FdsUnifert', 'EnvoiFds');
                
                $env->VersionFDS = $version;
                $env->addParent($this);
                $env->addParent($client);
                $env->Save();
                if ($env->sendMail($first)){
                        $env->DateEnvoi = time();
                        $env->Save();
                        return true;
                }
                return false;
        }
	
	/**
	 * getEnvois
	 * retourne la liste des envois liés a cette FDS et au client passé en argument ()
	 * si aucun argument retourne la liste complete des envois de ce tte FDS
	 * @param Object $client Objet Client (Client extends genericClass)
	 * @return Array Tableau d'EnvoiFds
	 */
	public function getEnvois($client = null){
		$env = $this->getChildren('EnvoiFds');
		if($client == null){
			return $env;
		}

		$oEnv = $client->getChildren('EnvoiFds');
		
		$cEnv = array_uintersect($env,$oEnv,function($a,$b){
			
			//if(!is_object($a)||!is_object($b))
			//	return -2;
			//if($a->ObjectType != $b->ObjectType)
			//	return -1;
			if($a->Id > $b->Id)
				return 1;
                        if($a->Id < $b->Id)
				return -1;
			
			return 0;
		});

		return $cEnv;
	}
	
	
	/**
	* Renvoi les clients de la Fds pour un client de type Groupe 
	*
	* @return bool
	*/
	function catalogueClientFds($clientId) {
                //Clients liés à la fds
		$fdsClist = $this->getChildren('Client');
                $mainCli = Sys::getOneData('FdsUnifert','Client/'.$clientId);
                //Clients liés au client passé en argument
                $cliClist = $mainCli->getChildren('Client');
                
                //Clients communs aux 2 listes
                $clis = array_uintersect($fdsClist,$cliClist,function($a,$b){
                        if($a->Id < $b->Id || $a->ObjectType != $b->ObjectType) return -1;
                        if($a->Id > $b->Id) return 1;
                        return 0;
                });
		return $clis;
	}
	
	/**
	 * getInfosCl
	 * retourne les infos (dernier envoi et derniere lecture) liés a cette FDS et au client passé en argument ()
	 * si aucun argument retourne false
	 * @param Object $client Objet Client (Client extends genericClass)
	 * @return Array Tableau d'informations diverses || false
	 */
	public function getInfosCl($client = null){
		if(!$client) return false;
		
		$envs = $this->getEnvois($client);
		
		$dateLecture = 0;
		$dateEnvoi = 0;
		$envId = 0;
		$version = 0;
		$toRead = 0;
		
		foreach($envs as $env){
			if($env->DateEnvoi > $dateEnvoi){
				$dateEnvoi = $env->DateEnvoi;
				$envId = $env->Id;
				$version = $env->VersionFDS;
				$toRead = ($env->DateLecture == 0);
                                $dateLecture = !$env->DateLecture ? 0 : $env->DateLecture;
			}
			
		}
		
		$file = Sys::getData('FdsUnifert','Fds/'.$this->Id.'/FichierFDS',0,1,'DESC','tmsCreate');
		
		return array('dateEnvoi' => $dateEnvoi, 'dateLecture' => $dateLecture, 'envId' => $envId, 'version' => $version, 'url' => ( isset($file[0])? $file[0]->URL : 'Aucun Fichier' ), 'toRead' =>$toRead );
	}
        
        public function creaFds($file){
                $rapport = 'Fichier introuvable';
                if (!is_file($file)) return $rapport;
                $rapport = '';
                $lines = file($file);
                foreach($lines as $line){
                        $deja = Sys::getData('FdsUnifert','Fds/Code='.$line,0,1,'DESC','tmsCreate');
                                
                        if(count($deja)){
                                $rapport .= $deja[0]->Id . ' => ' . $deja[0]->Code . ' Déjà présent !<br>';
                                continue;
                        }
                        $Fds = genericClass::createInstance('FdsUnifert', 'Fds');
                                
                        $Fds->Code = $line;
                        $Fds->Obligatoire = 1;
                        $Fds->Trace = 1;
                        $Fds->Save();
                        $rapport .= $Fds->Id . ' => ' . $Fds->Code . ' Ajouté <br>';
                        unset($Fds,$deja);
                }
                  
                return $rapport;
       }
}
