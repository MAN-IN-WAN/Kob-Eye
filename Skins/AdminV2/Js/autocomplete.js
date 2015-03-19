/**
	* Transforme un champ en autoComplete Field
	* @param	ID du champ
	* @param	Query à interroger
	* @param	Valeur initiale à inscrire - par défaut 0
	* @param	Champ à mémoriser dans la base - par défaut 'Id'
	* @param	Champ à afficher - par défaut 'Id'
	* @param	Texte initial à inscrire (si initialValue == -1 ou null) - par défaut ''
	* @param	Champ complémentaire à afficher - par défaut ''
	* @param	Présentation du champ ( %c1% est le champ #1 %c2% est le champ numéro 2 ) - par défaut '%c1%'
	* @param	Nombre de résultats max à afficher - par défaut 10
	* @return	void
	*/
function autoCompleteField( field, query, initialValue, valueField, textField, initialText, textField2, textPatern, nbResult ) {

	// Requete d'initialisation ?
	var firstReq = true;

	// Valeurs par défaut
	if(initialValue == null ||initialValue == '') {
		initialValue = '';
		firstReq = false;
	}
	if(valueField == null) valueField = 'Id';
	if(textField == null) textField = 'Id';
	if(initialText == null) initialText = '';
	if(textField2 == null) textField2 = '';
	if(textPatern == null) textPatern = '%c1%';
	if(nbResult == null) nbResult = 10;

	// Vérification du champ
	var f = $(field);
	if(f == null) return autoCompleteError('Erreur autoCompleteField : Le champ avec id="' + field + '" est introuvable.');

	// Création du champ invisible
	var i = new Element('input', {
		type: 'hidden',
		name: f.get('name'),
		id: field + '_ac_hidden',
		value: initialValue,
		styles: {
			'width': '50px'
		}
	}).inject(f, 'after');

	// On enleve le name du champ
	f.set('name', '');

	// Affichage de la value "visuelle" initiale
	if(firstReq) {
		f.value = 'Chargement...';
		var r = new Request.JSON({
			onSuccess: function( j ) {
				if(j.length > 0) {
					var res = textPatern;
					res = res.replace('%c1%', j[0].TextField);
					res = res.replace('%c2%', j[0].TextField2);
					f.value = res;
				}
				else f.value = '';
			},
			onComplete: function( a ) {
				if(a == undefined) {
					autoCompleteError('Le fichier ACSearch.md ne doit pas être présent dans la skin, dans le dossier '+query+'.');
					f.value = '';
				}
			}
		}).post('/'+query+'/ACSearch.htm?q='+query+'&v='+initialValue+'&tf='+textField+'&tf2='+textField2+'&init='+1);
	}
	else f.value = initialText;

	// Evenement key down / up
	var timeout = 0;
	f.addEvent('keydown', function(e) {
		var code = e.code;
		if(code == 38) changeActiveProposal(f,1);
		if(code == 40) changeActiveProposal(f,-1);
		if(code == 13) {
			e.preventDefault();
			changeActiveProposal(f,0);
		}
		if(code != 38 && code != 40 && code != 13) {
			clearTimeout(timeout);
			timeout = setTimeout(function() {
				// Création / Récupération de la div de propositions
				var parent = null;
				var rel = $$('div.ACRelative')
				if(rel.length > 0) parent = rel[0];
				if(parent == null) parent = document.body;
				var dP = $('proposals_ac');
				if(dP == null) dP = new Element('div', {'id':'proposals_ac'}).inject(parent);
				dP.innerHTML = 'Chargement...';
				// Vide, on masque tout
				if(f.value.trim() == '') { 
					i.value ='';
					return dP.setStyle('display','none');
				}
				else dP.setStyle('display','block');
				// On place la box au dessous du champ
				var coords = f.getCoordinates(parent);
				var size = f.getSize();
				dP.setStyles({
					'top': (coords.top + size.y - 1) + 'px',
					'left': coords.left + 'px',
					'width': (size.x - 2) + 'px'
				});
				var r = new Request.JSON({
					onSuccess: function( j ) {
						// Insertions des valeurs possibles
						dP.innerHTML = '';
						if(j.length == 0) dP.innerHTML = '&nbsp;Aucun résultat.';
						j.each( function(item) {
							var res = textPatern;
							res = res.replace('%c1%', item.TextField);
							res = res.replace('%c2%', item.TextField2);
							new Element('div', {
								text: res,
								value: item.ValueField,
								events: {
									'mouseover': function() {
										dP.getElements('div').removeClass('active');
										this.addClass('active');
									},
									'click': function() {
										f.value = this.innerHTML;
										$(f.get('id')+'_ac_hidden').value = this.get('value');
										$('proposals_ac').setStyle('display','none');
									}
								}
							}).inject(dP);
						});
					},
					onComplete: function( a ) {
						if(a == undefined) autoCompleteError('Le fichier ACSearch.md ne doit pas être présent dans la skin, dans le dossier '+query+'.');
					}
				}).post('/'+query+'/ACSearch.htm?q='+query+'&s='+f.value+'&vf='+valueField+'&tf='+textField+'&tf2='+textField2+'&limit='+nbResult);
			}, 800);
		}
	});

	// Evenement Focus
	f.addEvent('focus', function() {
		if(f.value == initialText) f.value = '';
	});
}

/**
	* Change le focus actuel après une frappe au clavier
	* @param	ID du champ
	* @param	Déplacement Haut/Bas
	* @return	void
	*/
function changeActiveProposal( field, move ) {
	var proposals = $$('div#proposals_ac div');
	var active = $('proposals_ac').getElement('div.active');
	if(active == null) var next = proposals[0];
	else {
		if(move == 0 && active != null) {
			field.value = active.innerHTML;
			$(field.get('id')+'_ac_hidden').value = active.get('value');
			$('proposals_ac').setStyle('display','none');
		}
		else {
			var next = (move > 0) ? active.getPrevious('div') : active.getNext('div');
			if(next == null) next = active;
			active.toggleClass('active');
		}
	}
	if(next != null) next.toggleClass('active');
}

/**
 * Gestion des erreurs pour la classe
 * @param	msg à afficher
 * @return	void
 */
function autoCompleteError( msg ) {
	var dP = $('proposals_ac');
	if(dP != null) dP.setStyle('display','none');
	if(msg != null) alert('Erreur Autocompletion : ' + msg);
}