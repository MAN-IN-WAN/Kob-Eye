<?php

/*********************************************
*
* Interface pour plugin
* Catalogue / TypePaiement
* Abtel
* 
*********************************************/


interface CatalogueTypePaiementPlugin {

	/**
	 * Récupère le code complet qui sera affiché sur l'étape 4b
	 * @param	object	Objet Kob-Eye de paiement
	 * @return	string
	 */
	public function getCodeHTML( $paiement );

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
	public function serveurAutoResponse( $paiement, $commande );

	/**
	** gestion du bloc qui apparaitra à l'étape 5 en fonction du type de paiement choisi
	 * @param	object	Objet Kob-Eye de paiement
	 * @param	object	Objet Kob-Eye de commande
	 * @return	string
	*/
	public function affichageEtape5( $paiement, $commande );

	/**
	 * Détermine si c'est ce plugin qu'on utilise suivant les données POST et GET
	 * Si c'est le cas retourne l'ID du paiement correspondant
	 * @return	int
	 */
	public function retrouvePaiementEtape4s() ;

}