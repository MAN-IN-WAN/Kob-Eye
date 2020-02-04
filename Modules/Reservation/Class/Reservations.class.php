<?php
class Reservations extends genericClass {
    public static $TabParVille = array();
    function Save() {
        if ($this->Id==""){
            //Definition reference
            if (time()>mktime(0,0,0,6,30,date('y')))$an = date('y');else $an = date('y')-1;
            $debutmois = mktime(0,0,0,6,30,$an);
            $Tab=Sys::$Modules["Reservation"]->callData("Reservation/Reservations/tmsCreate>".$debutmois,"",0,1,"","","COUNT(DISTINCT(m.Id))");
            $Nb=$Tab[0]["COUNT(DISTINCT(m.Id))"];
            $this->Data = date('ymd').sprintf('%05s',$Nb);
            $this->Set("Reference",$this->Data);
            genericClass::Save();
            //Saisie des informations
            $E=Sys::$Modules["Reservation"]->callData("Reservation/Evenement/Reservations/".$this->Id,"",0,1);
            $E = $E[0]["Id"];
            if ($E=="")return genericClass::Delete();
            $S=Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/Evenement/".$E,"",0,1);
            $this->TitreSpectacle = $S[0]["Nom"];
            $this->InformationReservation = $S[0]["InformationReservation"];
            $this->TypeContreMarque = $S[0]["TypeContreMarque"];
            genericClass::Save();
        }else{
            //Verification du nombre place disponible
            if ($this->Verify()){
                $Delta = $this->NbPlace;
                $Tab=Sys::$Modules["Reservation"]->callData("Reservation/Reservations/".$this->Id."/Personne","",0,1,"","","COUNT(DISTINCT(m.Id))");
                $this->NbPlace=$Tab[0]["COUNT(DISTINCT(m.Id))"];
                genericClass::Save();
                //Decompte des places sur l'evenement associé
                $Delta = $this->NbPlace-$Delta;
                if ($Delta>0){
                    $E=Sys::$Modules["Reservation"]->callData("Reservation/Evenement/Reservations/".$this->Id,"",0,1);
                    $E = genericClass::createInstance("Reservation",$E[0]);
                    $E->NbPlace -=$Delta;
                    $E->Save();
                }
            }
        }
    }
    function Verify() {
        if (genericClass::Verify()){
            if ($this->Id!=""){
                //On compte le nombre de place
                $Tab=Sys::$Modules["Reservation"]->callData("Reservation/Reservations/".$this->Id."/Personne","",0,1,"","","COUNT(DISTINCT(m.Id))");
                $NbPlace=$Tab[0]["COUNT(DISTINCT(m.Id))"];
                $NbPlace = $NbPlace-$this->NbPlace;
                //Vérification du nombre de place disponible
                $E=Sys::$Modules["Reservation"]->callData("Reservation/Evenement/Reservations/".$this->Id,"",0,1);
                if ($E[0]["Id"]!=""){
                    if (intval($E[0]["NbPlace"])<intval($NbPlace)){
                        //Generation des erreurs
                        $e["Message"] = "Il n'y a plus assez de place disponible (".$E[0]["NbPlace"]." / ".$NbPlace.") pour cette date (veuillez rafraîchir votre fiche spectacle.)";
                        $e["Prop"] = "NbPlace";
                        $this->AddError($e);
                        return false;
                    }
                }
            }
            return true;
        }else return false;
    }   
    function Delete() {
        //On rajoute les places à l'evenement
        $Delta = $this->NbPlace;
        if ($Delta>0){
            $E=Sys::$Modules["Reservation"]->callData("Reservation/Evenement/Reservations/".$this->Id,"",0,1);
            if (is_array($E[0])){
                $E = genericClass::createInstance("Reservation",$E[0]);
                $E->NbPlace +=$Delta;
                $E->Save();
            }
        }
        genericClass::Delete();
    }






    //////////////////////////////////// Stats
    
