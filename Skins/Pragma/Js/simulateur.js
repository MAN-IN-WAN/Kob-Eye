// Tranches d'imposition
var tranches = new Array(
	{'val':5875,'%':0},
	{'val':5845,'%':5.5},
	{'val':14310,'%':14},
	{'val':43753,'%':30},
	{'val':Infinity,'%':40}
)

/**
 * Ajuste une valeur si elle n'est pas dans les bornes
 * @param	Valeur minimum possible
 * @param	Valeur maximum possible
 * @param	Valeur donnée
 * @return	Valeur correcte
 */
function adjustValue(min, max, val) {
//alert (min + " " + max +" " + val);
//console.log(min + " " + max +" " + val);
	if(!(val>=min && val<=max)) {
		if(val>max) val=max;
		else val=min;
	}
	return val;
}

/**
 * Getters / Setters
 * On travaille uniquement avec le dispositif classique
 * @param	Value to set
 * @return	Value to get
 */
function getDispositif() { return 1 }

function getProfession() { return $('liberal').checked ? 1 : 0 }

function getImposition() { return parseFloat($('imposition_value').value) }
function setImposition(val) { $('imposition_value').value = val }

function getTMI() { return parseFloat($('tmi_value').value) }
function setTMI(val) { $('tmi_value').value = val }

function getNbParts() { return parseFloat($('nb_parts_value').value) }
function setNbParts(val) { $('nb_parts_value').value = val }

function getRevenu() { return parseFloat($('revenu_value').value) }
function setRevenu(val) { $('revenu_value').value = val }

function getRevenuFoncier() { return parseFloat($('revenu_foncier_value').value) }
function setRevenuFoncier(val) { $('revenu_foncier_value').value = val }

function getMontantInvestissement() { return parseFloat($('montant_investissement_value').value) }
function setMontantInvestissement(val) { $('montant_investissement_value').value = val }

function getDureeCredit() { return parseInt($('duree_credit_value').value) }
function setDureeCredit(val) { $('duree_credit_value').value = val }

function getTauxCredit() { return parseFloat($('taux_credit_value').value) }
function setTauxCredit(val) { $('taux_credit_value').value = val }

function getDureeSimulation() { return parseInt($('duree_simulation_value').value) }
function setDureeSimulation(val) { $('duree_simulation_value').value = val }

function getEconomieImpot() { return parseFloat($('economie_impot').innerHTML) }
function setEconomieImpot(val) { $('economie_impot').innerHTML = val }

function setCapitalConstitue(val) { $('capital_constitue').innerHTML = val }

function getEpargneMoyenne() { return parseFloat($('epargne_moyenne').innerHTML) }
function setEpargneMoyenne(val) { $('epargne_moyenne').innerHTML = val }

function setAssuranceVie(val) { $('assurance_vie').innerHTML = val }

function setLivretA(val) { $('livret_a').innerHTML = val; }

function setPEL(val) { $('pel').innerHTML = val }


/**
 * Retourne le pourcentage d'économies en fonction du dispositif
 * ainsi que de la durée de l'investissement
 * @return	Pourcentage
 */
function getPercentageReductionImpot() {
	var pourcentage = 0;
	switch(getDispositif()) {
		case 1: //Scellier classique
		case 2: //Scellier meublé
			pourcentage=0.25;
			break;
		case 4: //Scellier intermédiaire
			switch (getDureeSimulation()){
				case 9:
					pourcentage=0.25;
					break;
				case 12:
					pourcentage=0.31;
					break;
				case 15:
				case 18:
				case 20:
				case 25:
					pourcentage=0.37;
					break;
			};
			break;
		case 3: // Scellier outre mer
			switch (getDureeSimulation()){
				case 9: 
					pourcentage=0.4;
					break;
				case 12:
					pourcentage=0.46;
					break;
				case 15:
				case 18:
				case 20:
				case 25:
					pourcentage=0.52;
					break;
			};
			break;
	};
	return pourcentage;
}

/**
 * Retourne la mensualité
 * Formule : (capital*taux/12)/(1-(1+(taux/12))^(-durée)
 * @return	Mensualité
 */
function getMensualite() {
	var c = getMontantInvestissement();
	var t = getTauxCredit()/100; //On souhaite le taux en tant que coefficient (pour 3% on veut 0.03 et non 3 !)
	var d = getDureeCredit()*12;
	return (c*t/12)/ (1-Math.pow((1+(t/12)),(-d)));
}

/**
 * Retourne le déficit Foncier
 * @return	Déficit	
 */
function getDeficitFoncier() {
	var abattement = 0;
	var loyer = getLoyer()*12; //loyer annuel
	if(getDispositif() == 4) abattement = loyer*0.3 //si scellier intermédiaire abattement annuel
	var duree = getDureeCredit();
	var interets = (12*duree*getMensualite() - getMontantInvestissement())/duree; //interets annuels
	return loyer-interets-abattement;
}

