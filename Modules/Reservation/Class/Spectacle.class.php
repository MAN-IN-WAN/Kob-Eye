<?php
class Spectacle extends genericClass {
	function Save() {
		if ($this->Id!=""){
			//recuperer la liste des evenements du spectacle
			$E = Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/".$this->Id."/Evenement",false,0,1000);
			//date de debut est egale a la date d'aujourdhui
			$dd = time() * 10;
			$df = 0;
			$di = 0;
			$dc = time() * 10;

			$Localisation="";
			$okdd=0;
			$okdf=0;
			$okdc=0;
	
								
			for ($i=0;$i<sizeof($E);$i++){


				if ($dd > $E[$i]["DateDebut"]  ){
					$dd = $E[$i]["DateDebut"];
					$okdd=1;
				}
				if($E[$i]["DateFin"]>$df){
					$df = $E[$i]["DateFin"];
					$okdf=1;
				}
//				if ($E[$i]["DateDebut"]>time()&&$E[$i]["DateFin"]>time()) {
				if ($E[$i]["DateFin"]>time()) {
					$di+=$E[$i]["NbPlace"];
				}
				if($E[$i]["DateCloture"]<$dc&&$E[$i]["DateCloture"]>time()){
					$dc = $E[$i]["DateCloture"];
					$okdc=1;
				}
				//Recherche Ville
				$V = Sys::$Modules["Reservation"]->callData("Reservation/Salle/Evenement/".$E[$i]["Id"]."");
				if (is_array($V[0])&&sizeof(explode(strtolower($V[0]["Ville"]),strtolower($Localisation)))<=1){
					if (strlen($Localisation))$Localisation.=",";
					$Localisation.=$V[0]["Ville"];
				}
				if (is_array($V[0])&&sizeof(explode($V[0]["CodPos"],$Localisation))<=1){
					if (strlen($Localisation))$Localisation.=",";
					$Localisation.=$V[0]["CodPos"];
				}
				
				
			}
			//on definit les valeurs sur l'objet Spectacle
			$this->Localisation = $Localisation;
			if ($okdd==1) $this->DateDebut = $dd;
			if ($okdc==1)$this->ProchaineDateCloture = $dc;
			if ($okdf==1) $this->DateFin = $df;
			$this->Disponibilite = $di;
			//si le champ title est vide alors on definit comme valeur le nom du spectacle ainsi que sa ville
			// modifié car ça ne fonctionnait pas du tout et mettait des infos incorrectes
			
			// 2016 ****************************************************
			// changement Sept 2016 demande Baptiste Lainé : on enlève localisation qui est souvent erronée
			//if ($this->Title==""){$this->Set("Title",$this->Nom .' - '. $this->Localisation);}
			if ($this->Title==""){$this->Set("Title",$this->Nom);}
			
			//si le champ description est vide alors on definit comme valeur le resume du spectacle
			/*	if ($this->Description==""){$this->Set("Description",$this->Resume);}*/
		}
		return genericClass::Save();
	}
	function Update(){
		
		//recuperer la liste des evenements du spectacle
		$E = Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/".$this->Id."/Evenement");
		
		//date de debut est egale a la date d'aujourdhui
		$di = 0;
		$Localisation="";
		$Message=false;
		
		// pour que l'initialisation ne soit jamais supérieure à une date de début probable (dixit Enguer!)
		$DateDebut = time()*10;

		// 201602 : ajout pour comparaison 
		$dc = $DateDebut;
		
		for ($i=0;$i<sizeof($E);$i++){
				
			// !!! 2017-01-04 => à mettre en place voir avec GC : test pour ne traiter que les évenements qui ne sont pas passés .....
		//	if ($E[$i]["DateDebut"]<= mktime() and $DateFin >= mktime() ){
				//Se sont terminés hier
				if ($E[$i]["DateCloture"]>time()){
					$di+=$E[$i]["NbPlace"];
				}
				
				//Se termine aujourd'hui
				if ($E[$i]["DateCloture"]>mktime(0,0,0,date('m',time()),date('d',time()),date('Y',time()))&&$E[$i]["DateCloture"]<mktime(23,59,59,date('m',time()),date('d',time()),date('Y',time()))){
					$Ev = genericClass::createInstance("Reservation",$E[$i]);
					// 201602 : ajouts 
					$messabtel.= "<br />je passe dans envoi du message<br />";
					$Message=$Ev->Envoyer(false);
				}
	
				// on remet la date du jour pour le début du spectacle pour les spectacles en cours
				if ($DateDebut > $E[$i]["DateDebut"] ){
					$DateDebut = $E[$i]["DateDebut"];
					
				}
			//}
				
			
		}

		if ($this->Disponibilite!=$di or $DateDebut!=$dc){
			$this->Disponibilite = $di;
			$this->DateDebut = $DateDebut;
			genericClass::Save();
		}

		
		return $Message;
	}
	