    /**
     * Récupère les données pour un mois
     * @param   int     Timestamp compris dans le mois que l'on veut (si non défini - mois courant)
     * @return  Tableau de données
     */
    public function getStatsMensuelles( $date = 0, $sexe = '', $age = '' ) {

        $GLOBALS["Systeme"]->connectSQL();

        if(!$date) $date = time();

        $res = $this->getBounds($date);
        $data = array();
        for($i=1; $i<=$res['nbJours']; $i++) $data[$i] = array('NbResa' => 0, 'NbPlaces' => 0, 'Legende' => sprintf('%02d', $i).'/'. sprintf('%02d', $res['mois']));
        $sql = "SELECT Id, tmsCreate, NbPlace FROM `".MAIN_DB_PREFIX."Reservation-Reservations` WHERE tmsCreate >=".$res['start']." AND tmsCreate <= ".$res['end'];
        $req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
        $res = $req->fetchALL ( PDO::FETCH_ASSOC );
        foreach($res as $resa) :
            $idx = (int)(date('d', $resa['tmsCreate']));
            if(!empty($sexe) or !empty($age)) :
                $sql2 = "SELECT COUNT(*) FROM `".MAIN_DB_PREFIX."Reservation-PersonneReservationId` r LEFT JOIN `".MAIN_DB_PREFIX."Reservation-Personne` p ON p.Id = r.Personne WHERE r.ReservationsId = " . $resa['Id'];
                if(!empty($sexe)) $sql2 .= " AND p.Sexe = '$sexe'";
                if(!empty($age)) $sql2 .= " AND p.Age = '$age'";
                $req2 = $GLOBALS["Systeme"]->Db[0]->query( $sql2 );
                $count = $req2->fetchColumn ( 0 );
                if($count) :
                    $data[$idx]['NbResa']++;
                    $data[$idx]['NbPlaces'] += $count;
                endif;
            else :
                $data[$idx]['NbResa']++;
                $data[$idx]['NbPlaces'] += $resa['NbPlace'];
            endif;
        endforeach;
        return $data;
    }

    /**
     * Récupère les données pour une année
     * @param   int     Timestamp compris dans l'année que l'on veut (si non défini - année courante)
     * @return  Tableau de données
     */
    public function getStatsAnnuelles( $date = 0, $sexe = '', $age = '' ) {
        $GLOBALS["Systeme"]->connectSQL();
        $data = array();
        if(!$date) $date = time();
        $annee = date('Y', $date);
        for($i=1; $i<=12; $i++) {
            $start = mktime(0,0,0,$i,1, $annee);
            $end = mktime(0,0,0,$i+1,1, $annee);


            $sql = "SELECT Id, tmsCreate, NbPlace FROM `".MAIN_DB_PREFIX."Reservation-Reservations` WHERE tmsCreate >=".$start." AND tmsCreate <= ".$end;
            $req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
            $res = $req->fetchALL ( PDO::FETCH_ASSOC );


            foreach($res as $resa) :
                $idx = $i-1;
                if(!empty($sexe) or !empty($age)) :
                    $sql2 = "SELECT COUNT(*) FROM `".MAIN_DB_PREFIX."Reservation-PersonneReservationId` r LEFT JOIN `".MAIN_DB_PREFIX."Reservation-Personne` p ON p.Id = r.Personne WHERE r.ReservationsId = " . $resa['Id'];
                    if(!empty($sexe)) $sql2 .= " AND p.Sexe = '$sexe'";
                    if(!empty($age)) $sql2 .= " AND p.Age = '$age'";
                    $req2 = $GLOBALS["Systeme"]->Db[0]->query( $sql2 );
                    $count = $req2->fetchColumn ( 0 );
                    if($count) :
                        $data[$idx]['NbResa']++;
                        $data[$idx]['NbPlaces'] += $count;
                    endif;
                else :
                    $data[$idx]['NbResa']++;
                    $data[$idx]['NbPlaces'] += $resa['NbPlace'];
                endif;
            endforeach;

            //$sql .= "SELECT SUM( nbPlace ) AS NbPlaces, COUNT( * ) AS NbResa FROM `".MAIN_DB_PREFIX."Reservation-Reservations` WHERE `tmsCreate` >= " . $start . " AND `tmsCreate` < ".$end;
            //if($i < 12) $sql .= " UNION ";
        }
        //$req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
        //$res = $req->fetchALL ( PDO::FETCH_ASSOC );
        $mois = array('Jan','Fev','Mar','Avr','Mai','Jun','Jui','Aou','Sep','Oct','Nov','Dec');
        foreach($mois as $k => $m) {
            if(!isset($data[$k])) $data[$k] = array('NbResa' => 0, 'NbPlaces' => 0);
            $data[$k]['Legende'] = $m;
            if(!$data[$k]['NbPlaces']) $data[$k]['NbPlaces'] = 0;
            if(!$data[$k]['NbResa']) $data[$k]['NbResa'] = 0;
        }
        return $data;
    }

