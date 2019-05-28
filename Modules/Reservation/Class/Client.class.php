<?php
class Client extends genericClass {	
	function Save() {
		genericClass::Save();
		//Verification du champ NumeroGroupe (contenant l'id du groupe)
		if ($this->NumeroGroupe==""){
		//Creation du groupe
		$this->createGroupe();
		}else{
			//Verification de l'existence du groupe
			$Tab = Sys::$Modules["Systeme"]->callData("Systeme/Group/".$this->NumeroGroupe,0,0,1);
			if (!is_array($Tab[0])){
				//Creation du groupe
				$this->createGroupe();
			}else{
				//On le met a jour
				$G = genericClass::createInstance("Systeme",$Tab[0]);
				$G->Nom = $this->Nom;
				$G->Save();
			}
			//$Query,$recurs="",$Ofst="",$Limit="",$OrderType="",$OrderVar="",$Selection=""
		}
	}
	//CREATION DE GROUPE
	function createGroupe() {
		$Gr = genericClass::createInstance("Systeme","Group");
		$Gr->Set("Nom",$this->Nom);
		$Gr->AddParent("Systeme/Group/4");
		$Gr->Save();
		$this->Set("NumeroGroupe",$Gr->Id);
		genericClass::Save();
	}

	function sendHeader() {
		header("Content-type: application/vnd.ms-excel"); 
		header("Content-disposition: attachment; filename=\"cultures.csv\"");

	}

	function renvoietime ($jour,$mois) {
		
		return mktime(0, 0, 0, $mois , $jour, date('Y'));
	}
  	
	function addLigne($ctc) {
		$fullLigne = func_get_args();
		$fullLigne = implode(',',$fullLigne);
		//$GLOBALS["Systeme"]->Log->log($ctc);
		//$GLOBALS["Systeme"]->Log->log($fullLigne);
    		echo  $fullLigne  . "\r\n";
  	}
 
	function ImprimeEtiquette ($CodePos, $Ville , $Dept,  $Actives,$Etiquettes) {

		require_once "Class/Lib/FPDF.class.php";
		$pdf=new FPDF();
		$pdf->SetAutoPageBreak(false,0);
		$pdf->marge_gauche=0;
		$pdf->marge_droite=0;
		$pdf->marge_haute=0;
		$pdf->marge_basse=0;

		if ($CodePos!='') $filtre = "CodPos='" . $CodePos . "'" ;
		if ($Ville!='') $filtreV .= "Ville='" . $Ville . "'" ;
		if ($filtreV!='') $filtre .= " and " ;
		$filtre .=  $filtreV;
		if ($Dept!='') $filtreD .= " Departement='" . $Dept . "'" ;
		if ($filtre!='') $filtre .= " and " ;
 		$filtre .=  $filtreD;
		if ($Etiquettes!='3') {
			if ($filtre!='') $filtre .= " and ";
			$filtre .= " Etiq='" . $Etiquettes. "'";
		}
		$sql = "SELECT * FROM `cult-Reservation-Client` ";
		if ($filtre!='') $sql .= " WHERE ".$filtre . " ;";
		
		$GLOBALS["Systeme"]->connectSQL();
	    	$req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
		$res = $req->fetchALL ( PDO::FETCH_ASSOC );
		$nbetiq=16;
        	foreach($res as $resa) {
			$Imprime= true;
			if ($Actives!='3') {
				$DateDebut=mktime(0,0,0,01,01,date('y'));
				$DateFin==mktime(23,59,59,12,31,date('y'));;
				$sql2 = "SELECT count( R.Id ) as Tot
FROM `cult-Reservation-Reservations` AS R left join `cult-Reservation-Client` as C  on C.Id = R.ClientId where'" . $resa['Id'] . "' and R.tmsCreate>=".$DateDebut . " and R.tmsCreate<=" .$DateFin;
				$req = $GLOBALS["Systeme"]->Db[0]->query( $sql2 );
				if ($Actives==1&&$req[0]['Tot']>0) {
					$Imprime= true;		
				} else {
					$Imprime= false;		
				}
				if ($Actives==2&&$req[0]['Tot']>0) $Imprime= false;		
			}
			if ($Imprime==true) {
				if ($nbetiq==17) { $pdf->AddPage(); $nbetiq=1;}
				if ($nbetiq==1) {
					$Y=4;$X=1;
					$XG=$X;$YG=$Y;
				} else {
					if ($nbetiq % 2) {
						$Y=$YG;	
						$X=1;$Y+=38;
						$XG=$X;$YG=$Y;
					} else {
						$X=$XG;
						$X+=105;$Y=$YG;
						$XG=$X;$YG=$Y;
					} 
				}
				
				$pdf->SetXY($X,$Y);
				$pdf->SetFont('Helvetica','B', 12);
				$pdf->SetTextColor(0,0,0);
				$text=$resa['Nom'] ; 
				$text=str_replace ( "\'", "'", $text) ;
				$pdf->MultiCell(100,4,$text,0,'R' ); 
				$Y= $pdf->GetY();
				$Y+=5;
				$pdf->SetXY($X,$Y);
				$pdf->SetFont('Helvetica','', 10);
				$text= $resa['Adresse'] . "\n" . $resa['CodPos'] . " " . $resa['Ville']; 
				$text=str_replace ( "\'", "'", $text) ;
				$pdf->MultiCell(100,4,$text,0,'R' ); 

				$nbetiq++;
			}
		}
		$pdf->Output();
		$pdf->Close();
		
	}
	
