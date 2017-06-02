<?PHP
class Entite extends genericClass{
	
        function Save(){
		parent::Save();
		
                $this->mkLinkProjet();
                
	}
        
        function mkLinkProjet(){
                
                //On recup toutes les entités d'abtel
                $projets = Sys::getData('Abtel','Projet');
                $linkedProjects = array();
                //Pour ce projet on recup toutes les liaisons avec les entites puis les entités elles même
                $links = $this->getChildren('PELink');
                foreach($links as $link){
                        $linkedProjects = array_merge($linkedProjects,$link->getParents('Projet'));
                }
                //On regarde si il y une difference
                $missings = array_udiff($projets,$linkedProjects, function($a,$b){
                        if($a->Id > $b->Id) return 1;
                        if($a->Id == $b->Id) return 0;
                        if($a->Id < $b->Id) return -1;
                });
                //Si différence on crée les liens kivonbien
                foreach($missings as $missing){
                        $temp = genericClass::createInstance('Abtel','PELink');
                        $temp->addParent($missing);
                        $temp->addParent($this);
                        $temp->Set('Nom',$this->Nom .' / '. $missing->Nom);
			$temp->Save();
                }
        }
        
        
        /**
	 * fileUpload
	 * Manage the upload of a file when saving an object
	 * @param Array Property
	 * @param String Value of this property
	 * @return Boolean Success or not
	 */
	protected function fileUpload($Prop, $value = "") {
                //debug_print_backtrace();
		if (is_array($value)) {
                        $FileArray = $value;
                } elseif (!empty($_FILES[$value]['name'])) {
			$FileArray = $_FILES[$value];
		} elseif (!empty($_FILES['Form_' . $Prop["Nom"]]['name'])) {
			$FileArray = $_FILES['Form_' . $Prop["Nom"]];
		} elseif (!empty($_FILES['Form_' . $Prop["Nom"]]['name'])) {
			$FileArray = $_FILES['Form_' . $Prop["Nom"]];
		} elseif (!empty($_FILES['Form_' . $Prop["Nom"] . '_Upload']['name'])) {
			$FileArray = $_FILES['Form_' . $Prop["Nom"] . '_Upload'];
		} elseif (!empty($_FILES['Filedata'])) {
			$FileArray = $_FILES["Filedata"];
		} elseif (empty($FileArray)) {
			return false;
                } 
		$fichier = basename($FileArray['name']);
		//On definit l emplacement du fichier upload par defaut
		$Usr = ($this -> Get("Fake_User_Upload")) ? $this -> Get("Fake_User_Upload") : Sys::$User -> Id;
		if ($this -> Module == "Explorateur" && $this -> ObjectType == "Insert") {
			$dossier = "Home/$Usr/" . $this -> get("Destination") . "/";
		} else {
			$dossier = "Home/$Usr/" . $this -> Module . "/" . $this -> ObjectType . "/";
		}
                if($Prop["Nom"]=='PubSignature'){
                                $dossier='Home/Pub_Abtel/';
                }
		if (!file_exists($dossier))
			fileDriver::mk_dir($dossier);
		//On verifie si la taille ne depasse pas le seuil autorise
		$taille_maxi = 1000000000;
		$taille = @filesize($FileArray['tmp_name']);
                if(!$taille) return false;
		$extension = strrchr($fichier, '.');

		if (isset($this->Type)&&$this -> Type == "Media") {
			$extensions = array('.png', '.gif', '.jpg', '.jpeg', '.flv', '.bmp', '.tiff');
			//Ensuite on teste
			if (!in_array(strtolower($extension), $extensions)) {
				return false;
			}
		}
		$fichier = $this -> normalizeFileName($fichier);
		$file_name = basename($fichier, $extension);
		if ($taille > $taille_maxi) {
			$GLOBALS["Systeme"] -> Error -> sendWarningMsg(5);
		}

		$d = 0;
		if (!empty($Vars[$Prop["Nom"]])) {
			//if(!
			move_uploaded_file($FileArray['tmp_name'], $Vars[$Prop["Nom"]]);
			//) $GLOBALS["Systeme"]->Error->sendWarningMsg(4);
		} else {
			if (file_exists($dossier . $file_name . '_' . $d . $extension)) {
				while (file_exists($dossier . $file_name . '_' . $d . $extension))
					$d++;
				$file_name = $file_name . '_' . $d;
			}
                        if($Prop["Nom"]=='PubSignature'){
                                $file_name='Pub_Abtel';
                                $d = '';
                                $extension = strtolower($extension);
                                if (file_exists($dossier . $file_name . $d . $extension)){
                                        chmod($dossier . $file_name . $d . $extension, 0755); 
                                        unlink($dossier . $file_name . $d . $extension);
                                }
                        }
                        
			if (!move_uploaded_file($FileArray['tmp_name'], $dossier . $file_name . $d . $extension)) {
				//$GLOBALS["Systeme"]->Error->sendWarningMsg(41);
			}
			$N = $Prop["Nom"];
			$this -> $N = $dossier . $file_name . $d . $extension;
			return true;
		}
		unset($_FILES['Form_' . $Prop["Nom"] . '_Upload']);
		unset($_FILES['Form_' . $Prop["Nom"]]);
		return false;
	}
        
        
		
        /******************************************************
        * 					STATIC
        * ****************************************************/
        /**
        * recherche de lentité en cours en fonction du domaine actuel
        * @return Magasin
        */
        static function getCurrentEntite() {
                        //récupération du domaine en cours
                        $dom = Sys::$domain;
                        //recherche du site correspondant
                        $doms = Sys::getData('Systeme','Site/Domaine='.$dom);
                        //renvoi l'entité correpondante
                        if (isset($doms[0])){
                                $enti = $doms[0]->getChildren('Entite');
                                if (isset($enti[0]))return $enti[0]; 
                        }
                        //renvoi l'entité par défaut
                        $enti = Sys::getData('Abtel','Entite/Default=1');
                        if (isset($enti[0]))return $enti[0];
                        
                        return false;
        }
        
        /**
        * Remplace la pub de la signature par une
        * @return Magasin
        */
        static function resetPubSign(){
                $url = 'Home/Pub_Abtel/Pub_Abtel.jpg';
                if (file_exists($url)){
                        chmod($url, 0755);
                        unlink($url);     
                }
                
                return copy('Tools/Images/Blank.jpg',$url);
        }
}
?>