    /**
     * Retourne les bornes du mois en cours (ou précédent) en timestamp
     * @param   int     Timestamp compris dans le mois que l'on veut (si non défini - mois courant)
     * @return  Tableau avec start et end
     */
    private function getBounds( $date = 0 ) {
        // Mois et année ciblés
        if(!$date) $date = time();
        $annee = date('Y',$date);
        $mois = date('m',$date);
        // Bornes
        $res = array();
        $res['start'] = mktime(0,0,0,$mois,1,$annee);
        $res['mois'] = $mois;
        $res['annee'] = $annee;
        $res['nbJours'] = date('t',$res['start']);
        $res['end'] = mktime(23,59,59,$mois,$res['nbJours'],$annee);
        return ($res);
    }

    public function getStatsNbSpectacles() {
        $GLOBALS["Systeme"]->connectSQL();
        $result = array();
        $sql = "SELECT COUNT(*) as total FROM ( SELECT COUNT(*) as total FROM `cult-Reservation-PersonneReservationId` GROUP BY Personne HAVING COUNT(total) <= 2 ) as a";
        $req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
        $res = $req->fetchALL ( PDO::FETCH_ASSOC );
        $result[] = array('Libelle' => '1-2', 'Val' => $res[0]['total']);
        $sql = "SELECT COUNT(*) as total FROM ( SELECT COUNT(*) as total FROM `cult-Reservation-PersonneReservationId` GROUP BY Personne HAVING COUNT(total) >=3 AND COUNT(total) <= 5 ) as a";
        $req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
        $res = $req->fetchALL ( PDO::FETCH_ASSOC );
        $result[] = array('Libelle' => '3-5', 'Val' => $res[0]['total']);
        $sql = "SELECT COUNT(*) as total FROM ( SELECT COUNT(*) as total FROM `cult-Reservation-PersonneReservationId` GROUP BY Personne HAVING COUNT(total) >= 6 AND COUNT(total) <= 10 ) as a";
        $req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
        $res = $req->fetchALL ( PDO::FETCH_ASSOC );
        $result[] = array('Libelle' => '6-10', 'Val' => $res[0]['total']);
        $sql = "SELECT COUNT(*) as total FROM ( SELECT COUNT(*) as total FROM `cult-Reservation-PersonneReservationId` GROUP BY Personne HAVING COUNT(total) > 10 ) as a";
        $req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
        $res = $req->fetchALL ( PDO::FETCH_ASSOC );
        $result[] = array('Libelle' => '+ de 10', 'Val' => $res[0]['total']);
        return $result;
    }

