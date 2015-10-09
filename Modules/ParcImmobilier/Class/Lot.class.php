<?php
/**
 * Lot.class.php
 *
 * php version 5
 *
 * @category  Kobeye
 * @package   ParcImmobilier
 * @author    Abtel agence Web <web@abtel.fr>
 * @copyright 2012 Abtel Agence Web
 * @license   http://agence-web.abtel.fr
 * @link      http://agence-web.abtel.fr
 */

/**
 * Extension de classe pour les lots
 *
 * @category Kobeye
 * @package  ParcImmobilier
 * @author   Abtel agence Web <web@abtel.fr>
 * @license  http://agence-web.abtel.fr
 * @link     http://agence-web.abtel.fr
 */
class Lot extends genericClass
{

    /*--------------------- GLOBAL ---------------------*/

    /**
     * Enregistrement en BDD
     *
     * @return void
     */
    public function Save()
    {
        genericClass::Save();
        // $this->UpdateTypeLogement();
    }

    /**
     * Maj du type logement lié au lot pour les surface mini et maxi et nombre de lots
     * ( actuellement non utilisé )
     *
     * @return	Tableau d'objets
     */
    private function UpdateTypeLogement()
    {
        // Rechercher le type de logement lié à la grille
        $TypL = $this->storproc('ParcImmobilier/TypeLogement/Lot/' . $this->Id);
        $TypeLogement = genericClass::createInstance('ParcImmobilier', $TypL[0]);
        
        if (isset($TypeLogement) && is_object($TypeLogement)) {
            // si la surface saisie est mini ou maxi on met à jour type logement
            if ($this->SurfaceLogement < $TypeLogement->SuperficieMin) {
                $TypeLogement->SuperficieMin = $this->SurfaceLogement;
            }
            if ($this->SurfaceLogement > $TypeLogement->SuperficieMax) {
                $TypeLogement->SuperficieMax = $this->SurfaceLogement;
            }

            // On compte le nombre de lot qui ne sont pas actés donc encore disponible
            $totalLot = 0;
            $NbL = $this->storproc('ParcImmobilier/TypeLogement/' . $TypeLogement->Id . '/Lot');
            foreach ($NbL as $L) {
                if ($L->Statut != 4)
                    $total++;
            }
            $TypeLogement->Nombre = $total;
            $TypeLogement->Save();
        }
    }

    /**
     * Raccourci vers callData
     *
     * @return	Résultat de la requete
     */
    private function storproc($Query, $recurs = '', $Ofst = '', $Limit = '', $OrderType = '', $OrderVar = '', $Selection = '', $GroupBy = '')
    {
        return Sys::$Modules['ParcImmobilier']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
    }
    
    
    /*--------------------- WEB SERVICE ---------------------*/
    
    // public function send
    public function initializeWS($chaine_xml)
    {						
    	$document_xml = new xml2array($chaine_xml); // Instanciation de la classe DomDocument : création d'un nouvel objet
       	$HeaderInfos = $document_xml->Tableau["soap:Envelope"]["#"]["soap:Header"][0];
		
		$Header =  array(	'username' 			=> $HeaderInfos["#"]["username"][0]["#"],
							'password' 			=> $HeaderInfos["#"]["password"][0]["#"],
							'logicaladdress'	=> $HeaderInfos["#"]["logicaladdress"][0]["#"],
							'sign'				=> $HeaderInfos["#"]["sign"][0]["#"],);
		
		$BodyInfos = $document_xml->Tableau["soap:Envelope"]["#"]["soap:Body"][0]["#"];
		$Tab = array();
		$Methode = "";
		
		switch ($Header['logicaladdress']) {
		    case "DenoncerContact":
				$BodyInfos = $document_xml->Tableau["soap:Envelope"]["#"]["soap:Body"][0]["#"]["DenoncerContact"][0];
				$Methode = "DenoncerContact";
		        $Tab = $this->DenoncerContact($Header, $BodyInfos);
		        break;
			case "PoserOption":
				$BodyInfos = $document_xml->Tableau["soap:Envelope"]["#"]["soap:Body"][0]["#"]["PoserOption"][0];
				$Methode = "PoserOption";
		        $Tab = $this->PoserOption($Header, $BodyInfos);
		        break;
			case "AnnulerOption":
				$BodyInfos = $document_xml->Tableau["soap:Envelope"]["#"]["soap:Body"][0]["#"]["AnnulerOption"][0];
				$Methode = "AnnulerOption";
		        $Tab = $this->AnnulerOption($Header, $BodyInfos);
		        break;
			case "Reserver":
				$BodyInfos = $document_xml->Tableau["soap:Envelope"]["#"]["soap:Body"][0]["#"]["Reserver"][0];
				$Methode = "Reserver";
		        $Tab = $this->Reserver($Header, $BodyInfos);
		        break;
			case "AnnulerReservation":
				$BodyInfos = $document_xml->Tableau["soap:Envelope"]["#"]["soap:Body"][0]["#"]["AnnulerReservation"][0];
				$Methode = "AnnulerReservation";
		        $Tab = $this->AnnulerReservation($Header, $BodyInfos);
		        break;
			default:
		        print_r("erreur");
				return;
		        break;
		}

		return $this->setResponse($Tab, $Methode);
    }