	function getDuree() {
		$a=$this->Duree;
		if ($a<60) {
			$decimale2=sprintf("%02d",$a);
			$val = "0h".$decimale2;
		} else {
			$b=60; 
			$c=$a/$b; 
			$entier=intval(abs($c)); 
			$lesminutes=$a-($entier*60);
			$decimale2=sprintf("%02d",$lesminutes);
			$val= $entier."h";
			if ($decimale2 > 0) {
				$val= $val .$decimale2;
			}
			//$decimale2=sprintf("%.2f",$lesminutes); 
			//$decimale=abs($c)-intval(abs($c)); 
			//$decimale2=sprintf("%.2f",$decimale);  
			//$decimale2=sprintf("%.2f",$lesminutes);  
			//$val= $entier."h".substr($decimale2,2,2);
		}
		return $val;

	}

	/**
	 * Création du tableau de stockage des spectacles pour une semiane
	 * @return	void
	 */
	function initTableSpectacle($datedepart='') {

		$this->tablespectacle=array();
		$today = getdate();
		$DateDepart= $this->GetDatePremierJourSemaine('stamp');
		$DateFin= $this->GetDateDernierJourSemaine('stamp');
		if ($datedepart!='') {
			$today = getdate($DateDepart);
			$DateDepart= $this->GetDatePremierJourSemaine('stamp');
			$DateFin= $this->GetDateDernierJourSemaine('stamp');
		}
		 
		//if ($_SERVER['REMOTE_ADDR']=="185.71.149.9") $DateFin= $this->GetDateDernierJourSemaine('stamp')+604800;
		
		//var_dump("debut",date('Y-m-d',$DateDepart));
		//var_dump("fin",date('Y-m-d',$DateFin));

		//var_dump("fin - st",$DateFin);

		//var_dump("Reservation/Evenement/DateDebut>=".$DateDepart."&DateFin<=".$DateFin);

		// on boucle sur les jours de la semaine
 		$E= Sys::$Modules["Reservation"]->callData("Reservation/Evenement/DateDebut>=".$DateDepart."&DateFin<=".$DateFin);
		if(is_array($E)) {
			foreach($E as $ev) {
				$Jour=date('Y-m-d',$ev['DateDebut']);
				// Modification 2016-05-12 : ajout des horaires
				$horaires=date('H:i',$ev['DateDebut']);
				$Spe= Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/Evenement/".$ev['Id'] );
				if(is_array($Spe)) {
					foreach($Spe as $sp) {
						// recherche du lieu
						$InfoSall ="";					
						$Sal= Sys::$Modules["Reservation"]->callData("Reservation/Salle/Evenement/".$ev['Id']);
						if(is_array($Sal)) {
							foreach($Sal as $sl) {
								$InfoSall = "<br />" .$sl['Nom'] . "<br />" . $sl['Adresse'] . "<br />" . $sl['CodPos'] . " - " .$sl['Ville'];
								if ($sl['TelInfo']!='') $InfoSall .= "<br /> Reservation au : " . $sl['TelInfo'];
								if ($sl['Handi']) $InfoSall .= "<br />Cette salle a un accès handicapés";
							}
						}
						$logo='';
						if ($sp['Logo']!=''&&file_exists($sp['Logo'])) $logo=$sp['Logo'];
//						$this->tablespectacle[$Jour][] = array("Url"=>$sp['Url'],"Img"=>$logo,"Nom"=>$sp['Nom'],"Id"=>$sp['Id'],"Genre"=>$sp['Genre'],"Ville"=>$sp['Ville'],"Duree"=>$sp['Duree'],"Salle"=>$InfoSall);
// Modification 2016-05-12 : ajout des horaires
						$this->tablespectacle[$Jour][] = array("Url"=>$sp['Url'],"Img"=>$logo,"Nom"=>$sp['Nom'],"Id"=>$sp['Id'],"Genre"=>$sp['Genre'],"Ville"=>$sp['Ville'],"Duree"=>$sp['Duree'],"Salle"=>$InfoSall,"Horaire"=>$horaires);
					}
				}
			}
     	} 
	}