    /**
     * Retourne un tableau associatif genre => nombre de places réservées
     * @param   int     borne min
     * @param   int     borne max
     * @return  Tableau associatif
     */
    public function getStatsGenre( $min, $max, $sexe = '', $age = '' ) {
        $GLOBALS["Systeme"]->connectSQL();
        $sql = "SELECT s.Genre as Nom, COUNT(p.Id) as NbPlaces
                FROM `".MAIN_DB_PREFIX."Reservation-Spectacle` s
                INNER JOIN `".MAIN_DB_PREFIX."Reservation-Evenement` e ON e.SpectacleId = s.Id
                INNER JOIN `".MAIN_DB_PREFIX."Reservation-ReservationsEvenementId` rs ON rs.EvenementId = e.Id
                INNER JOIN `".MAIN_DB_PREFIX."Reservation-Reservations` r ON r.Id = rs.Reservations
                INNER JOIN `".MAIN_DB_PREFIX."Reservation-PersonneReservationId` pr ON pr.ReservationsId = r.Id 
                INNER JOIN `".MAIN_DB_PREFIX."Reservation-Personne` p ON p.Id = pr.Personne 
                WHERE s.Genre != ''
                AND r.tmsCreate >= $min
                AND r.tmsCreate < $max ";
        if(!empty($sexe)) $sql .= " AND p.Sexe = '$sexe' ";
        if(!empty($age)) $sql .= " AND p.Age = '$age' ";
        $sql.= "GROUP BY s.Genre
                ORDER BY s.Genre DESC";
        $req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
        $res = $req->fetchALL ( PDO::FETCH_ASSOC );
        $res = $this->completeGenres($res);
        return $res;
    }


    /**
     * Retourne un tableau associatif genre => nombre d'evenements
     * @param   int     borne min
     * @param   int     borne max
     * @return  Tableau associatif
     */
    public function getStatsGenreEvenements( $min, $max ) {
        $GLOBALS["Systeme"]->connectSQL();
        $sql = "SELECT s.Genre as Nom, COUNT(e.Id) as NbEvents
                FROM `".MAIN_DB_PREFIX."Reservation-Spectacle` s
                INNER JOIN `".MAIN_DB_PREFIX."Reservation-Evenement` e ON e.SpectacleId = s.Id
                WHERE s.Genre != ''
                AND e.DateDebut >= $min
                AND e.DateDebut < $max
                GROUP BY s.Genre
                ORDER BY s.Genre DESC";
        $req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
        $res = $req->fetchALL ( PDO::FETCH_ASSOC );
        $res = $this->completeGenres($res);
        return $res;
    }



    /**
     * Retourne un tableau associatif genre => nombre de spectacle
     * @param   int     borne min
     * @param   int     borne max
     * @return  Tableau associatif
     */
    public function getStatsGenreSpectacles( $min, $max ) {
        $GLOBALS["Systeme"]->connectSQL();
        $sql = "SELECT Distinct s.Id, s.Genre as Nom , COUNT(s.Id) as NbSpec FROM `".MAIN_DB_PREFIX."Reservation-Spectacle` s
                WHERE s.Genre != ''
                AND s.DateDebut >= $min
                AND s.DateDebut < $max
                GROUP BY s.Genre
                ORDER BY s.Genre DESC";
        $req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
        $res = $req->fetchALL ( PDO::FETCH_ASSOC );
        return $res;
    }



    /**
     	* Ajuste le tableau pasé en paramètres pour avoir TOUS les genres
     	* @param   array   Tableau à compléter
     	* @return  void
    */
    public function getStatsGenrePartProgrammeesReservees(  $min, $max ) {

  	$GLOBALS["Systeme"]->connectSQL();

       	$sql = "SELECT  s.Genre as Nom, SUM(s.Disponibilite) as NbProgrammees, SUM(r.NbPlace) as NbReservees
               	FROM `".MAIN_DB_PREFIX."Reservation-Spectacle` s
               	INNER JOIN `".MAIN_DB_PREFIX."Reservation-Evenement` e ON e.SpectacleId = s.Id
		INNER JOIN `".MAIN_DB_PREFIX."Reservation-ReservationsEvenementId` re ON e.Id = re.EvenementId
		INNER JOIN `".MAIN_DB_PREFIX."Reservation-Reservations` r ON re.Reservations = r.Id
       		WHERE s.Genre != ''
               	AND e.DateDebut >= $min
               	AND e.DateDebut < $max
               	GROUP BY s.Genre
               	ORDER BY NbProgrammees, s.Genre ASC";

	//echo $sql; die;
	$req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
      	$res = $req->fetchALL ( PDO::FETCH_ASSOC );
	return $res;

    }

