<?php

class Paiement extends genericClass {

	/**
	 * Enregistrement d'un paiement
	 * -> Check référence
	 * @return	void
	 */
	public function Save() {
		parent::Save();
	}

	public function initFromPaypal() {
		/*** transaction_subject=&payment_date=10:54:35 Jan 12, 2011 PST&txn_type=web_accept&last_name=User&residence_country=US&item_name=&payment_gross=&mc_currency=EUR&business=enguer_1292239145_biz@gmail.com&payment_type=instant&protection_eligibility=Ineligible&verify_sign=AiPC9BjkCyDFQXbSkoZcgqH3hpacA6MpTVt8TktLS0gyxDL1WG87veBo&payer_status=verified&test_ipn=1&tax=0.00&payer_email=enguer_1294852454_per@gmail.com&txn_id=2ES94002B0727171D&quantity=0&receiver_email=enguer_1292239145_biz@gmail.com&first_name=Test&payer_id=ZZH5MNJJAHU68&receiver_id=LM6LSTZXTCHSU&item_number=&payment_status=Completed&payment_fee=&mc_fee=0.36&mc_gross=2.85&custom=&charset=windows-1252&notify_version=3.0  **/
	}

	/**
	* Verification du paiement en enregistrement du paiement
	* @param r tableau de reponse paypal
	* @return void
	*/
	public function checkPaiement($r){
		$this->Detail.="----------------------------------\r\n";
		$this->Detail.=print_r($r,true);
		if ($r->status=="COMPLETED"){
			//recuperation de la commande associee pour la completer
			$lignes = $this->storproc('Boutique/Commande/Paiement/' . $this->Id,false,0,1);
			if (is_array($lignes)&&is_array($lignes[0]))
				$C = genericClass::createInstance('Boutique',$lignes[0]);
			$C->Paye=true;
			$C->Save();
		}
		$this->Save();
		return $C;
	}

	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return $GLOBALS['Systeme']->Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}

}