	public function DenoncerContact($Header, $BodyInfos)
    {
    	$ProgrammeInfo = $this->getProgrammeInfo($BodyInfos, false);
		$CommercialInfo = $this->getCommercialInfo($BodyInfos, false);
		
		$BodyContact1 = $BodyInfos["#"]["Contact1"][0];
		$Contact1Info = $this->getContactInfo($BodyContact1, false);
		
		$BodyContact2 = $BodyInfos["#"]["Contact2"][0];
		$Contact2Info = $this->getContactInfo($BodyContact2, false);
		
		
		$DenoncerContact = array(	'Header'		=> $Header,
									'Programme' 	=> $ProgrammeInfo,
									'Contact1' 		=> $Contact1Info,
									'Contact2'		=> $Contact2Info,
									'Commercial'	=> $CommercialInfo,);
		$Code = array();
		
		$R = $this->storproc('ParcImmobilier/Residence/' . $DenoncerContact['Programme']['ReferenceExterne']);
		if (empty($R)) {
			$Code = $this->getMessageWithCode("2010");
			return $Code;
		}
		else{
			$Residence = genericClass::createInstance('ParcImmobilier', $R[0]);
			if($Residence->Logement == 0){
				$Code = $this->getMessageWithCode("2011");
				return $Code;
			}
		}
		$Cial = $this->storproc('ParcImmobilier/Commercial/Mail=' . $DenoncerContact['Commercial']['Email']);
		if (empty($Cial)) {
			$Commercial = genericClass::createInstance('ParcImmobilier', 'Commercial');
			$Commercial->Set('Nom', $DenoncerContact['Commercial']['Nom']);
			$Commercial->Set('Prenom', $DenoncerContact['Commercial']['Prenom']);
			$Commercial->Set('Fonction', 'AGIMMO');
			$Commercial->Set('Mail', $DenoncerContact['Commercial']['Email']);
			$Commercial->Set('Ville', $DenoncerContact['Commercial']['SuccursaleLibelle']);
			$Commercial->Set('Telephone', $DenoncerContact['Commercial']['Tel1']);
			$Commercial->Set('Referent', 1);
			
			$Commercial->Save();
		}

		$Cact1 = $this->storproc('ParcImmobilier/Denonciation/Adresse1=' . $DenoncerContact['Contact1']['Adresse']);
		if (empty($Cact1)) {
			$Contact = genericClass::createInstance('ParcImmobilier', 'Denonciation');
			$Contact->Set('Civilite', $DenoncerContact['Contact1']['Civilite']);
			$Contact->Set('Nom', $DenoncerContact['Contact1']['Nom']);
			$Contact->Set('Prenom', $DenoncerContact['Contact1']['Prenom']);
			$Contact->Set('Adresse1', $DenoncerContact['Contact1']['Adresse1']);
			$Contact->Set('CodePostal', $DenoncerContact['Contact1']['CodePostal']);
			$Contact->Set('Ville', $DenoncerContact['Contact1']['Ville']);
			$Contact->Set('Pays', 'France');
			
			$Contact->Save();
		}
		else{
			$Contact = genericClass::createInstance('ParcImmobilier', $Cact1[0]);
			$Cial = $this->storproc('ParcImmobilier/Commercial/' . $Contact->CommercialId);
			
			if($Cial->Fonction == 'AGIMMO')
				$Code = $this->getMessageWithCode("1000");
			else
				$Code = $this->getMessageWithCode("1001");
			
			return $Code;
		}
		
		if($DenoncerContact['Contact2'] != NULL)
		{
			$Cact2 = $this->storproc('ParcImmobilier/Denonciation/Adresse1=' . $DenoncerContact['Contact2']['Adresse']);
			if (empty($Cact2)) {
				$Contact = genericClass::createInstance('ParcImmobilier', 'Denonciation');
				$Contact->Set('Civilite', $DenoncerContact['Contact1']['Civilite']);
				$Contact->Set('Nom', $DenoncerContact['Contact1']['Nom']);
				$Contact->Set('Prenom', $DenoncerContact['Contact1']['Prenom']);
				$Contact->Set('Adresse1', $DenoncerContact['Contact1']['Adresse1']);
				$Contact->Set('CodePostal', $DenoncerContact['Contact1']['CodePostal']);
				$Contact->Set('Ville', $DenoncerContact['Contact1']['Ville']);
				$Contact->Set('Pays', 'France');
				
				$Contact->Save();
			}
			else{
				$Contact = genericClass::createInstance('ParcImmobilier', $Cact2[0]);
				$Cial = $this->storproc('ParcImmobilier/Commercial/' . $Contact->CommercialId);
				
				if($Cial->Fonction == 'AGIMMO')
					$Code = $this->getMessageWithCode("1000");
				else
					$Code = $this->getMessageWithCode("1001");
				
				return $Code;
			}
		}
		
		return $this->getMessageWithCode("0000");
    }
	
