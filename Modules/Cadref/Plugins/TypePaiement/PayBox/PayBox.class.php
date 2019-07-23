<?php

/*********************************************
*
* Module de paiement
* Crédit Agricole
* Abtel
* 
*********************************************/

require_once( dirname(dirname(__FILE__)).'/TypePaiement.interface.php' );

class CadrefTypePaiementPayBox extends Plugin implements CadrefTypePaiementPlugin {

	/**
	* initStatePaiement
	* Initiliase le paiement avec ses propriétés particulières
	*/
	public function initStatePaiement() {
		return 0;
	}


	public function __construct() {
		if (!defined("PAYBOX_LIB_DIR"))define("PAYBOX_LIB_DIR", dirname(__FILE__));
	}

	/**
	 * getCodeHTML
	 * return the html code generated by the payment module.
	 * $paiement = Cadref/Paiement Objectclass
	 **/
	public function getCodeHTML( $paiement ) {
		// Params
		//mode d'appel
		     $PBX_MODE        = '4';    //pour lancement paiement par exécution
		     //$PBX_MODE        = '1';    //pour lancement paiement par URL
		//identification
		     $PBX_SITE        = $this->Params['IDSITE'];
		     $PBX_RANG        = $this->Params['RANG'];
		     $PBX_IDENTIFIANT = $this->Params['IDENTIFIANT'];
		//gestion de la page de connection : paramétrage "invisible"
		     $PBX_WAIT        = '0';
		     $PBX_TXT         = " ";
		     $PBX_BOUTPI      = "nul";
		     $PBX_BKGD        = "white";
		//informations paiement (appel)
		     $PBX_TOTAL       = round($paiement->Montant * 100);
		     $PBX_DEVISE      = '978';
		     $PBX_CMD         = sprintf("%06d", $paiement->Id);
		     $PBX_PORTEUR     = $GLOBALS["Systeme"]->Conf->get("GENERAL::INFO::ADMIN_MAIL");
		//informations nécessaires aux traitements (réponse)
		     $PBX_RETOUR      = "auto:A\;amount:M\;ident:R\;trans:T";
		     $PBX_EFFECTUE    = "https://".$_SERVER['HTTP_HOST']."/".Sys::getMenu('Cadref/Paiement/Etape5');
		     $PBX_REFUSE      = "https://".$_SERVER['HTTP_HOST']."/".Sys::getMenu('Cadref/Paiement/Etape5');
		     $PBX_ANNULE      = "https://".$_SERVER['HTTP_HOST']."/".Sys::getMenu('Cadref/Paiement/Etape5');
		     $PBX_REPONDRE_A  = "https://".$_SERVER['HTTP_HOST']."/".Sys::getMenu('Cadref/Paiement/Etape4s');
		//page en cas d'erreur
		     $PBX_ERREUR      = "https://".$_SERVER['HTTP_HOST']."/".Sys::getMenu('Cadref/Paiement/Etape5');
		//date
		     $PBX_TIME	      =  date("c");
		
		//construction de la chaîne de paramètres
		     $PBX             = "PBX_MODE=$PBX_MODE PBX_SITE=$PBX_SITE PBX_RANG=$PBX_RANG PBX_IDENTIFIANT=$PBX_IDENTIFIANT PBX_WAIT=$PBX_WAIT PBX_TXT=$PBX_TXT PBX_BOUTPI=$PBX_BOUTPI PBX_BKGD=$PBX_BKGD PBX_TOTAL=$PBX_TOTAL PBX_DEVISE=$PBX_DEVISE PBX_CMD=$PBX_CMD PBX_PORTEUR=$PBX_PORTEUR PBX_EFFECTUE=$PBX_EFFECTUE PBX_REFUSE=$PBX_REFUSE PBX_ANNULE=$PBX_ANNULE PBX_ERREUR=$PBX_ERREUR PBX_RETOUR=$PBX_RETOUR PBX_REPONDRE_A=$PBX_REPONDRE_A";
		
		/***** DEPRECATED *****/
		//lancement paiement par exécution
		/*ob_clean();
		$html = shell_exec( PAYBOX_LIB_DIR."/modulev2.cgi $PBX" );
		$html = preg_replace("#Content-type.*#","",$html);
		$html = preg_replace("#Cache-Control.*#","",$html);
		$html = preg_replace("#Pragma.*#","",$html);
		echo $html;
		exit();*/
		//return shell_exec( PAYBOX_LIB_DIR."/modulev2.cgi $PBX" );
		
		// Si la clé est en ASCII, On la transforme en binaire
		$binKey = pack("H*", $this->Params['KEY']);
	
		//on génère la clef HMAC
		$hmac = strtoupper(hash_hmac('sha512', $msg, $binKey));
		
		//on renvoie le formulaire
		return '
		<form method="POST" onload="this." action="https://preprod-tpeweb.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi">
			<input type="hidden" name="PBX_SITE" value="'.$PBX_SITE.'">
			<input type="hidden" name="PBX_RANG" value="'.$PBX_RANG.'">
			<input type="hidden" name="PBX_IDENTIFIANT" value="'.$PBX_IDENTIFIANT.'">
			<input type="hidden" name="PBX_TOTAL" value="'.$PBX_TOTAL.'">
			<input type="hidden" name="PBX_DEVISE" value="'.$PBX_DEVISE.'">
			<input type="hidden" name="PBX_CMD" value="'.$PBX_CMD.'">
			<input type="hidden" name="PBX_PORTEUR" value="'.$PBX_PORTEUR.'">
			<input type="hidden" name="PBX_RETOUR" value="'.$PBX_RETOUR.'">
			<input type="hidden" name="$PBX_REPONDRE_A" value="'.$PBX_REPONDRE_A.'">
			<input type="hidden" name="PBX_HASH" value="SHA512">
			<input type="hidden" name="PBX_TIME" value="'.$PBX_TIME.'">
			<input type="hidden" name="PBX_HMAC" value="'.$hmac.'">
			<input type="submit" value="Envoyer">
		</form>
		';
	}

