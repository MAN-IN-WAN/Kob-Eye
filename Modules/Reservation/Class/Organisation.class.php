<?php
class Organisation extends genericClass {
    	function Save() {
	        genericClass::Save();
	        //Verification du champ NumeroGroupe (contenant l'id du groupe)
	        if ($this->NumeroGroupe==""){
            	//Creation du groupe
            	$this->createGroupe();
        	}else{
            	//Verification de l'existence du groupe
            	$Tab = Sys::$Modules["Systeme"]->callData("Systeme/Group/".$this->NumeroGroupe,0,0,1,"","","COUNT(DISTINCT(m.Id))");
            	if ($Tab[0]["COUNT(DISTINCT(m.Id))"]!=1){
	                //Creation du groupe
                	$this->createGroupe();
            	}
            	//$Query,$recurs="",$Ofst="",$Limit="",$OrderType="",$OrderVar="",$Selection=""
        	}
            return true;
    	}
    	//CREATION DE GROUPE
    	function createGroupe() {
	        $Gr = genericClass::createInstance("Systeme","Group");
	        $Gr->Set("Nom",$this->Nom);
		$Gr->Set("Skin","GestionStructures");
	       	$Gr->AddParent("Systeme/Group/3");
	        $Gr->Save();
	        $this->Set("NumeroGroupe",$Gr->Id);
	        genericClass::Save();
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

		$sql = "SELECT * FROM `cult-Reservation-Organisation` ";
		if ($filtre!='') $sql .= " WHERE ".$filtre . " ;";


		$GLOBALS["Systeme"]->connectSQL();
	    	$req = $GLOBALS["Systeme"]->Db[0]->query( $sql );
		$res = $req->fetchALL ( PDO::FETCH_ASSOC );
		$nbetiq=16;
        	foreach($res as $resa) {
			$Imprime= true;
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