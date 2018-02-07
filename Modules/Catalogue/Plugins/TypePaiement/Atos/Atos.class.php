<?php

/*********************************************
*
* Interface pour plugin
* Boutique / TypePaiement
* Abtel
* 
*********************************************/


class BoutiqueTypePaiementAtos extends Plugin implements BoutiqueTypePaiementPlugin {

	public function __construct() {
		define("ATOS_LIB_DIR", dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))."/Class/Lib/Atos");
	}

	/**
	 * LISTE FICHIERS PARAMETRES
	 * -> A paramatrer !
	 */
	private function generatePathFile( $id ) {
		$str = "DEBUG!NO!\r\n";
		$str .= "D_LOGO!/Class/Lib/Logo/!\r\n";
		$str .= "F_DEFAULT!".ATOS_LIB_DIR."/parmcom!\r\n";
		$str .= "F_PARAM!".ATOS_LIB_DIR."/parmcom!\r\n";
		$str .= "F_CERTIFICATE!".ATOS_LIB_DIR."/certif!\r\n";
		file_put_contents(ATOS_LIB_DIR."/pathfile", $str);
		return ATOS_LIB_DIR."/pathfile";
	}

	/**
	 * FICHIER DE PARAMETRES COMMERCANT
	 * -> A paramatrer !
	 */
	private function generateParamFile( $id ) {
		$str = "ADVERT!merchant.gif!\r\n";
		$str .= "AUTO_RESPONSE_URL!http://".$_SERVER['HTTP_HOST']."/Boutique/Commande/Etape4s!\r\n";
		$str .= "CANCEL_URL!http://".$_SERVER['HTTP_HOST']."/Boutique/Commande/Etape5!\r\n";
		$str .= "RETURN_URL!http://".$_SERVER['HTTP_HOST']."/Boutique/Commande/Etape5!\r\n";
		file_put_contents(ATOS_LIB_DIR."/parmcom.$id", $str);
		return ATOS_LIB_DIR."/parmcom";
	}
	
	/**
	 * FICHIER DE PARAMETRES CYBERPLUS
	 * -> A paramatrer !
	 */
	private function generateTransactionFile( $id ) {
		$str = "BGCOLOR!ffffff!\r\n";
		$str .= "BLOCK_ALIGN!center!\r\n";
		$str .= "BLOCK_ORDER!1,2,3,4,5,6,7,8!\r\n";
		$str .= "CONDITION!SSL!\r\n";
		$str .= "CURRENCY!978!\r\n";
		$str .= "HEADER_FLAG!yes!\r\n";
		$str .= "LANGUAGE!fr!\r\n";
		$str .= "LOGO!cyber.gif!\r\n";
		$str .= "LOGO2!bp.gif!\r\n";
		$str .= "MERCHANT_COUNTRY!fr!\r\n";
		$str .= "MERCHANT_LANGUAGE!fr!\r\n";
		$str .= "PAYMENT_MEANS!CB,2,VISA,2,MASTERCARD,2!\r\n";
		$str .= "TARGET!_top!\r\n";
		$str .= "TEXTCOLOR!000000!\r\n";
		file_put_contents(ATOS_LIB_DIR."/parmcom", $str);
		return ATOS_LIB_DIR."/parmcom";
	}

	/**
	 * CERTIFICAT
	 * -> A récupérer !
	 */
	private function generateCertifFile( $id ) {
		$str = $this->Params["CERTIFICAT"];
		file_put_contents(ATOS_LIB_DIR."/certif.fr.$id", $str);
	}


	/**
	 * Récupère le code complet qui sera affiché sur l'étape 4b
	 * @param	object	Objet Kob-Eye de paiement
	 * @return	string
	 */
	public function getCodeHTML( $paiement ) {
		global $_POST, $_SERVER;

		// Config boutique (à dynamiser)
		$merchant_id = $this->Params["MERCHANT_ID"];

		// Génération fichiers -> comment ne pas le faire à chaque fois ?
		$pathFile = 	$this->generatePathFile($merchant_id);
		$paramFile =	$this->generateParamFile($merchant_id);
		$transacFile =	$this->generateTransactionFile($merchant_id);
		$certifFile =	$this->generateCertifFile($merchant_id);

		/***************************************************
		*				EXECUTABLE REQUEST
		***************************************************/

		// Params
		$language = 'french';
     	$amount = sprintf("%03d", $paiement->Montant );

		// Commande
		$command  = dirname(__FILE__).'/request';
		$command .= " pathfile=" . $pathFile;
		$command .= " merchant_id=" . $merchant_id;
		$command .= " amount=" . round($paiement->Montant * 100);
		$command .= " currency_code=978";
		$command .= " language=fr";
		$command .= " normal_return_url=http://".$_SERVER['HTTP_HOST']."/Boutique/Commande/Etape5";
		$command .= " cancel_return_url=http://".$_SERVER['HTTP_HOST']."/Boutique/Commande/Etape5";
		$command .= " automatic_response_url=http://".$_SERVER['HTTP_HOST']."/Boutique/Commande/Etape4s";
		$command .= " customer_id=" . $paiement->Id; // On passe l'ID du paiement
		$command .= " customer_ip_address=" . $_SERVER['REMOTE_ADDR'];
		$sips_result = shell_exec("$command 2>&1");

		$sips = array();
		$sips_values     = explode ("!", $sips_result);
		$sips['code']    = $sips_values[1];
		$sips['error']   = $sips_values[2];
		$sips['message'] = $sips_values[3];
		$sips['command'] = $command;
		$sips['output']  = $sips_result;
		
		if (!isset($sips['code'])) {
			$sips['code']   = -1;
			$sips['error']  = $sips_result;
		}
		
		if ($sips['code'] != 0) {
			$sips['amount'] = $amount;
			$sips['lang']   = $language;
			$sips['id']     = $_COOKIE[PHP_SESSION_NAME];
		}

		/***************************************************
		*				RETOUR FORMULAIRE
		***************************************************/

		if ($sips['error']) {
			$this->error   = 1;
			$this->message = "Erreur Atos" . ': ' . $sips['command'] . '<br>' . $sips['error'];
		} else {
			$regs = array();
			$this->error   = 0;
			$this->message = $sips['message'];
			if (eregi('<form [^>]*action="([^"]*)"[^>]*>(.*)</form>', $sips['message'], $regs)) {
				$this->message = $regs[2];
				$this->form_action_url = $regs[1];
				$this->form_submit     = '';
				return $sips['message'];
			} else {
				$this->error = 1;
				$this->message = "Erreur Atos";
			}
		}
		return $this->message;
	}


	/**
	 * Analyse la réponse serveur et renvoi le code retour normalisé pour le paiement
	 * 0 : en attente
	 * 1 : paiement autorisé
	 * 2 : paiement refusé
	 * ainsi que la référence du paiement
	 * @param	object	Objet Kob-Eye de paiement
	 * @param	object	Objet Kob-Eye de commande
	 * @return	array ( 'etat', 'ref' )
	 */
	public function serveurAutoResponse( $paiement, $commande ){
		if(isset($_POST['DATA']) and !empty($_POST['DATA'])) {
			$message = "message=" . escapeshellcmd($_POST['DATA']);
			$path_bin  = dirname(__FILE__).'/response';
			$pathfile = "pathfile=" . $this->generatePathFile($this->Params["MERCHANT_ID"]);
			$result = exec("$path_bin $pathfile $message");
			$tableau = explode ("!", $result);
			$etat = ($tableau[18] == '00') ? 1 : 2;
			return array(
				'etat' => $etat,
				'ref' => $tableau[13]
			);
		}
		return false;
	}

	public function retrouvePaiementEtape4s() {
		if(isset($_POST['DATA']) and !empty($_POST['DATA'])) {
			$message = "message=" . escapeshellcmd($_POST['DATA']);
			$path_bin  = dirname(__FILE__).'/response';
			$pathfile = "pathfile=" . $this->generatePathFile($this->Params["MERCHANT_ID"]);
			$result = exec("$path_bin $pathfile $message");
			$tableau = explode ("!", $result);
			return round($tableau[26]);
		}
		return false;
	}

	/**
	** gestion du bloc qui apparaitra à l'étape 5 en fonction du type de paiement choisi
	 * @param	object	Objet Kob-Eye de paiement
	 * @param	object	Objet Kob-Eye de commande
	 * @return	string
	*/
	public function affichageEtape5( $paiement, $commande ){
		if($commande->Paye) return 'Votre commande a été enregistrée sous le numéro '. $commande->RefCommande;
		else return 'Une erreur est survenue lors du paiement de la commande '. $commande->RefCommande . '<br /> Vous pouvez contacter le support via ce <a href="/Contact">formulaire</a> en rappelant cette référence.';
	}

}