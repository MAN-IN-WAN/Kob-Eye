window.addEvent('domready', function ()	{

	/*--- MULTIBOX ---*/
	/*new multiBox({
		mbClass: '.mb',
		container: $(document.body),
		descClassName: 'multiBoxDesc',
		useOverlay: true,
		maxSize: {w:1000, h:500},
		addRollover: true
	});*/

	$$('a.lienExterne').each(function(lien) {
		lien.addEvent('click', function(e) {
			e.preventDefault();
			window.open(this.href);
		});
	});

});