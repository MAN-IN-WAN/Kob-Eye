//////////////////////////////////// Fonctions communes //////////////////////////////////
function getMontant(montant) {
	if (montant.value!="") {
		return Math.round(parseFloat(montant.value*100))/100;
	}
	return 0;
}

function verifMontant(montant) {
	if (montant.value=="") {
		return true;
	}

	floatValue = parseFloat(montant.value);
	
	if ( isNaN(floatValue) ) {
		return false;
  } else {
  	return true;
  }
}

function modifValeur(obj, valeur) {
	if (verifMontant(obj)) {
		calcul = Math.round(100*(getMontant(obj) + valeur))/100;
		if (calcul<0) {
			obj.value = 0;
		} else {
			obj.value = calcul;
		}
	}
	obj.onchange();
}

///////////////////////////////////////////////////////////////////////////////////////////
function verifMontantBien() {
	// Vérification du contenu des champs
	formBien = document.formbien;
	
	ok = true;
	
	ok = ok && verifMontant(formBien.somme_finance);
	ok = ok && verifMontant(formBien.mensualite);
	ok = ok && verifMontant(formBien.duree);
	ok = ok && verifMontant(formBien.taux);
			
	if (ok == false ) {
		formBien.cout_emprunt.value = "Erreur";	
		return false;
	}
	return true;
}

function calculSommeFinancer(form) {
	if (verifMontant(form.prix_bien) && verifMontant(form.apport_perso)) {
		form.somme_finance.value = Math.round(100*(getMontant(form.prix_bien) - getMontant(form.apport_perso)))/100;
	} else {
		form.somme_finance.value = "Erreur";
	}
}

function changeMensualiteBien(form) {
	calculSommeFinancer(form);
	if (verifMontantBien() == true ) {
		taux = getMontant(form.taux);
		t = taux / 1200;
		mensualite = getMontant(form.mensualite);
		somme = getMontant(form.somme_finance);
		duree = Math.round(Math.log(-1/( ((t*somme)/(mensualite)) -1))/Math.log(1+t));
		
		if (isFinite(duree)) {
			form.duree.value = Math.floor(duree/12);
			form.cout_emprunt.value = Math.round(100*(mensualite * duree) - somme)/100;
		} else {
			form.cout_emprunt.value = "Erreur";
		}
	}	
}

function changeDureeBien(form) {
	calculSommeFinancer(form);
	if (verifMontantBien() == true ) {
		taux = getMontant(form.taux);
		t = taux / 1200;
		duree = getMontant(form.duree);
		somme = getMontant(form.somme_finance);
		mensualite = Math.round(100 * somme*(t/(1-(1/Math.pow(1+t,duree*12))))) / 100;
		if (isFinite(mensualite)) {
			form.mensualite.value = mensualite;
			form.cout_emprunt.value = Math.round(100 * ((mensualite * (duree*12)) - somme))/100;	
		} else {
			form.cout_emprunt.value = "Erreur";
		}
	}
}

function calculBien(form) {
	changeDureeBien(form);
}

///////////////////////////////////////////////////////////////////////////////////////

function verifMontantLoc() {
	// Vérification du contenu des champs
	formLoc = document.formlocataire;
	
	ok = true;
	
	ok = ok && verifMontant(formLoc.mensualite);
	ok = ok && verifMontant(formLoc.duree);
	ok = ok && verifMontant(formLoc.taux);
			
	if (ok == false ) {
		formLoc.cout_emprunt.value = "Erreur";	
		return false;
	}
	return true;
}

function calculLoc(form) {
	
	if (verifMontantLoc() == true ) {
		taux = getMontant(form.taux);
		t = taux / 1200;
		mensualite_ori = getMontant(form.mensualite);
		duree = getMontant(form.duree)*12;
		
		somme_res = Math.round(100 * mensualite_ori /(t/(1-(1/Math.pow(1+t,duree)))) )/100;
		
		if (isFinite(somme_res)) {
			total = duree * mensualite_ori;
			form.emprunt.value = somme_res;
			
			form.cout_emprunt.value = Math.round(100*(total - somme_res))/100;
		} else {
			form.cout_emprunt.value = "Erreur";
		}
	}	
}