	function GetSpectacleJour($LeNumjour) {
	
		$today= $this->GetDatePremierJourSemaine('stamp');
		$interval=86400*$LeNumjour;
		$DateSpectacle= $today+$interval;
		$TabJour=date('Y-m-d',$DateSpectacle);
		return $this->tablespectacle[$TabJour];
	}


	function GetDatePremierJourSemaine($format) {

		$today = getdate();
		$interval=$today['wday']-1;
		$interval=86400*$interval;
		$jour= mktime(0,0,0, $today['mon'],$today['mday'],$today['year'])-$interval;
		if ($format="stamp") return $jour;
		return date('Y-m-d',$jour);

	}

	function GetDateDernierJourSemaine($format){
		$today = getdate();
		$interval=7-$today['wday'];
		$interval=86400*$interval;
		$jour= mktime(23,59,59, $today['mon'],$today['mday'],$today['year'])+$interval;

		if ($format="stamp") return $jour;
		return date('Y-m-d',$jour);
	}

	// jour de la semaine pour rechercher directement les spectacles
	function GetDateJourSemaine ($jour) {
		$today = getdate();
		$jour= mktime(0,0,0, $today['mon'],$today['mday'],$today['year']);
		return date('Y-m-d',$jour);
	}


	// fonction qui permet de renvoyer un tableau 
	// à partir d'une chaine de valeur avec séparateur
	function RenvoiTableau ( $separateur , $infos) {
		$var='';
		if ($separateur==1||$separateur=='1') $var  = explode(' ',$infos);
		if ($separateur==2||$separateur=='2') $var  = explode('&',$infos);
		return $var;
	}


	function GetDateJourSemaineSup ($jour) {
		$today = getdate();
		$interval=$today['wday']-1;
		$interval=86400*$jour;
		$jour= mktime(0,0,0, $today['mon'],$today['mday'],$today['year'])-$interval;
		return $jour;
	}

	function DonneJourSemaine ($Numerojour) {
		$today = getdate();
		$jour= mktime(0,0,0, $today['mon'],$today['mday'],$today['year']);
		$JourdelaSemaine = date('W',$jour);
		if ($JourdelaSemaine ==$Numerojour) {
			// ok on est sur le bon jour demandé et on est pas à une date antérieur
			return $jour;
		}
		// bon on est pas sur le bon jour
		// on recherche le premier
		$Date= $this->GetDateJourSemaineSup($Numerojour);
		
		
		//return date('Y-m-d',$jour);
	
	}
	
