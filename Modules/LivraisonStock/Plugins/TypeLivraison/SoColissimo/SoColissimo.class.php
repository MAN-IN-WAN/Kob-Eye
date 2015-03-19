<?php
/**
* Solissimo
* Gestion des livraisons socolissimo en mode flexibilité
* 1- Authentification
* 2- Configuration livraison
* 3- Recuperération des points de livraison
* 4- Récupération de la tarification
* 5- Validation de la livraison
*/


require_once( dirname(dirname(__FILE__)).'/TypeLivraison.interface.php' );

class LivraisonStockTypeLivraisonSoColissimo extends Plugin implements LivraisonStockTypeLivraisonPlugin {

	public function setTypeLivraison( $typeLivraison ) {
		$this->TypeLivraison = $typeLivraison;
	}

	public function getTarif( $commande, $adresseLivraison ) {
		// Vérifie si la zone est couverte
		$Zone = $this->TypeLivraison->GetZone($adresseLivraison->Pays,$adresseLivraison->CodePostal);
		if(!$Zone) return -1;

		// Critères du tarif à respecter
		$testTarif = 0;
		if ($this->TypeLivraison->SelectionPoids) $testTarif++;
		if ($this->TypeLivraison->SelectionQuantite) $testTarif++;
		if ($this->TypeLivraison->SelectionVolume) $testTarif++;
		if ($this->TypeLivraison->SelectionMontant) $testTarif++;
		if ($this->Params['MODE'] == 1) $testTarif++;

		// On cherche les tarifs correspondants
		$Tarifs = $this->TypeLivraison->storproc('LivraisonStock/ZoneLivraison/'.$Zone->Id . '/TarifLivraison/Actif=1',false,0,100,'ASC','Ordre');
		if (is_array($Tarifs)) {
			foreach ($Tarifs as $TL) {
				$TL = genericClass::createInstance('LivraisonStock',$TL);
				$OkTarif=0;
				if($this->TypeLivraison->SelectionPoids) {
					// Vérification du poids maximum
					if ($commande->Poids <= $TL->MaxiPoids || $TL->MaxiPoids==-1)  $OkTarif++;
				}
				if($this->TypeLivraison->SelectionQuantite) {
					// Vérification de la quantité maximum
					if ( $commande->Qte <=$TL->MaxiQuantite || $TL->MaxiQuantite==-1) $OkTarif++;
				}
				if($this->TypeLivraison->SelectionVolume) {
					// Vérification du volume maximum
					if ( $commande->Volume <= $TL->MaxiVolume || $TL->MaxiVolume==-1) $OkTarif++;
				}
				if($this->TypeLivraison->SelectionMontant) {
					// Vérification du montant maximum
					if ($commande->MontantTTC <= $TL->MaxiMontant || $TL->MaxiMontant==-1) $OkTarif++;
				}
				if($this->Params['MODE'] == 1) {
					// Mode "hors domicile, on doit avoir au moins un choix de point relai
					//if(sizeof($this->getChoix( $commande, $adresseLivraison )) > 0)
					$OkTarif++;
				}
				// Tarif valide -> on vérifie qu'il propose au moins un choix de retrait pour le valider
				if($OkTarif >= $testTarif) return $TL;
			}
		}

		// Pas de tarif
		return -1;
	}