/**
 * Retourne le loyer mensuel
 * @return	Loyer
 */
function getLoyer() {
	var rentabilite = 0;
	switch (getDispositif()) {
		case 1: //scellier classique   
			rentabilite = 0.032;
			break;
		case 4: //scellier intermédiaire
		case 3: //scellier outre-mer 
			rentabilite = 0.03;
			break;
		case 2: //scellier meublé
			rentabilite = 0.042;
			break;
	}
	return rentabilite*getMontantInvestissement()/12;	
}

/**
 * Retourne le Montant reporté
 * @return	Montant Report
 */
function getMontantReport() {
	var reduc_annuelle = getPercentageReductionImpot()*getMontantInvestissement()/getDureeSimulation();
	var imposition = getImposition();
	if(reduc_annuelle > imposition) { //On regarde si le report peut se faire (réduction d'impôts annuelle supérieure à l'imposition annuelle)
		if(6*(reduc_annuelle - imposition) > 6*imposition) //le report est limité à 6*l'imposition annuelle
			return 6*imposition;
		return 6*(reduc_annuelle - imposition);
	}
	else return 0;
}

/**
 * Retourne le placement Assurance vie
 * @return	Epargne
 */
function getPlacementAssuranceVie() {
	var epargne = getEpargneMoyenne();
	if(epargne<0)
		return 0;
	var epargne_moyenne = epargne;
	var interets;
	var taux_mensuel = Math.pow(1.045,(1/12))-1;
	for(var i=0; i<getDureeSimulation(); i++)
	{
		interets = 0;
		for(var j=0; j<12; j++)
		{
			interets += epargne*taux_mensuel;
			epargne += epargne_moyenne;
		}//fin d'année
		interets *= 0.879; //prélèvements sociaux de 12.1% en décembre 
		epargne += interets;             
	}
	return epargne;
}

/**
 * Retourne le placement livret A
 * @return	Placement
 */
function getPlacementLivretA() {
	var epargne = getEpargneMoyenne()/2;
	if(epargne<0)
		return 0;
	var epargne_moyenne = epargne;
	var interets;
	var taux_quinzaine = Math.pow(1.0125,(1/24))-1;
	for(var i=0; i<getDureeSimulation(); i++)
	{
		interets = 0;
		for(var j=0; j<24; j++)
		{
			interets += epargne*taux_quinzaine;
			epargne += epargne_moyenne;
		}//fin d'année
		epargne += interets;              
	}
	return epargne;            
}

/**
 * Retourne le placement PEL
 * @return	Placement
 */
function getPlacementPEL() {
	var epargne = getEpargneMoyenne();
	if(epargne<45) //Le versement annuel minimum sur un PEL est de 540€ (soit 45€ / mois) 
	return 0;
	var epargne_moyenne = epargne;
	var interets;
	var taux_mensuel = Math.pow(1.025,(1/12))-1;
	for(var i=0; i<getDureeSimulation(); i++)
	{
		interets = 0;
		for(var j=0; j<12; j++)
		{
			interets += epargne*taux_mensuel;
			epargne += epargne_moyenne;
		}//fin d'année
		interets *= 0.879; //prélèvements sociaux de 12.1% en décembre 
		epargne += interets;             
	}
	return epargne;      
}

/**
 * Retourne l'imposition
 * @return	Imposition
 */
function imposition() {
	var min = 0;
	var max = 8833;
	var nbParts = getNbParts();
	var revenu = getRevenu();
	if(getProfession()==0) revenu *= 0.9; //abattement de 10% sur les revenus salariés
	revenu += getRevenuFoncier();
	revenu /= nbParts;
	var impots = 0;
	for(var i=0;i<tranches.length;i++)
	{
		if(revenu - tranches[i]['val'] <= 0)
		{
			impots += revenu*(tranches[i]['%']/100);
			setTMI(tranches[i]['%']);
			setImposition(Math.round(impots*nbParts));
			return;
		}
		else
		{
			impots+=tranches[i]['val']*(tranches[i]['%']/100);
			revenu-=tranches[i]['val'];
		}
	}
}
      
/**
 * Retourne l'économie d'impot
 * @return	Economie d'Impot
 */