 	function RechercheEve ($deb,$fin) {
 	//	return 0;
		// on recherche les spectacles qui ont une date le jour recherché
		$tabspe="";
		$PassT="";
		$Spe= Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/DateDebut<=".$deb."DateFin>=".$deb );
		if(is_array($Spe)) {
			foreach($Spe as $sp) {
				$PasseSPe ='';
				
				// on regarde si il y a un évenement qui correspond
				// Cas d'un évenement sur plusieurs jours qui commence avant la date demandée et qui finit après la date demandée
				$E= Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/".$sp['Id']."/Evenement/DateDebut<=".$deb."&DateFin>=".$deb);
				if(is_array($E)) {
					// on ajoute le spectacle car evenement trouvé
					// on regarde si le spectacle est deja dans la chaine
					if ($tabspe!='') $tabspe .=",";
					$tabspe .= $sp['Id'];
					$PasseSPe ='1';
					
					
				} else {
					if ($sp['Id']=='18121') $PassT="1-NOn";
					
				}
				if ($PasseSPe==''){
					// Cas d'un évenement sur plusieurs jours qui commence à la date demandée et qui finit après la date demandée
					$E= Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/".$sp['Id']."/Evenement/DateDebut>=".$deb."&DateFin>=".$deb);
					if(is_array($E)) {
						if ($tabspe!='') $tabspe .=",";
						$tabspe .= $sp['Id'];
						$PasseSPe ='1';						
					} else {
						if($sp['Id']=='18121') $PassT.=" / 2-NOn";
					}
				}									
				if ($PasseSPe==''){
					// Cas d'un évenement sur un jour qui commence à la date demandée et qui finit à la date demandée
					$E= Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/".$sp['Id']."/Evenement/DateDebut>=".$deb."&DateFin<=".$fin);
					if(is_array($E)) {
						if ($tabspe!='') $tabspe .=",";
						$tabspe .= $sp['Id'];
						$PasseSPe ='1';
					} else {
						if($sp['Id']=='18121') $PassT.=" / 3-NOn";
												
					}
					
				}									
			}
		}
		return $PassT;
		return $tabspe;
 	
 	}
	function RechercheEve2 ($deb,$fin) {
 	//	return 0;
		// on recherche les spectacles qui ont une date le jour recherché
		$tabspe="";
				
		// on regarde si il y a un évenement qui correspond
		// Cas d'un évenement sur plusieurs jours qui commence avant la date demandée et qui finit après la date demandée
		$E= Sys::$Modules["Reservation"]->callData("Reservation/Evenement/DateDebut<=".$deb."&DateFin>=".$deb);
		if(is_array($E)) {
			foreach($E as $ev) {
				// on ajoute le spectacle car evenement trouvé
				$Spe= Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/Evenement/".$ev['Id'] );
				if(is_array($Spe)) {
					foreach($Spe as $sp) {
						// on regarde si le spectacle est deja dans la chaine
						if (strpos($tabspe,$sp['Id'])==false) {
							if ($tabspe!='') $tabspe .=",";
							$tabspe .= $sp['Id'];
						}
					}
				}
			}
		}
		// Cas d'un évenement sur plusieurs jours qui commence à la date demandée et qui finit après la date demandée
		$E= Sys::$Modules["Reservation"]->callData("Reservation/Evenement/DateDebut<=".$fin."&DateFin>=".$deb);
		if(is_array($E)) {
			foreach($E as $ev) {
				$dateEve = $this->ladatecommejeveux($ev['DateDebut']);
				$dateCompar = $this->ladatecommejeveux($deb);
				if ($dateEve>=$dateCompar) {
					// on ajoute le spectacle car evenement trouvé
					$Spe= Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/Evenement/".$ev['Id'] );
					if(is_array($Spe)) {
						foreach($Spe as $sp) {
							// on regarde si le spectacle est deja dans la chaine
							if (strpos($tabspe,$sp['Id'])==false) {
								if ($tabspe!='') $tabspe .=",";
								$tabspe .= $sp['Id'];
							}
						}
					}
				}
			}
		}
		// Cas d'un évenement sur un jour qui commence à la date demandée et qui finit à la date demandée
/*		$E= Sys::$Modules["Reservation"]->callData("Reservation/Evenement/DateDebut<=".$fin."&DateFin>=".$deb);
		if(is_array($E)) {
			foreach($E as $ev) {
				// on ajoute le spectacle car evenement trouvé
				$Spe= Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/Evenement/".$ev['Id'] );
				if(is_array($Spe)) {
					foreach($Spe as $sp) {
						// on regarde si le spectacle est deja dans la chaine
						if (strpos($tabspe,$sp['Id'])==false) {
							if ($tabspe!='') $tabspe .=",";
							$tabspe .= $sp['Id'];
						}
					}
				}
			}
		}*/
		return $tabspe;
 	
 	}

	function ladatecommejeveux ($ladate) {
		preg_match("#^([0-9]+?)\/([0-9]+?)\/([0-9]+?)\ ([0-9]+?)\:([0-9]+?)$#",$ladate,$D);
		if (sizeof($D)<=1)preg_match("#^([0-9]+?)\/([0-9]+?)\/([0-9]+?)$#",$P,$D);
		if (sizeof($D)<=1) return 0;
		$M=(isset($D[2]))?$D[2]:0;
		$J=(isset($D[1]))?$D[1]:0;
		$A=(isset($D[3]))?$D[3]:0;;
		return $A."-".$M."-".$J;
	}


