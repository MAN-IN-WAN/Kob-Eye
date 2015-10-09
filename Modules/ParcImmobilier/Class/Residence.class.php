<?php

class Residence extends genericClass{
	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}

 	function Save() {
		$lieprescripteur=0;
		if ($this->Id!=""){
			if ($this->Title==""){$this->Set("Title",'PragmA immobilier, r&eacute;sidence '.$this->Titre.' - '.$this->NbApparts);}
			//si le champ description est vide alors on definit comme valeur le resume du spectacle
			if ($this->Description==""){$this->Set("Description",$this->Texte);}
		}
		genericClass::Save();
			
		if ($this->LiePrescripteur) $lieprescripteur=1;

		if( $lieprescripteur==1) {
			// Demande de Xavier Delcher au moment de la mise en place prescripteur
			// lien des prescripteur
			// lire les prescripteurs actif
			// faire addParent à chaque prescripteur
			$D = Sys::$Modules['Systeme']->callData('Systeme/Group/7/Group/*/User/Actif=1');
			if (is_array($D))foreach ($D as $De){
				$this->AddParent( "Systeme/User/".$De["Id"] );
				//var_dump("Systeme/User/".$De["Id"]);
			}
			genericClass::Save();
		
		}
		
	}


	/********** LISTE DE RESIDENCES **********/

	/**
	 * Retourne le nom de ma ville par IP
	 * @return	string
	 */
	function getVille() {
		$myIp = explode('.', $_SERVER['REMOTE_ADDR']);
		if($_GET['ville']=='toulouse') $myIp = explode('.','90.60.94.9');
		$ip = 16777216 * $myIp[0] + 65536 * $myIp[1] + 256 * $myIp[2] + $myIp[3];
		$Bloc = Sys::$Modules["ParcImmobilier"]->callData("ParcImmobilier/IPBloc/StartIpNum<=".$ip,null,0,1,"DESC","StartIpNum");
		if(!empty($Bloc)) {
			$Ville = Sys::$Modules["ParcImmobilier"]->callData("ParcImmobilier/IPVille/IPBloc/".$Bloc[0]['Id']);
			if(!empty($Ville)) {
				$v = genericClass::createInstance('ParcImmobilier', $Ville[0]);
				return $v;
			}
		}
		return null;
	}

	/**
	 * Retourne la liste des résidences qui sont attachées à mon IP
	 * @return	Tableau d'objets
	 */
	function getMyResidences( $order = 'tmsCreate') {
		$return = array();
		$ville = $this->getVille();
		if($ville != null) {
			$Residences = Sys::$Modules["ParcImmobilier"]->callData("ParcImmobilier/Residence/IPVille/".$ville->Id,null,0,100,"DESC",$order);
			if(!empty($Residences)) {
				foreach($Residences as $k => $Residence) $return[] = genericClass::createInstance('ParcImmobilier', $Residence);
			}
		}
		return $return;
	}

	/**
	 * Récupère la bonne résidence une (soit par IP soit par AmbianceReferente)
	 * @return	Objet Residence
	 */
	function getResidenceUne() {
		$myResidences = $this->getMyResidences('tmsEdit');
		if(!empty($myResidences)) return $myResidences[0];
		$Residence = Sys::$Modules["ParcImmobilier"]->callData("ParcImmobilier/Residence/AmbianceReferente=1",null,0,1,"DESC","tmsEdit");
		$myRes = genericClass::createInstance('ParcImmobilier', $Residence[0]);

		return $myRes;
	}

	/**
	 * Retourne toutes les résidences en commençant par celles de ma ville
	 * @return	array
	 */
	function getAllResidences() {
		$myResidences = $this->getMyResidences();
		$Residences = array();
		$refs = Sys::$Modules["ParcImmobilier"]->callData("ParcImmobilier/Residence/Logement=1&Reference=0",null,0,100,"DESC","ALaUne");
		// On transforme en objClass
		foreach($refs as $k => $Residence) $Residences[] = genericClass::createInstance('ParcImmobilier', $Residence);
		// On retire les résidences qui sont déjà dans "MyResidences" pour ne pas avoir de doublons
		foreach($Residences as $k => $Residence) {
			foreach($myResidences as $j => $myRes) {
				if($Residence->Id == $myRes->Id) {
					unset($Residences[$k]);
					break;
				}
			}
		}
		return array_merge($myResidences, $Residences);
	}



	/*---------------------------------------------- Accès prescripteur 2012 ------------------------------------------------------------*/
   
    
    
    /**
     * Retourne toutes les résidences du Prescripteur connecté
     * @param   int     ID d'un département
     * @param   int     ID d'une ville
     * @param   int     Tranche Budget
     * @param   string  Type Fiscalite
     * @param   array   Types cochés
     * @return  array   Liste des résidences correspondantes
     */
    function getMesResidences( $Departement = false, $Ville = false, $Budget = false, $Fiscalite = false, $Type = array(), $Residence = false, $Gestion = false, $LimitStart = 0, $NbParPage = 100 ) {
        // Filtres
        $db = $GLOBALS["Systeme"]->Db[0];
        $filtres = array(1);
        if($Departement) $filtres[] = "d.Id=".$db->Quote($Departement);
        if($Residence) $filtres[] = "r.Id=".$db->Quote($Residence);
        if($Ville) $filtres[] = "v.Id=".$db->Quote($Ville);
        if($Fiscalite) $filtres[] = "r.LoiResidence=".$db->Quote($Fiscalite);
     	if (is_array($Type)) {
		$lefiltre="";
 		foreach ($Type as $i) {
			switch($i) {
				case '1' :	
					$lefiltre.="tl.Type ='T1'"; break;
				case '2' :	
					if ($lefiltre!='') {
						$lefiltre.=' OR '; 
					}
					$lefiltre.=" tl.Type ='T2'"; 
					break;
				case '3' :	
					if ($lefiltre!='') {
						$lefiltre.=' OR '; 
					}
					$lefiltre.=" tl.Type ='T3'"; 
					break;
				case '4' :	
					if ($lefiltre!='') {
						$lefiltre.=' OR '; 
					}
					$lefiltre.=" tl.Type ='T4'"; 
					break;
				case '5' :	
					if ($lefiltre!='') {
						$lefiltre.=' OR '; 
					}
					$lefiltre.=" tl.Type ='T5' OR tl.Type ='Maison'  OR tl.Type ='Villa'"; 
					break;
				case 'Ccial' :	
					if ($lefiltre!='') {
						$lefiltre.=' OR '; 
					}
					$lefiltre.=" tl.Type ='Ccial'"; 
					break;
			}
 			
		}
		if ($lefiltre!='') $filtres[] = "(" . $lefiltre . ")";
	}

 	if($Budget) {
		switch($Budget) {
			case '1' :	$filtres[] = "gp.Tarif<=120000"; break;
			case '2' :	$filtres[] = "gp.Tarif>=121000 and gp.Tarif<=160000"; break;
			case '3' :	$filtres[] = "gp.Tarif>161000 and gp.Tarif<=190000"; break;
			case '4' :	$filtres[] = "gp.Tarif>=191000 and gp.Tarif<=260000"; break;
			case '5' :	$filtres[] = "gp.Tarif>=261000 and gp.Tarif<=350000"; break;
			case '6' :	$filtres[] = "gp.Tarif>350000"; break;
		}
	}

        return $this->getMyxxx('r', $filtres,"", $Gestion , "LIMIT $LimitStart, $NbParPage");
    }
    
	/**
	 * Permet de faire une requete globale pour des résidences ou des lots
	 * @param	string	'r' ou 'l'
	 * @param	array	Les filtres
	 */
	function getMyTypeLot( $Residence, $Statut=0) {
		$UserId = $GLOBALS["Systeme"]->User->Id;
		$db = $GLOBALS["Systeme"]->Db[0];
		if ($Statut!=0) $Filtre=" and ( l.Statut='" . $Statut . "' OR l.Statut='') ";
// modification juin 2013 pour n'afficher que les lots réellement disponible
$Filtre=" and ( l.Statut='1' OR l.Statut='') ";
		$sql = "SELECT tl.Type as TypeLogement,tl.Titre as NomLogement,
	          COUNT(distinct l.Id) as NbLots, MAX(gp.Tarif) as MaxTarif, MIN(gp.Tarif) as MiniTarif , tl.PrixMin as TLMinTarif, tl.PrixMax as TLMaxTarif
				FROM `".MAIN_DB_PREFIX."ParcImmobilier-Residence` r
                LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-ResidencePrescripteur` rp ON rp.Residence=r.Id
				LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-TypeLogement` as `tl` ON tl.ResidenceId=r.Id
				LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-Lot` l ON tl.Id=l.TypeLogementId
				LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-GrillePrix` gp ON gp.LotId=l.Id 
                LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-LotPrescripteur` lp ON lp.Lot=l.Id
				WHERE r.Id ='" . $Residence ."' AND (lp.UserId=".$UserId." OR rp.UserId=".$UserId.") " . $Filtre . "
				GROUP BY tl.Type 
				ORDER BY TypeLogement ASC 
				$pagination";
//var_dump($Statu); die;
		$Result = $db->query( $sql );
		if($Result) return $Result->fetchALL( PDO::FETCH_ASSOC );
		else return array();
	}




    /**
     * Retourne tous les lots du Prescripteur connecté
     * @param   int     ID d'un département
     * @param   int     ID d'une ville
     * @param   int     Tranche Budget
     * @param   string  Type Fiscalite
     * @param   array   Types cochés
     * @param   int     ID d'une résidence particulière
     * @return  array   Liste des résidences correspondantes
     */
    function getMesLots( $Departement = false, $Ville = false, $Budget = false, $Fiscalite = false, $Type = array(), $Residence = false,  $FiltreActions = false, $Gestion = false, $LimitStart = 0, $NbParPage = 100 ) {
        // Filtres
        // TODO Gérér filtre Fiscalité
        // TODO Gérer fitre types
        $db = $GLOBALS["Systeme"]->Db[0];
        $filtres = array(1);
        if($Departement) $filtres[] = "d.Id=".$db->Quote($Departement);
        if($Ville) $filtres[] = "v.Id=".$db->Quote($Ville);
        if($Residence) $filtres[] = "r.Id=".$db->Quote($Residence);
        if($Fiscalite) $filtres[] = "r.LoiResidence=".$db->Quote($Fiscalite);
		if($FiltreActions) {
			if($Gestion) {
				switch($FiltreActions) {
					case 'Optionnés' :	$filtres[] = " l.Statut='2'"; break;
					case 'Reservés' :	$filtres[] = " l.Statut='3'"; break;
					case 'Optionnes' :	$filtres[] = " l.Statut='2'"; break;
					case 'Reserves' :	$filtres[] = " l.Statut='3'"; break;
					case 'Vendus' :	$filtres[] = " l.Statut='4'"; break;
				}
			} else {
				switch($FiltreActions) {
					case 'Optionnés' :	$filtres[] = " a.Type='Optionner'"; break;
					case 'Reservés' :	$filtres[] = " a.Type='Reserver'"; break;
					case 'Optionnes' :	$filtres[] = " a.Type='Optionner'"; break;
					case 'Reserves' :	$filtres[] = " a.Type='Reserver'"; break;
					case 'Vendus' :	$filtres[] = " a.Type='Vendu'"; break;
				}
			}
			
		}

        if (is_array($Type)) {
			$lefiltre="";
	 		foreach ($Type as $i) {
				switch($i) {
					case '1' :	
						$lefiltre.="tl.Type ='T1'"; break;
					case '2' :	
						if ($lefiltre!='') {
							$lefiltre.=' OR '; 
						}
						$lefiltre.=" tl.Type ='T2'"; 
						break;
					case '3' :	
						if ($lefiltre!='') {
							$lefiltre.=' OR '; 
						}
						$lefiltre.=" tl.Type ='T3'"; 
						break;
					case '4' :	
						if ($lefiltre!='') {
							$lefiltre.=' OR '; 
						}
						$lefiltre.=" tl.Type ='T4'"; 
						break;
					case '5' :	
						if ($lefiltre!='') {
							$lefiltre.=' OR '; 
						}
						$lefiltre.=" tl.Type ='T5' OR tl.Type ='Maison'  OR tl.Type ='Villa'"; 
						break;
					case 'Ccial' :	
						if ($lefiltre!='') {
							$lefiltre.=' OR '; 
						}
						$lefiltre.=" tl.Type ='Ccial'"; 
						break;
				}
	 			
			}
			if ($lefiltre!='') $filtres[] = "(" . $lefiltre . ")";
		}

 		if($Budget) {
			switch($Budget) {
				case '1' :	$filtres[] = "gp.Tarif<=120000"; break;
				case '2' :	$filtres[] = "gp.Tarif>=121000 and gp.Tarif<=160000"; break;
				case '3' :	$filtres[] = "gp.Tarif>161000 and gp.Tarif<=190000"; break;
				case '4' :	$filtres[] = "gp.Tarif>=191000 and gp.Tarif<=260000"; break;
				case '5' :	$filtres[] = "gp.Tarif>=261000 and gp.Tarif<=350000"; break;
				case '6' :	$filtres[] = "gp.Tarif>350000"; break;
			}
		}

        return $this->getMyxxx('l', $filtres, $FiltreActions, $Gestion, "LIMIT $LimitStart, $NbParPage");
    }


 function RecuperePrix( $Budget = false,  $Type = false) {
        // Filtres
        // TODO Gérér filtre Fiscalité
        // TODO Gérer fitre types
        $db = $GLOBALS["Systeme"]->Db[0];
  	if($Type) $filtres .= " and tl.Type =".$db->Quote($Type);
	if($Budget) {
		switch($Budget) {
			case 'T1' :	$filtres .= " and gp.Tarif<120000"; break;
			case 'T2' :	$filtres .= " and gp.Tarif>120000 and gp.Tarif<160000"; break;
			case 'T3' :	$filtres .= " and gp.Tarif>160000 and gp.Tarif<190000"; break;
			case 'T4' :	$filtres .= " and gp.Tarif>190000 and gp.Tarif<260000"; break;
			case 'T5' :	$filtres .= " and gp.Tarif>260000 and gp.Tarif<350000"; break;
			case 'T6' :	$filtres .= " and gp.Tarif>350000"; break;
		}
	}
	$sql = "SELECT gp.TarifLogement, tl.Type FROM `".MAIN_DB_PREFIX."ParcImmobilier-Residence` r
		LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-TypeLogement` as `tl` ON tl.ResidenceId=r.Id
		LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-Lot` l ON tl.Id=l.TypeLogementId
		LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-GrillePrix` gp ON gp.LotId=l.Id 
			WHERE r.Id =" . $this->Id  . $filtres;
		//echo $sql . '<br/>';
		$Result = $db->query( $sql );
		if($Result) return $Result->fetchALL( PDO::FETCH_ASSOC );
		else return array();

    }

	/**
	 * Permet de faire une requete globale pour des résidences ou des lots
	 * @param	string	'r' ou 'l'
	 * @param	array	Les filtres
	 */
	 private function getMyxxx( $alias, $filtres, $FiltreActions, $Gestion, $pagination ) {
		$UserId = $GLOBALS["Systeme"]->User->Id;
		$db = $GLOBALS["Systeme"]->Db[0];
		$sql = "SELECT ".$alias.".*, r.Titre as Residence, r.Id as ResidenceId, r.Actabilite as Actabilite,  r.LoiResidence as LoiResidence,  r.IconeLoiResidence as IconeLoiResidence, r.Gestionnaire as GestionResidence , l.Statut as StatutLot,  r.DateLivraison as DateLivraison, tl.Titre as TypeLogement,
		          v.Nom as Ville, v.CodePostal as CodePostal,d.Nom as Departement, l.Id as IdLot , 
		          COUNT(l.Id) as NbLots, gp.Tarif as Tarif, 
		          a.Type as TypeAction, a.tmsCreate as TmsAction
				FROM `".MAIN_DB_PREFIX."ParcImmobilier-Residence` r
                LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-ResidencePrescripteur` rp ON rp.Residence=r.Id
				LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-Ville` as `v` ON v.Id=r.VilleId
				LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-Departement` as `d` ON d.Id=v.DepartementId
				LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-TypeLogement` as `tl` ON tl.ResidenceId=r.Id
				LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-Lot` l ON tl.Id=l.TypeLogementId
				LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-GrillePrix` gp ON gp.LotId=l.Id 
                LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-LotPrescripteur` lp ON lp.Lot=l.Id
                LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-Action` a ON a.LotId=l.Id
                LEFT JOIN `".MAIN_DB_PREFIX."ParcImmobilier-ActionPrescripteur` ap ON ap.Action=a.Id
				WHERE r.Prescripteur='1' and ";
				// ici si on est connecté en tant que gestionnaire commercial on ne fait pas de filtre sur les statuts
				if ($Gestion==FALSE) {
				 	$sql .= " (lp.UserId=".$UserId." OR rp.UserId=".$UserId. ") " ;
                	if ($FiltreActions!='') {
                			 $sql .= " AND (ap.UserId=".$UserId.") ";
                	} else {
				if ($Gestion==FALSE) {
					// modification du 17/10/2014 demande Grégory Meunier : les prescripteurs ne voit pas les lots actés ou vendu
	                		$sql .= " AND (l.Statut<'3')  AND (l.Publier='1') ";
				} else {
	                		$sql .= "  AND (l.Publier='1') ";
				}	
    	            }
    	            $sql .=" AND ";
    	        }
 				$sql .= " (". $alias.".Id > 0) AND ". implode(' AND ', $filtres) ."
				GROUP BY ".$alias.".Id
				ORDER BY ".$alias.".Id DESC " . $pagination;
//var_dump($sql); 
//die;
		$Result = $db->query( $sql );
		if($Result) return $Result->fetchALL( PDO::FETCH_ASSOC );
		else return array();
	}

	/**
	* getClone
	*/
	function getClone() {
		//recupération des données
		$dons = Sys::getData('ParcImmobilier','Residence/'.$this->Id.'/Donnee');
		$pars = Sys::getData('ParcImmobilier','Ville/Residence/'.$this->Id);
		$pars = array_merge($pars,Sys::getData('ParcImmobilier','PictoResidence/Residence/'.$this->Id));
		$pars = array_merge($pars,Sys::getData('ParcImmobilier','Prescripteur/Residence/'.$this->Id));
		$cl = parent::getClone();
		$cl->Titre.=' (Copie)';
		$cl->Logement=0;
		//ajout des liens parents
		foreach ($pars as $p)
			$cl->AddPArent($p);
		$cl->Save();
		//Cloner les données
		foreach ($dons as $d) {
			$dc = $d->getClone();
			$dc->addParent($cl);
			$dc->Save();
		}
		return $cl;
	}

	/**
	* Delete
	*/
	function Delete () {
		$dons = Sys::getData('ParcImmobilier','Residence/'.$this->Id.'/Donnee');
		foreach ($dons as $d) {
			$d->Delete();
		}
		$tl = Sys::getData('ParcImmobilier','Residence/'.$this->Id.'/TypeLogement');
		foreach ($tl as $t) {
			$t->Delete();
		}
		parent::Delete();
	}

	/**
	* fonction renvoie true si la date est un jour ouvré entre 9 et 17h
	*/
	function RenvoiDateCron () {

		$ladate=time();
		if (date('w',$ladate)>0&&date('w',$ladate)<6) {
			if (date('H',$ladate)>9&&date('',$ladate)<18) {
				//return(date('H',$ladate));
				return true;
			} else {
				return false;
				//return(date('w',$ladate)." false");
			}
		} else { 
			return false;
			//return(date('w',$ladate));
			
		}
		return false;

	}

}