function economieImpot() {
	var reduction = getMontantInvestissement()*getPercentageReductionImpot(); //réduction = montant investissement * % d'économie d'impôt (dans la limite des impôts payés)
	if(reduction > getImposition()*getDureeSimulation())
		  reduction = getImposition()*getDureeSimulation(); //on limite la réduction d'impôt à l'imposition annuelle multipliée par la durée de l'investissement
	var dispositif = getDispositif();
	if (dispositif != 2) //si on est pas en scellier meublé
	{        
		  var deficitFoncier = getDeficitFoncier();
		  if(deficitFoncier<0) { //s'il y a un déficit foncier
				deficitFoncier = (-1)*getDureeSimulation()*deficitFoncier;
				if(getRevenuFoncier()<=0) { //s'il n'y a pas de revenu foncier
					reduction += deficitFoncier; //on ajoute le déficit foncier à l'économie d'impôt
					if(reduction > getMontantInvestissement()*getPercentageReductionImpot()) reduction = getMontantInvestissement()*getPercentageReductionImpot(); //on limite la réduction d'impôt à l'imposition annuelle multipliée par la durée de l'investissement
					$('texte_csgcrds').innerHTML = 0;
				}
				else {
					  var csgcrds = 0.121*deficitFoncier; //on économise le csg/crds à hauteur de 12.1% des revenus fonciers totaux
					  $('texte_csgcrds').innerHTML = Math.round(csgcrds);
					  reduction += csgcrds;
				}                        
		  }
		  else $('texte_csgcrds').innerHTML = 0;
	}
	else $('texte_csgcrds').innerHTML = 0;
	reduction += getMontantReport();
	setEconomieImpot(Math.round(reduction));
}

/**
 * Retourne le capital constitue
 * @return	Capital constitue
 */
function capitalConstitue() {
	var capital_restant = getMontantInvestissement();
	var mensualite = getMensualite();
	var taux_mensuel = Math.pow(1+getTauxCredit()/100,1/12)-1; //getTauxCredit()/1200;
	for(var i=0; i<12*getDureeSimulation(); i++) capital_restant -= (mensualite - capital_restant*taux_mensuel);
	if(capital_restant < 0) capital_restant = 0;
	setCapitalConstitue(Math.round(getMontantInvestissement()-capital_restant));
}

/**
 * Retourne l'epargne
 * @return	Epargne
 */
function epargne() {
	setEpargneMoyenne(Math.round(getMensualite() - getLoyer() - getEconomieImpot()/(getDureeSimulation()*12))); //epargne = mensualité - loyer - économie d'impôts (arrondi au centième : round(val*100)/100)
	setAssuranceVie(Math.round(getPlacementAssuranceVie()));
	setLivretA(Math.round(getPlacementLivretA()));
	setPEL(Math.round(getPlacementPEL()));
}

/**
 * Met à jour le graphique
 * -> Utilise l'API google charts
 * @return	void
 */
function graphique() {
	var investissement = getMontantInvestissement();
	 
	var etat = getEconomieImpot()/investissement*100;
	if(etat > 100) etat = 100; //cas où l'état prend en charge l'intégralité de l'investissement à travers l'économie d'impôts.
	var locataire = (getLoyer()*12*getDureeSimulation()*100)/investissement;
	if(locataire > 100-etat) locataire = 100-etat; //cas où le locataire rembourse plus que le pourcentage restant.
	var vous = Math.round((100-etat-locataire)*100)/100;
	etat = Math.round(etat*100)/100;
	locataire = Math.round(locataire*100)/100;
	$('graph').innerHTML = '<img src="http://chart.apis.google.com/chart?cht=p3&chd=t:'+etat+','+locataire+','+vous+'&chs=600x150&chco=0070AB|0070AB|525252&chl=L Etat (Reduction de '+etat+' %)|Votre locataire ('+locataire+'%)|Vous ('+vous+'%)" />';
}

function graphique_simulateurs() {
	var investissement = getMontantInvestissement();
	 
	var etat = getEconomieImpot()/investissement*100;
	if(etat > 100) etat = 100; //cas où l'état prend en charge l'intégralité de l'investissement à travers l'économie d'impôts.
	var locataire = (getLoyer()*12*getDureeSimulation()*100)/investissement;
	if(locataire > 100-etat) locataire = 100-etat; //cas où le locataire rembourse plus que le pourcentage restant.
	var vous = Math.round((100-etat-locataire)*100)/100;
	etat = Math.round(etat*100)/100;
	locataire = Math.round(locataire*100)/100;
	$('graph').innerHTML = '<img src="http://chart.apis.google.com/chart?cht=p&chd=t:'+etat+','+locataire+','+vous+'&chs=425x150&chco=0070AB|0070AB|525252&chl=L Etat (Reduction '+etat+' %)|Votre locataire ('+locataire+'%)|Vous ('+vous+'%)" />';
}

/**
 * Recalcul général de tous les champs
 * @return	void
 */
function calcul() {
	$('texte_duree').innerHTML = getDureeSimulation();
	imposition();
	economieImpot();
	capitalConstitue();
	epargne();
	graphique();
}
function calcul_simulateurs() {
	$('texte_duree').innerHTML = getDureeSimulation();
	imposition();
	economieImpot();
	capitalConstitue();
	epargne();
	graphique_simulateurs();
}