    /**
     * Ajuste le tableau pasé en paramètres pour avoir TOUS les genres
     * @param   array   Tableau à compléter
     * @return  void
     */
    private function completeGenres( $tab ) {
        if(!is_array($this->allGenres)) :
            $GLOBALS["Systeme"]->connectSQL();
            $sql = "SELECT DISTINCT ( Genre ) FROM `cult-Reservation-Spectacle` WHERE Genre != '' ORDER BY Genre";
            $req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
            $this->allGenres = $req->fetchALL ( PDO::FETCH_ASSOC );
        endif;
        foreach($this->allGenres as $k => $g) :
            $found = false;
            $idx = 0;
            foreach($tab as $j => $e) :
                if($e['Nom']==$g['Genre']) $found = true;
                if(strtolower($e['Nom'])>strtolower($g['Genre'])) $idx++;
            endforeach;
            if(!$found) array_splice($tab, $idx, 0, array(array('Nom' => $g['Genre'], 'NbPlaces' => '0', 'NbEvents' => '0')));
        endforeach;

        return $tab;
    }

    /**
     * Retourne un tableau associatif structure => nombre de résa / nombre de places réservées
     * @return  Tableau associatif
     */
    public function getTabStructures() {
        $sql = "SELECT c.Id, c.Nom, c.Tel, c.Mail, COUNT(r.Id) as NbResa, SUM(r.NbPlace) as NbPlaces
                FROM `".MAIN_DB_PREFIX."Reservation-Client` c
                LEFT JOIN `".MAIN_DB_PREFIX."Reservation-Reservations` r ON r.ClientId = c.Id
                GROUP BY c.Id
                ORDER BY NbPlaces DESC";
        $req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
        $res = $req->fetchALL ( PDO::FETCH_ASSOC );
        foreach($res as $k => $s) if(!$s['NbPlaces']) $res[$k]['NbPlaces'] = '0';
        return $res;
    }

  /**
     * Retourne un tableau associatif structure => nombre de résa / nombre de places réservées
     * @return  Tableau associatif
     */
    public function getTabStructuresAnneeEncours($date=0) {
    	if ($date==0) $date=time();
		$annee = date('Y', $date);
        $start = mktime(0,0,0,1,1, $annee);
        $end = mktime(0,0,0,12,31, $annee);
		
/*        $sql = "SELECT c.Id, c.Nom, c.Tel, c.Mail, COUNT(r.Id) as NbResa, SUM(r.NbPlace) as NbPlaces
	                FROM `".MAIN_DB_PREFIX."Reservation-Client` c
	                LEFT JOIN `".MAIN_DB_PREFIX."Reservation-Reservations` r ON r.ClientId = c.Id
	                LEFT JOIN `".MAIN_DB_PREFIX."Reservation-ReservationsEvenementId` v ON r.Id = v.Reservations
	                LEFT JOIN `".MAIN_DB_PREFIX."Reservation-Evenement` e ON v.EvenementId = e.Id
	                WHERE e.DateDebut >= ".$start." and e.DateFin <=". $end."
                GROUP BY c.Id
                ORDER BY NbPlaces DESC";
				
	*/			
				$sql = "SELECT i.Id, i.Nom, i.Tel, i.Mail, SUM(i.NbResa) as LesResas, SUM(i.NbPlaces) as LesPlaces FROM  
        		(
	        		SELECT c.Id, c.Nom, c.Tel, c.Mail, COUNT(r.Id) as NbResa, SUM(r.NbPlace) as NbPlaces
	                FROM `".MAIN_DB_PREFIX."Reservation-Client` c
	                LEFT JOIN `".MAIN_DB_PREFIX."Reservation-Reservations` r ON r.ClientId = c.Id
	                LEFT JOIN `".MAIN_DB_PREFIX."Reservation-ReservationsEvenementId` v ON r.Id = v.Reservations
	                LEFT JOIN `".MAIN_DB_PREFIX."Reservation-Evenement` e ON v.EvenementId = e.Id
	                WHERE e.DateDebut >= ".$start." and e.DateFin <=". $end."
	                GROUP BY c.Id
                UNION
	                SELECT c.Id, c.Nom, c.Tel, c.Mail, 0 ,0 
	                FROM `".MAIN_DB_PREFIX."Reservation-Client` c
                ) i
                GROUP BY i.Id
                ORDER BY NbPlaces DESC";
        $req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
		//var_dump($sql);
		
        $res = $req->fetchALL ( PDO::FETCH_ASSOC );
        foreach($res as $k => $s) if(!$s['NbPlaces']) $res[$k]['NbPlaces'] = '0';
        return $res;
    }