	public function serveurAutoResponse( $paiement, $commande ) {
		// Vérification signature
		$signature = sha1(
			$_POST['version'] . "+" . $_POST['site_id'] . "+" . $_POST['ctx_mode'] . "+" . $_POST['trans_id'] . "+" . $_POST['trans_date'] . "+" . 
			$_POST['validation_mode'] . "+" . $_POST['capture_delay'] . "+" . $_POST['payment_config'] . "+" . $_POST['card_brand'] ."+" . 
			$_POST['card_number'] . "+" . $_POST['amount'] . "+" . $_POST['currency'] ."+" . $_POST['auth_mode'] ."+" . $_POST['auth_result'] ."+" .
			$_POST['auth_number'] ."+" . $_POST['warranty_result'] ."+" . $_POST['payment_certificate'] ."+" . $_POST['result'] ."+" . $_POST['hash'] . "+" . $this->Params["CERTIFICAT"]
		);
		if($signature != $_POST['signature']) {
			return false;
		}

		// Retourne le code d'état du paiement
		$etat = ($_POST['result'] == '00') ? 1 : 2;
		return array(
			'etat' => $etat,
			'ref' => $_POST['auth_number']
		);
	}

	public function retrouvePaiementEtape4s() {
		if(isset($_POST['trans_id']) and !empty($_POST['trans_id'])) return round($_POST['trans_id']);
		return false;
	}

	public function affichageEtape5( $paiement, $commande ) {
		if($commande->Paye) return 'Votre inscription a été enregistrée sous le numéro '. $commande->RefCommande;
		else return 'Une erreur est survenue lors du paiement de la commande '. $commande->RefCommande . '<br /> Vous pouvez contacter le support via ce <a href="/Contact">formulaire</a> en rappelant cette référence.';
	}

}