 	function genererLotEvenement($params){
		$step = 0;
		if(!empty($params['step']))
			$step = $params['step'];
		if(!empty($params['type']))
			$step = $params['type'];

		switch($step){
			case 1 : //Evenements sur toute une journée avec  jours d'ouverture

				return array (
					'template'=>"genererLot",
					'step'=>4,
					'callNext'=>array (
						'nom'=>'genererLotEvenement',
						'title'=>'Progression'
					),
					'funcTempVars' => array(
						'step'=> $step
					)
				);
				break;
			case 4 :

				$debut = explode('/',$params['dateDebut']);
				$debut = $debut[1].'/'. $debut[0].'/'. $debut[2];
				$tmsDebut = strtotime($debut);
				$salle= Sys::getOneData('Reservation','Salle/'.$params['salle']);
				$html = '<table border="1">
							<tr>
								<th> </th>
								<th style=\'padding:5px 15px;text-align: center;\'>Date Evenement</th>
								<th style=\'padding:5px 15px;text-align: center;\'>Date Fin Evenement</th>
								<th style=\'padding:5px 15px;text-align: center;\'>Date Fin Réservation</th>
								<th style=\'padding:5px 15px;text-align: center;\'>Salle</th>
								<th style=\'padding:5px 15px;text-align: center;\'>Places à dispo</th>
							</tr>';
				for($n = 0; $n < $params['nbEvt']; $n++){
					for($m = 1; $m <= 7; $m++ ){
						$jourDebSemaine = date('N',$tmsDebut);
						$tmsNext = $tmsDebut+86400 ;
						if($params['joursEvt'][$jourDebSemaine]){
							break;
						} else{
							$tmsDebut = $tmsNext;
						}
					}

					$debut = date('d/m/Y',$tmsDebut);//$tmsDEbut redefini a chaque boucle dont on reprends la date
					$clo = $tmsDebut - (86400*$params['clotureX']);

					$evt = genericClass::createInstance('Reservation','Evenement');
					$evt->DateDebut = $debut.' '.$params['heureDebut'];
					$evt->DateFin = $debut.' '.$params['heureFin'];
					$evt->DateCloture = date('d/m/Y',$clo).' '.$params['clotureHM'];
					$evt->Nom = $this->Nom;
					$evt->NbPlace = $params['nbPlcEvt'];
					$evt->Valide = 1;
					$evt->addParent($this);
					$evt->addParent($salle);
					$evt->Save();

					//Preparation evt suivant:
					$tmsDebut = $tmsNext;

					$html .=  "<tr>
								<td style='padding:5px 15px;'>". ($n + 1) ."</td>
								<td style='padding:5px 15px;'>".date('d/m/Y à H:i:s',$evt->DateDebut)."</td>
								<td style='padding:5px 15px;'>".date('d/m/Y à H:i:s',$evt->DateFin)."</td>
								<td style='padding:5px 15px;'>".date('d/m/Y à H:i:s',$evt->DateCloture)."</td>
								<td style='padding:5px 15px;'>".$salle->Nom."</td>
								<td style='padding:5px 15px;'>".$evt->NbPlace."</td>
							</tr>";
				}
				$html .= '</table>';

				return array ( 'data' => $html );
				break;
			//-------------------------------------------------------------------
			case 2 ://Évènements mensuels (du premier au dernier jours du mois)

				return array (
					'template'=>"genererLot",
					'step'=>5,
					'callNext'=>array (
						'nom'=>'genererLotEvenement',
						'title'=>'Progression'
					),
					'funcTempVars' => array(
						'step'=> $step
					)
				);
				break;
			case 5 :
				$debut = explode('/',$params['dateDebut']);
				$debut = $debut[1].'/'. $debut[0].'/'. $debut[2];
				$tmsDebut = strtotime($debut);
				$salle= Sys::getOneData('Reservation','Salle/'.$params['salle']);
				$html = '<table border="1">
							<tr>
								<th> </th>
								<th style=\'padding:5px 15px;text-align: center;\'>Date Evenement</th>
								<th style=\'padding:5px 15px;text-align: center;\'>Date Fin Evenement</th>
								<th style=\'padding:5px 15px;text-align: center;\'>Date Fin Réservation</th>
								<th style=\'padding:5px 15px;text-align: center;\'>Salle</th>
								<th style=\'padding:5px 15px;text-align: center;\'>Places à dispo</th>
							</tr>';
				for($n = 0; $n < $params['nbMois']; $n++){

					$fin = date('Y/m/t',$tmsDebut);
					$tmsFin = strtotime($fin);

					$fin = date('d/m/Y',$tmsFin);
					$debut = date('d/m/Y',$tmsDebut);//$tmsDEbut redefini a chaque boucle dont on reprends la date
					$clo = $tmsFin - (86400*$params['clotureX']);

					$evt = genericClass::createInstance('Reservation','Evenement');
					$evt->DateDebut = $debut.' '.$params['heureDebut'];
					$evt->DateFin = $fin.' '.$params['heureFin'];
					$evt->DateCloture = date('d/m/Y',$clo).' '.$params['clotureHM'];
					$evt->Nom = $this->Nom;
					$evt->NbPlace = $params['nbPlcEvt'];
					$evt->Valide = 1;
					$evt->addParent($this);
					$evt->addParent($salle);
					$evt->Save();

					//Preparation evt suivant:
					$tmsDebut = $tmsFin+86400;

					$html .=  "<tr>
								<td style='padding:5px 15px;'>". ($n + 1) ."</td>
								<td style='padding:5px 15px;'>".date('d/m/Y à H:i:s',$evt->DateDebut)."</td>
								<td style='padding:5px 15px;'>".date('d/m/Y à H:i:s',$evt->DateFin)."</td>
								<td style='padding:5px 15px;'>".date('d/m/Y à H:i:s',$evt->DateCloture)."</td>
								<td style='padding:5px 15px;'>".$salle->Nom."</td>
								<td style='padding:5px 15px;'>".$evt->NbPlace."</td>
							</tr>";
				}
				$html .= '</table>';

				return array ( 'data' => $html );
				break;
			//-------------------------------------------------------------------
			case 3 ://Évènements sur plusieurs jours récurrents sur la même semaine

				return array (
					'template'=>"genererLot",
					'step'=>6,
					'callNext'=>array (
						'nom'=>'genererLotEvenement',
						'title'=>'Progression'
					),
					'funcTempVars' => array(
						'step'=> $step
					)
				);
				break;
			case 6 :

				$debut = explode('/',$params['dateDebut']);
				$debut = $debut[1].'/'. $debut[0].'/'. $debut[2];
				$fin = explode('/',$params['dateFin']);
				$fin = $fin[1].'/'. $fin[0].'/'. $fin[2];

				$tmsDebut = strtotime($debut);
				$tmsFin = strtotime($fin);

				$salle= Sys::getOneData('Reservation','Salle/'.$params['salle']);
				$html = '<table border="1">
							<tr>
								<th> </th>
								<th style=\'padding:5px 15px;text-align: center;\'>Date Evenement</th>
								<th style=\'padding:5px 15px;text-align: center;\'>Date Fin Evenement</th>
								<th style=\'padding:5px 15px;text-align: center;\'>Date Fin Réservation</th>
								<th style=\'padding:5px 15px;text-align: center;\'>Salle</th>
								<th style=\'padding:5px 15px;text-align: center;\'>Places à dispo</th>
							</tr>';
				for($n = 0; $n < $params['nbEvt']; $n++){

					$fin = date('d/m/Y H:i:s',$tmsFin);//$tmsDEbut redefini a chaque boucle dont on reprends la date
					$debut = date('d/m/Y H:i:s',$tmsDebut);//$tmsDEbut redefini a chaque boucle dont on reprends la date
					$clo = $tmsFin - (86400*$params['clotureX']);

					$evt = genericClass::createInstance('Reservation','Evenement');
					$evt->DateDebut = $debut;
					$evt->DateFin = $fin;
					$evt->DateCloture = date('d/m/Y',$clo).' '.$params['clotureHM'];
					$evt->Nom = $this->Nom;
					$evt->NbPlace = $params['nbPlcEvt'];
					$evt->Valide = 1;
					$evt->addParent($this);
					$evt->addParent($salle);
					$evt->Save();

					//Preparation evt suivant:
					$tmsDebut = $tmsDebut+7*86400;
					$tmsFin = $tmsFin+7*86400;

					$html .=  "<tr>
								<td style='padding:5px 15px;'>". ($n + 1) ."</td>
								<td style='padding:5px 15px;'>".date('d/m/Y à H:i:s',$evt->DateDebut)."</td>
								<td style='padding:5px 15px;'>".date('d/m/Y à H:i:s',$evt->DateFin)."</td>
								<td style='padding:5px 15px;'>".date('d/m/Y à H:i:s',$evt->DateCloture)."</td>
								<td style='padding:5px 15px;'>".$salle->Nom."</td>
								<td style='padding:5px 15px;'>".$evt->NbPlace."</td>
							</tr>";
				}
				$html .= '</table>';

				return array ( 'data' => $html );

				break;
			//-------------------------------------------------------------------
			default: //Initialisation
				return array (
					'template'=>"genererLot",
					'step'=>1,
					'callNext'=>array (
						'nom'=>'genererLotEvenement',
						'title'=>'Réglages'
					),
					'funcTempVars' => array(
						'step'=> $step
					)
				);
		}

	}
}
?>