	public function getChoix( $commande, $adresseLivraison ) {
		// On a déjà fait la requete
		if(!empty($this->Choix)) return $this->Choix;

		// Initialisation
		$this->Choix = array();

		// Uniquement en mode "Hors domicile"
		if($this->Params['MODE'] == 1) {
			$requestId = md5("Ab".$adresseLivraison->Id.time());
			$url = "https://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/findRDVPointRetraitAcheminement";
			$url .= "?accountNumber=" . $this->Params['USERNAME'];
			$url .= "&password=" . $this->Params['PASSWORD'];
			$url .= "&address=" . urlencode($adresseLivraison->Adresse);
			$url .= "&zipCode=" . $adresseLivraison->CodePostal;
			$url .= "&city=" . $adresseLivraison->Ville;
			$url .= "&weight=" . $commande->Poids * 1000;
			$url .= "&shippingDate=" . date('d/m/Y', time() + 86400 * $this->TypeLivraison->LivreEn);
			$url .= "&filterRelay=1";
			$url .= "&requestId=" . $requestId;
      
			$content = @file_get_contents($url);    
			if(!$content) return array();
			$x = new xml2array($content);
			$points = $x->Tableau["soap:Envelope"]["#"]["soap:Body"][0]["#"]["ns1:findRDVPointRetraitAcheminementResponse"][0]["#"]["return"][0]["#"]["listePointRetraitAcheminement"];
			if(is_array($points)) {
				foreach($points as $pt) {
					if($this->Params['NBMAXI'] && sizeof($this->Choix) >= $this->Params['NBMAXI']) break;	
					if(!$this->Params['DISTANCE'] || $pt["#"]["distanceEnMetre"][0]["#"] < $this->Params['DISTANCE'])
						$this->Choix[] = array("Uid" => ucwords(strtolower($pt["#"]["identifiant"][0]["#"])), "Libelle" => "<strong>" . ucwords(strtolower($pt["#"]["nom"][0]["#"])) . "</strong><br />" . strtolower($pt["#"]["adresse1"][0]["#"]) . "<br />" . $pt["#"]["codePostal"][0]["#"] . " " . ucwords(strtolower($pt["#"]["localite"][0]["#"])));
				}
			}
		}

		// var_dump($this->Choix);

		return $this->Choix;
	}

	
	public function getChoixIntitule( $commande, $adresseLivraison, $Uid ) {
		$choix = $this->getChoix( $commande, $adresseLivraison );
		foreach($choix as $c) if($c["Uid"]==$Uid) return $c["Libelle"];
		return "";
	}

	public function isAdresseLivraisonAlternative() {
		return $this->Params['MODE'] == 1;
	}