	public function PoserOption($Header, $BodyInfos)
    {
    	$ProgrammeInfo = $this->getProgrammeInfo($BodyInfos, false);
		$CommercialInfo = $this->getCommercialInfo($BodyInfos, false);
		
		$BodyContact = $BodyInfos["#"]["Contact"][0];
		$ContactInfo = $this->getContactInfo($BodyContact, false);
		
		$LotInfo = $this->getLotInfo($BodyContact, true, false);
		
		$OptionInfo = array(	'DateDebut' 		=> $BodyInfos["#"]["Option"][0]["#"]["DateDebut"][0]["#"],
								'DateFin' 			=> $BodyInfos["#"]["Option"][0]["#"]["DateFin"][0]["#"],);	
		
		$PoserOption = array(	'Header'		=> $Header,
								'Programme' 	=> $ProgrammeInfo,
								'Lot'			=> $LotInfo,
								'Contact' 		=> $ContactInfo,
								'Commercial'	=> $CommercialInfo,
								'Option'		=> $OptionInfo,);
		
		$Code = array();
		
		$R = $this->storproc('ParcImmobilier/Residence/' . $PoserOption['Programme']['ReferenceExterne']);
		if (empty($R)) {
			$Code = $this->getMessageWithCode("2010");
			return $Code;
		}
		else{
			$Residence = genericClass::createInstance('ParcImmobilier', $R[0]);
			if($Residence->Logement == 0){
				$Code = $this->getMessageWithCode("2011");
				return $Code;
			}
		}
    }
	public function AnnulerOption($Header, $BodyInfos)
    {
    	$ProgrammeInfo = $this->getProgrammeInfo($BodyInfos, true);
		$CommercialInfo = $this->getCommercialInfo($BodyInfos, true);
		
		$BodyContact = $BodyInfos["#"]["Contact"][0];
		$ContactInfo = $this->getContactInfo($BodyContact, true);
		
		$LotInfo = $this->getLotInfo($BodyContact, false, true);
		
		$OptionInfo = array(	'DateDebut' 	=> $BodyInfos["#"]["Option"][0]["#"]["DateDebut"][0]["#"],);	
		
		return array(	'Header'		=> $Header,
						'Programme' 	=> $ProgrammeInfo,
						'Lot'			=> $LotInfo,
						'Contact' 		=> $ContactInfo,
						'Commercial'	=> $CommercialInfo,
						'Option'		=> $OptionInfo,);
    }

