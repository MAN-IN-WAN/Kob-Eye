/**
 * Pré rempli un champ avec une valeur grisée
 * @param	HTMLElement		Champ à prendre en charge
 * @param	String			Texte par défaut à affecter
 * @param	HTMLElement		Formulaire  container
 * @return	void
 */
function FieldDefaultText( item, defaultText, form ) {
	if(item == null) return;
	if(item.value == '' || item.value == defaultText) {
		item.value = defaultText;
		item.setStyles({
			'font-style': 'italic',
			'color': '#888'
		});
	}
	item.addEvent('click', function() {
		if(item.value == defaultText) {
			item.value = '';
			item.setStyles({
				'font-style': 'normal',
				'color': '#000'
			});
		}
	});
	item.addEvent('focus', function() {
		if(item.value == defaultText) {
			item.value = '';
			item.setStyles({
				'font-style': 'normal',
				'color': '#000'
			});
		}
	});
	item.addEvent('blur', function() {
		if(item.value == '') {
			item.value = defaultText;
			item.setStyles({
				'font-style': 'italic',
				'color': '#888'
			});
		}
	});

	if(form != null) {
		form.addEvent('submit', function(e) {
			if(item.value == defaultText) item.value = '';
		});
	}
}