<?php

/*********************************************
*
* Interface pour plugin
* LivraisonStock / TypeLivraison
* Abtel
* 
*********************************************/

interface LivraisonStockTypeLivraisonPlugin {

	/**
	 * Le plugin doit connaitre les informations sur le type de livraison
	 * on fournit donc l'objet dès le constructeur
	 * @param	object	TypeLivraison KE
	 * @return	void
	 */
	public function setTypeLivraison( $typeLivraison );

	/**
	 * Retourne le tarif pour un type de livraison, une commande, une adresse particulière
	 * @param	object	Commande KE
	 * @param	object	Adresse Livraison KE
	 * @return	object	Tarif Livraison KE
	 */
	public function getTarif( $commande, $adresseLivraison );

	/**
	 * Retourne les différents choix que propose le mode de livraison
	 * @param	object	Commande KE
	 * @param	object	Adresse Livraison KE
	 * @return	Tableau [
					 [ Uid => ... , Libelle => ...  ]
					 [ Uid => ... , Libelle => ...  ]
				] (peut être vide)
	 */
	public function getChoix( $commande, $adresseLivraison );

	/**
	 * Retourne le descriptif d'un choix complémentaire à partir de son id unique
	 * @param	object	Commande KE
	 * @param	object	Adresse Livraison KE
	 * @param	string	Uid du choix
	 * @return	string	La description
	 */
	public function getChoixIntitule( $commande, $adresseLivraison, $Uid );

	/**
	 * Si VRAI, l'adresse livraison sera l'intitule du choix et non l'adresse de livraison standard
	 * @return	boolean
	 */
	public function isAdresseLivraisonAlternative();

	/**
	 * Met à jour des informations complémentaires dans le BL une fois le paiement confirmé
	 * @return void
	 */
	public function updateInfosBL( $bonLivraison );
}