	public function Reserver($Header, $BodyInfos)
    {
    	$ProgrammeInfo = $this->getProgrammeInfo($BodyInfos, false);
		$CommercialInfo = $this->getCommercialInfo($BodyInfos, false);
		
		$BodyContact = $BodyInfos["#"]["Contact"][0];
		$ContactInfo = $this->getContactInfo($BodyContact, false);
		
		$LotInfo = $this->getLotInfo($BodyContact, true, false);
		
		$ReservationInfo = array(	'Date' 		=> $BodyInfos["#"]["Reservation"][0]["#"]["Date"][0]["#"],
									'Prix' 		=> $BodyInfos["#"]["Reservation"][0]["#"]["Prix"][0]["#"],);	
		
		return array(	'Header'		=> $Header,
						'Programme' 	=> $ProgrammeInfo,
						'Lot'			=> $LotInfo,
						'Contact' 		=> $ContactInfo,
						'Commercial'	=> $CommercialInfo,
						'Reservation'	=> $ReservationInfo,);
    }
	public function AnnulerReservation($Header, $BodyInfos)
    {
    	$ProgrammeInfo = $this->getProgrammeInfo($BodyInfos, true);
		$CommercialInfo = $this->getCommercialInfo($BodyInfos, true);
		
		$BodyContact = $BodyInfos["#"]["Contact"][0];
		$ContactInfo = $this->getContactInfo($BodyContact, true);
		
		$LotInfo = $this->getLotInfo($BodyContact, true, true);
		
		$ReservationInfo = array(	'Date' 	=> $BodyInfos["#"]["Reservation"][0]["#"]["Date"][0]["#"],);	
		
		return array(	'Header'		=> $Header,
						'Programme' 	=> $ProgrammeInfo,
						'Lot'			=> $LotInfo,
						'Contact' 		=> $ContactInfo,
						'Commercial'	=> $CommercialInfo,
						'Reservation'	=> $ReservationInfo,);
    }
	
	public function getProgrammeInfo($BodyInfos, $IsAnnulation)
    {
    	if($IsAnnulation)
			return array(	'Reference' 		=> $BodyInfos["#"]["Programme"][0]["#"]["Reference"][0]["#"],
							'ReferenceExterne' 	=> $BodyInfos["#"]["Programme"][0]["#"]["ReferenceExterne"][0]["#"],);
		else
			return array(	'Reference' 		=> $BodyInfos["#"]["Programme"][0]["#"]["Reference"][0]["#"],
							'ReferenceExterne' 	=> $BodyInfos["#"]["Programme"][0]["#"]["ReferenceExterne"][0]["#"],
							'Nom'				=> $BodyInfos["#"]["Programme"][0]["#"]["Nom"][0]["#"],
							'Region'			=> $BodyInfos["#"]["Programme"][0]["#"]["Region"][0]["#"],);
    }
	public function getCommercialInfo($BodyInfos, $IsAnnulation)
    {
    	if($IsAnnulation)
			return array(	'MemoId' 			=> $BodyInfos["#"]["Commercial"][0]["#"]["MemoId"][0]["#"],);
		else
			return array(	'MemoId' 			=> $BodyInfos["#"]["Commercial"][0]["#"]["MemoId"][0]["#"],
							'Nom' 				=> $BodyInfos["#"]["Commercial"][0]["#"]["Nom"][0]["#"],
							'Prenom'			=> $BodyInfos["#"]["Commercial"][0]["#"]["Prenom"][0]["#"],
							'Email'				=> $BodyInfos["#"]["Commercial"][0]["#"]["Email"][0]["#"],
							'SuccursaleCode' 	=> $BodyInfos["#"]["Commercial"][0]["#"]["SuccursaleCode"][0]["#"],
							'SuccursaleLibelle'	=> $BodyInfos["#"]["Commercial"][0]["#"]["SuccursaleLibelle"][0]["#"],
							'Tell'				=> $BodyInfos["#"]["Commercial"][0]["#"]["Tel1"][0]["#"],);
    }
	public function getContactInfo($ContactInfos, $IsAnnulation)
    {
    	if($IsAnnulation)
			return array(	'Reference' 	=> $ContactInfos["#"]["Reference"][0]["#"],);
		else
			return array(	'Reference' 	=> $ContactInfos["#"]["Reference"][0]["#"],
							'Civilite' 		=> $ContactInfos["#"]["Civilite"][0]["#"],
							'Nom'			=> $ContactInfos["#"]["Nom"][0]["#"],
							'Prenom'		=> $ContactInfos["#"]["Prenom"][0]["#"],
							'Adresse' 		=> $ContactInfos["#"]["Adresse"][0]["#"],
							'CodePostal' 	=> $ContactInfos["#"]["CodePostal"][0]["#"],
							'Ville'			=> $ContactInfos["#"]["Ville"][0]["#"],
							'Pays'			=> $ContactInfos["#"]["Pays"][0]["#"],);
    }
	public function getLotInfo($BodyInfos, $IsDestination_inc, $IsAnnulation)
    {
    	if($IsDestination_inc)
		{
			if($IsAnnulation)
				return array(	'Reference' 		=> $BodyInfos["#"]["Lot"][0]["#"]["Reference"][0]["#"],
								'ReferenceExterne' 	=> $BodyInfos["#"]["Lot"][0]["#"]["ReferenceExterne"][0]["#"],);
			else
				return array(	'Reference' 		=> $BodyInfos["#"]["Lot"][0]["#"]["Reference"][0]["#"],
								'ReferenceExterne' 	=> $BodyInfos["#"]["Lot"][0]["#"]["ReferenceExterne"][0]["#"],
								'Destination'		=> $BodyInfos["#"]["Lot"][0]["#"]["Destination"][0]["#"],);
		}
		else
		{
			return array(	'Reference' 		=> $BodyInfos["#"]["Lot"][0]["#"]["Reference"][0]["#"],
							'ReferenceExterne' 	=> $BodyInfos["#"]["Lot"][0]["#"]["ReferenceExterne"][0]["#"],);
		}
    }
	
