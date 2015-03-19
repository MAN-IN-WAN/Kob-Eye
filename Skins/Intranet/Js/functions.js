window.addEvent('domready', function ()	{

	/*--- MULTIBOX ---*/
	new multiBox({
		mbClass: '.mb',
		container: $(document.body),
		descClassName: 'multiBoxDesc',
		useOverlay: true,
		maxSize: {w:1000, h:500},
		addRollover: true
	});

});