	public function updateInfosBL( $bonLivraison ) {
		return;
		$commande = $bonLivraison->getCommande();
		$adresseLivraison = $commande->getAdresseLivraison();
		$client = $commande->getClient();
		$magasin = $commande->getMagasin();

		/*
		// URL  PRODUCTION / TEST
		$url = "https://ws.colissimo.fr/soap.shippingclpV2/services/WSColiPosteLetterService?wsdl";
		// $url = "https://217.108.161.162/soap.shippingclpV2/services/WSColiPosteLetterService?wsdl";
		echo '<pre>';

		$commande = $bonLivraison->getCommande();
		$adresseLivraison = $commande->getAdresseLivraison();
		$client = $commande->getClient();
		$magasin = $commande->getMagasin();

		// PARAMS
		$stdLetter = new stdClass();
		$stdLetter->password = new SoapVar($this->Params['PASSWORD'], XSD_STRING);
		$stdLetter->contractNumber = new SoapVar($this->Params['USERNAME'], XSD_INT);

		$stdService = new stdClass();
		$stdService->dateDeposite = new SoapVar(date("c",$bonLivraison->DateLivPrev), XSD_DATETIME);
		// $stdService->returnType = new SoapVar("CreatePDFFile", XSD_STRING);
		$stdService->serviceType = new SoapVar("SO", XSD_STRING);
		$stdService->crbt = new SoapVar(false, XSD_BOOLEAN);
		$stdService->totalAmount = new SoapVar($bonLivraison->MontantLivraisonTTC*100, XSD_INT);
		$stdService->commandNumber = new SoapVar($bonLivraison->NumBL, XSD_STRING);
		$stdService->commercialName = new SoapVar($this->Params['NOM'], XSD_STRING);

		$stdParcel = new stdClass();
		// $stdParcel->weight = new SoapVar($commande->Poids, XSD_FLOAT);
		$stdParcel->horsGabarit = new SoapVar($this->isMecanisable( $commande ), XSD_BOOLEAN);
		$stdParcel->horsGabaritAmount = new SoapVar(600, XSD_INT);

		$stdDest = new stdClass();
		// $stdDest->alert = null;
		// $stdDest->codeBarForreference = null;

		$stdDestAdr = new stdClass();
		$stdDestAdr->name = new SoapVar($adresseLivraison->Nom, XSD_STRING);
		$stdDestAdr->surname = new SoapVar($adresseLivraison->Prenom, XSD_STRING);
		// $stdDestAdr->mail = new SoapVar($client->Mail, XSD_STRING);
		$adresse = strtr($adresseLivraison->Adresse, "\r\n", "  ");
		$stdDestAdr->line2 = new SoapVar(substr($adresse, 0, 35), XSD_STRING);
		if(strlen($adresse)>35) $stdDestAdr->line3 = new SoapVar(substr($adresse, 35, 70), XSD_STRING);
		$stdDestAdr->countryCode = new SoapVar("FR", XSD_STRING);
		$stdDestAdr->city = new SoapVar($adresseLivraison->Ville, XSD_STRING);
		$stdDestAdr->postalCode = new SoapVar($adresseLivraison->CodePostal, XSD_STRING);


		$stdExp = new stdClass();
		// $stdExp->alert = null;
		$stdExpAdr = new stdClass();
		$stdExpAdr->line2 = new SoapVar(strtr($magasin->Adresse, "\r\n", "  "), XSD_STRING);
		$stdExpAdr->countryCode = new SoapVar("FR", XSD_STRING);
		$stdExpAdr->postalCode = new SoapVar($magasin->CodePostal, XSD_STRING);
		$stdExpAdr->city = new SoapVar($magasin->Ville, XSD_STRING);

		$stdDM = new stdClass();

		switch($this->Params['MODE']) {
			case "1" :
				$stdDM->DeliveryModeVO = new SoapVar("BRP", XSD_STRING);
				$stdParcel->regateCode = new SoapVar($bonLivraison->ChoixLivraisonId, XSD_INT);
			break;
			case "2" :
				$stdDM->DeliveryModeVO = new SoapVar("DOM", XSD_STRING);
			break;
			case "3" :
				$stdDM->DeliveryModeVO = new SoapVar("DOS", XSD_STRING);
			break;
			case "4" :
				$stdParcel->DeliveryModeVO = new SoapVar("RDV", XSD_STRING);
				if(empty($client->Portable)) die("Pour utiliser la livraison sur RDV, le client doit avoir donné un numéro de mobile !");
				$mobile = str_replace("+33", "0", $client->Portable);
				$mobile = str_replace(" ", "", $mobile);
				$mobile = str_replace(".", "", $mobile);
				$mobile = str_replace("-", "", $mobile);
				$stdDestAdr->mobileNumber = new SoapVar("RDV", XSD_STRING);
			break;
		}

		// $stdParcel->deliveryMode = new SoapVar($stdDM, SOAP_ENC_OBJECT);

		$stdExp->addressVO = new SoapVar($stdExpAdr, SOAP_ENC_OBJECT);
		$stdDest->addressVO = new SoapVar($stdDestAdr, SOAP_ENC_OBJECT);

		$stdLetter->service = new SoapVar($stdService, SOAP_ENC_OBJECT);
		$stdLetter->parcel = new SoapVar($stdParcel, SOAP_ENC_OBJECT);
		$stdLetter->dest = new SoapVar($stdDest, SOAP_ENC_OBJECT);
		$stdLetter->exp = new SoapVar($stdExp, SOAP_ENC_OBJECT);

		$params = new stdClass();
		$params->letter = new SoapVar($stdLetter, SOAP_ENC_OBJECT);
		$soapParam = new SoapParam($params, SOAP_ENC_OBJECT);

		// print_r($soapParam);

		// CALL
		$soapClient = new SoapClient($url);
		$result = $soapClient->getLetterColissimo( $soapParam );
		//$soapClient = new SoapClient($url);
		//$result = $soapClient->getLetter( $soapParam );

		var_dump($result); die;

		// FERMETURE CONNEXION
		unset($soapClient);
		*/

		$url = "https://ws.colissimo.fr/soap.shippingclp-return-ws-proxy/services/WSColiPosteLetterReturnService?wsdl";
		$url = "https://217.108.161.162/soap.shippingclp-return-ws-proxy/services/WSColiPosteLetterReturnService?wsdl";

		// Adresse DEST
		$adresseDest = strtr($adresseLivraison->Adresse, "\r\n", "  ");
		$stdDestAdr = new stdClass();
		$stdDestAdr->line2 = substr($adresseDest, 0, 35);
		$stdDestAdr->line3 = (strlen($adresseDest)>35) ? substr($adresseDest, 35, 70) : '';

		// Adresse EXP
		$adresseExp = strtr($magasin->Adresse, "\r\n", "  ");
		$stdExpAdr = new stdClass();
		$stdExpAdr->line2 = substr($adresseExp, 0, 35);
		$stdExpAdr->line3 = (strlen($adresseExp)>35) ? substr($adresseExp, 35, 70) : '';

		// PARAMS
		// $client = new SoapClient("http://217.108.161.162/soap.shippingclp-return-ws-proxy/services/WSColiPosteLetterReturnService?wsdl", array('trace' => 1, 'soap_version'  => SOAP_1_1));
		$soapClient = new SoapClient($url, array('trace' => 1, 'soap_version'  => SOAP_1_1));
        $params = array(
			"letter" => array(
				"contractNumber" => $this->Params['USERNAME'],
				"password" => $this->Params['PASSWORD'],
				"service" => array(
					"dateDeposite" => date('c'),
					"returnType" => 'CreatePDFFile',
					"serviceType" => 'SO',
					"commandNumber" => $bonLivraison->NumBL
				),
				"parcel" => array(
					"weight"=> $commande->Poids,
					"horsGabarit" => $this->isMecanisable( $commande )
				),
				"dest" => array(
					"entity" => array(
						"companyName" => $adresseLivraison->Civilite,
						"civility" => $adresseLivraison->Civilite,
						"Name" => $adresseLivraison->Nom,
						"Surname" => $adresseLivraison->Prenom
					),
					"address" => array(
						"line0" => $stdDestAdr->line2,
						"line1" => $stdDestAdr->line3,
						"line2" => '',
						"line3" => '',
						"countryCode" => 'FR',
						"city" => $adresseLivraison->Ville,
						"postalCode" => $adresseLivraison->CodePostal,
						"email" => $client->Mail
					),
					"codeBarForreference" => false
				),
				"exp" => array(
					"entity" => array(
						"civility" => "Société",
						"companyName" => "Société",
						"Name" => $this->Params['NOM'],
						"Surname" => $this->Params['NOM']
					),
					"address" => array(
						"line0" => $stdExpAdr->line2,
						"line1" => $stdExpAdr->line3,
						"line2" => '',
						"line3" => '',
						"countryCode" => 'FR',
						"city" => $magasin->Ville,
						"postalCode" => $magasin->CodePostal
					)
				)
			)
		);

		// CALL
		echo '<pre>';
		$result = $soapClient->getLetter($params);
		var_dump($result); die;

		// FERMETURE CONNEXION
		unset($soapClient);
	}

	private function isMecanisable( $commande ) {
		// Calcul volume et quantite totale
		$volume = 0;
		$quantite = 0;
		$lignes = $commande->getLignesCommande();
		foreach($lignes as $LC) {
			$quantite += $LC->Quantite;
			$volume += $LC->Largeur*$LC->Hauteur*$LC->Profondeur;
		}
		// Marge selon nb de produit
		$facteur = 1;
		if($quantite > 4) $facteur += 0.05;
		if($quantite > 9) $facteur += 0.05;
		$volume *= $facteur;
		// On récupère les dimensions APPROXIAMTIVES du colis (qu'on estime rectangulaire)
		$racine3 = pow($volume, 1/3);
		$longueur = 1.6 * $racine3;
		$hauteur = 0.7 * $racine3;
		$profondeur = 0.9 * $racine3;
		return ($longueur + $hauteur + $profondeur > 150) || ($longueur > 100);
	}

}