   /* Retourne un tableau associatif structure => nombre de résa / nombre de places réservées
     * @return  Tableau associatif
     */
    public function getTabSpeEveAnneeEncours($date=0) {
    	if ($date==0) {
    		$date=time();
    		$annee = date('Y', $date);
    		$annee2=$annee+1;	
    	} 
    	if ($date=='-1') {
    		//var_dump($date);
    		$date=time();
    		$annee = date('Y', $date);
    		$annee2=$annee;	
    		$annee--;
    	} 
        $start = mktime(0,0,0,1,1, $annee);
        $end = mktime(0,0,0,1,1, $annee2);
		//var_dump($start,$end);		
			
			$sql="SELECT i.Id, i.Nom,  i.Debut, i.Fin,  SUM(i.NbEve) as TotEve FROM  
			(
				SELECT S.Id ,S.Nom, FROM_UNIXTIME( S.DateDebut ) AS Debut, FROM_UNIXTIME( S.DateFin ) AS Fin, Count( E.Id ) AS NbEve
				FROM `".MAIN_DB_PREFIX."Reservation-Spectacle` AS S
				INNER JOIN `".MAIN_DB_PREFIX."Reservation-Evenement` E ON S.Id = E.SpectacleId
				WHERE E.`DateDebut` >=" . $start . "
				AND E.`DateDebut` <" . $end . " 
				GROUP BY S.Id
			UNION
				SELECT S2.Id, S2.Nom,FROM_UNIXTIME(S2.DateDebut) as Debut, FROM_UNIXTIME( S2.DateFin ) AS Fin, 0  FROM `".MAIN_DB_PREFIX."Reservation-Spectacle` as S2
				where S2.`DateDebut` >=" . $start . " and S2.`DateDebut` <" . $end . "  
			) i 
			 GROUP BY i.Id
			 order by TotEve, i.Debut ASC ";
        	$req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
		//var_dump($sql);
		
        $res = $req->fetchALL ( PDO::FETCH_ASSOC );
        return $res;
    }

	public function getDateFormat ($date,$Format) {
		return date($Format,$date);
	}


    /**
     * Retourne un tableau associatif ville => nombre de résa / nombre de places réservées
     * @return  Tableau associatif
     */
    public function getTabVille( $min = false, $max = false ) {
        $GLOBALS["Systeme"]->connectSQL();

	// demande jeremy chassang le 10/12/13 on affiche 16 villes
        $sql = "SELECT c.Ville, COUNT(r.Id) as NbResa, SUM(r.NbPlace) as NbPlaces
                FROM `".MAIN_DB_PREFIX."Reservation-Client` c
                LEFT JOIN `".MAIN_DB_PREFIX."Reservation-Reservations` r ON r.ClientId = c.Id ";
        if($min) {
            $sql.= "WHERE r.tmsCreate >= $min AND r.tmsCreate <= $max
                    GROUP BY c.Ville
                    ORDER BY NbPlaces DESC ";
                    //LIMIT 0, 21";
        } else {
            $sql.= "GROUP BY c.Ville
                    ORDER BY c.Ville";
        }
        $req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
        $res = $req->fetchALL ( PDO::FETCH_ASSOC );
        foreach($res as $k => $s) {
            if(!$s['NbPlaces']) unset($res[$k]);
            $res[$k]['Ville'] = addslashes(stripslashes($s['Ville']));
        }
        return $res;
    }

