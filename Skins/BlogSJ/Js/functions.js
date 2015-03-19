// Multibox
window.addEvent('domready', function(){
	var initMultiBox = new multiBox({
		mbClass: '.mb',
		container: $(document.body),
		descClassName: 'multiBoxDesc',
		useOverlay: true,
		maxSize: {w:800, h:600},
		addRollover: true
	});
});

// Message si l'on a pas JS
window.addEvent('domready', function () {
	var jsToMask = $('javascriptehoh');
	if(jsToMask != null) jsToMask.setStyle('display','none');
});

// Pré rempli un champ avec une valeur grisée
function FieldDefaultText( item, defaultText ) {
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
}