	function ImprimeEtiquette2 ($CodePos, $Ville , $Dept,  $Actives, $Etiquettes) {

		require_once "Class/Lib/FPDF.class.php";
		$pdf=new FPDF();
		$pdf->SetAutoPageBreak(false,0);
		$pdf->marge_gauche=0;
		$pdf->marge_droite=0;
		$pdf->marge_haute=0;
		$pdf->marge_basse=0;

		if ($CodePos!='') $filtre = "CodPos='" . $CodePos . "'" ;
		if ($Ville!='') $filtreV .= "Ville='" . $Ville . "'" ;
		if ($filtreV!='') $filtre .= " and " ;
		$filtre .=  $filtreV;
		if ($Dept!='') $filtreD .= " Departement='" . $Dept . "'" ;
		if ($filtre!='') $filtre .= " and " ;
 		$filtre .=  $filtreD;
		if ($Etiquettes!='3') {
			if ($filtre!='') $filtre .= " and ";
			$filtre .= " Etiq='" . $Etiquettes. "'";
		}
		$sql = "SELECT * FROM `cult-Reservation-Client` ";
		if ($filtre!='') $sql .= " WHERE ".$filtre . " ;";
		
		$GLOBALS["Systeme"]->connectSQL();
	    	$req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
		$res = $req->fetchALL ( PDO::FETCH_ASSOC );
		$nbetiq=16;
        	foreach($res as $resa) {
			$Imprime= true;
			if ($Actives!='3') {
				$DateDebut=mktime(0,0,0,01,01,date('y'));
				$DateFin=mktime(23,59,59,12,31,date('y'));
				// version qui buggé donc ....
				/*$sql2 = "SELECT count( R.Id ) as Tot
FROM `cult-Reservation-Reservations` AS R left join `cult-Reservation-Client` as C  on C.Id = R.ClientId where  R.ClientId=" . $resa['Id'] . " and R.tmsCreate>=".$DateDebut . " and R.tmsCreate<=" .$DateFin;

				if ($Actives=='1')  {
					$Imprime= false;		
					if ($req->Tot>0) $Imprime= true;		
					//echo "ici 1 - " . $req->Tot . "<br />"; 

				}
				if ($Actives=='2')  {
					$Imprime= true;		
					if ($req->Tot>0) $Imprime= false;		
					//echo "ici 2 - " . $req->Tot . "<br />"; 
				}
*/
				$sql2 = "SELECT  R.Id  as Tot FROM `cult-Reservation-Reservations` AS R left join `cult-Reservation-Client` as C  on C.Id = R.ClientId where  R.ClientId=" . $resa['Id'] . " and R.tmsCreate>=".$DateDebut . " and R.tmsCreate<=" .$DateFin;
				$req2 = $GLOBALS["Systeme"]->Db[0]->query( $sql2 );
				$res2 = $req2->fetchALL ( PDO::FETCH_ASSOC );
				if ($Actives=='1')  {
					$Imprime= false;		
					foreach($res2 as $resa2) {
						$Imprime= true;		
					}
				}
				if ($Actives=='2')  {
					$Imprime= true;		
					foreach($res2 as $resa2) {
						$Imprime= false;		
					}
				}
					
			}
			if ($Imprime==true) {
				if ($nbetiq==17) { $pdf->AddPage(); $nbetiq=1;}
				if ($nbetiq==1) {
					$Y=14;$X=1;
					$XG=$X;$YG=$Y;
				} else {
					if ($nbetiq % 2) {
						$Y=$YG;	
						$X=1;$Y+=36;
						$XG=$X;$YG=$Y;
					} else {
						$X=$XG;
						$X+=99;$Y=$YG;
						$XG=$X;$YG=$Y;
					} 
				}
				
				$pdf->SetXY($X,$Y);
				$pdf->SetFont('Helvetica','B', 12);
				$pdf->SetTextColor(0,0,0);
				$text=$resa['Nom'] ; 
				$text=str_replace ( "\'", "'", $text) ;
				$pdf->MultiCell(100,4,$text,0,'R' ); 
				$Y= $pdf->GetY();
				$Y+=5;
				$pdf->SetXY($X,$Y);
				$pdf->SetFont('Helvetica','', 10);
				$text= $resa['Adresse'] . "\n" . $resa['CodPos'] . " " . $resa['Ville']; 
				$text=str_replace ( "\'", "'", $text) ;
				$pdf->MultiCell(100,4,$text,0,'R' ); 

				$nbetiq++;
			}
		}

		$pdf->Output();
		$pdf->Close();
	}
	


}


?>