    // ajout pour futures fonctions stats sur les personnes
    public function getStatsPersonneReservantes( $min, $max ) {

        $GLOBALS["Systeme"]->connectSQL();
    //    $GLOBALS["Systeme"]->Log->log("eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee");

        $sql = "SELECT Count(Sexe) NbPersonne, Sexe, Rsa, Accompagnateur, P.Age FROM `".MAIN_DB_PREFIX."Reservation-Personne` P
            INNER JOIN `".MAIN_DB_PREFIX."Reservation-PersonneReservationId` PR ON P.Id = PR.Personne 
            INNER JOIN `".MAIN_DB_PREFIX."Reservation-Reservations` R ON R.Id = PR.ReservationsId 
            INNER JOIN `".MAIN_DB_PREFIX."Reservation-ReservationsEvenementId` ER ON ER.Reservations = R.Id 
            INNER JOIN `".MAIN_DB_PREFIX."Reservation-Evenement` E ON E.Id = ER.EvenementId 
            WHERE E.DateDebut >= $min AND E.DateDebut < $max GROUP BY Sexe, Rsa, Accompagnateur, Age";
//echo $sql;
    //    $GLOBALS["Systeme"]->Log->log("sql". $sql);
        $req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
    //    $GLOBALS["Systeme"]->Log->log("666666666666666666666666666666666666666666666666666666666666666");
        $res = $req->fetchALL ( PDO::FETCH_ASSOC );
     //   $GLOBALS["Systeme"]->Log->log("9999999999999999999999999999999999999999999999999999999999999999e");
        return $res;
    }


    // ajout pour futures fonctions stats sur les personnes
    public function getStatsPersonneReservantesAge( $min, $max ) {

        $GLOBALS["Systeme"]->connectSQL();
        //    $GLOBALS["Systeme"]->Log->log("eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee");

        $sql = "SELECT Count(Sexe) NbPersonne, P.Age FROM `".MAIN_DB_PREFIX."Reservation-Personne` P
            INNER JOIN `".MAIN_DB_PREFIX."Reservation-PersonneReservationId` PR ON P.Id = PR.Personne 
            INNER JOIN `".MAIN_DB_PREFIX."Reservation-Reservations` R ON R.Id = PR.ReservationsId 
            INNER JOIN `".MAIN_DB_PREFIX."Reservation-ReservationsEvenementId` ER ON ER.Reservations = R.Id 
            INNER JOIN `".MAIN_DB_PREFIX."Reservation-Evenement` E ON E.Id = ER.EvenementId 
            WHERE E.DateDebut >= $min AND E.DateDebut < $max GROUP BY Age";
//echo $sql;
        //    $GLOBALS["Systeme"]->Log->log("sql". $sql);
        $req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
        //    $GLOBALS["Systeme"]->Log->log("666666666666666666666666666666666666666666666666666666666666666");
        $res = $req->fetchALL ( PDO::FETCH_ASSOC );
        //   $GLOBALS["Systeme"]->Log->log("9999999999999999999999999999999999999999999999999999999999999999e");
        return $res;
    }
	

    public function ImprimerCM(){
        if($this->Imprimer == 1)
            return 'La contre-marque a déjà été imprimée';

        $this->Imprimer = 1;
        parent::Save();

        return array(
            'template' => 'PrintCM',
            'funcTempVars' => array(
                'Resa'=>$this
            )
        );
    }
}