	public function setResponse($Tableau, $Methode)
    {
    	$code = $Tableau[0];
		$message = $Tableau[1];
		
    	return '<?xml version="1.0" encoding="utf-8"?>
				<soap:Envelope
				xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
				xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
				xmlns:xsd="http://www.w3.org/2001/XMLSchema">
					<soap:Body>
						<'.$Methode.'Response xmlns="http://tempuri.org/">
							<'.$Methode.'Result>
								<Code>'.$code.'</Code>
								<Message>'.$message.'</Message>
							</'.$Methode.'Result>
						</'.$Methode.'Response>
					</soap:Body>
				</soap:Envelope>';
    }

	public function getMessageWithCode($code)
	{
		switch ($code) {
		    case "0000":
				$Erreur = array("0000","Action réalisée avec succès");
		        break;
			case "1000":
				$Erreur = array("1000","Dénonciation déjà réalisée par AGImmo");
		        break;
			case "1001":
				$Erreur = array("1001","Dénonciation déjà réalisée par un autre partenaire");
		        break;
			case "1010":
				$Erreur = array("1010","Option déjà posée par AGImmo");
		        break;
			case "1011":
				$Erreur = array("1011","Durée d’option invalide (motif à fournir par le partenaire)");
		        break;
			case "1020":
				$Erreur = array("1020","Réservation déjà réalisée par AGImmo");
		        break;
			case "2000":
				$Erreur = array("2000","Traitement impossible");
		        break;
			case "2001":
				$Erreur = array("2001","Trop d’option posée sur ce lot");
		        break;
			case "2002":
				$Erreur = array("2002","Trop d’option posée par ce commerciale");
		        break;
			case "2003":
				$Erreur = array("2003","Trop d’option posée sur ce programme");
		        break;
			case "2010":
				$Erreur = array("2010","Programme introuvable");
		        break;
			case "2011":
				$Erreur = array("2011","Programme terminé");
		        break;
			case "2020":
				$Erreur = array("2020","Lot introuvable");
		        break;
			case "2021":
				$Erreur = array("2021","Lot déjà optionné");
		        break;
			case "2022":
				$Erreur = array("2022","Lot déjà vendu");
		        break;
			case "2023":
				$Erreur = array("2023","Lot non optionné");
		        break;
			case "2024":
				$Erreur = array("2024","Lot non réservé");
		        break;
			case "2030":
				$Erreur = array("2030","Prix incorrect");
		        break;
			case "9000":
				$Erreur = array("9000","Paramètre en entrée incorrect :");
		        break;
			case "9979":
				$Erreur = array("9979","Authentification invalide.");
		        break;
			case "9989":
				$Erreur = array("9989","Habilitation insuffisante.");
		        break;
			case "9999":
				$Erreur = array("9999","Une erreur interne est survenue.");
		        break;
		}
	
		return $Erreur;
	}
}
