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


	/*--- LIENS POPUP ---*/
	$$('a.lienExterne').each(function(item) {
		item.addEvent('click', function(e) {
			e.preventDefault();
			window.open(this.href);
		});
	});

	/*--- MENUS ---*/
	if(Browser.name != 'ie') {
		$$('li.Level0').each(function(li) {
			li.setStyle('overflow','hidden');
			var ul = li.getElement('ul.Menu1');
			if(ul != null) {
				var maxtop = ul.getDimensions(true).height + 1;
				ul.setStyles({'display':'block','position':'relative', 'top':'-'+maxtop+'px'}).set('tween', {'duration': 'short', 'property': 'top'});
				var div = new Element('div', {
					'styles': { 'position':'absolute', 'top':'100%', 'left':'0', 'overflow':'hidden' },
					'class': 'Level1'
				}).wraps(ul);
				li.addEvent('mouseover', function() {
					this.setStyle('overflow','visible');
					ul.get('tween').start('0');
				});
				li.addEvent('mouseout', function() {
					ul.get('tween').start('-'+maxtop+'px').chain(function() {
						li.setStyle('overflow','hidden');
					});
				});
			}
		});
		$$('li.Level1').each(function(li) {
			li.setStyles({
				'overflow':'hidden',
				'position':'relative'
			});
			var ul = li.getElement('ul.Menu2');
			var ulP = li.getParent('ul.Menu1');
			if(ul != null) {
				var maxleft = ul.getDimensions(true).width + 1;
				ul.setStyles({'display':'block','position':'relative', 'left':'-'+maxleft+'px'}).set('tween', {'duration': 'short', 'property': 'left'});
				var div = new Element('div', {
					'styles': { 'position':'absolute', 'top':'0', 'left':'100%', 'overflow':'hidden' },
					'class': 'Level2'
				}).wraps(ul);
				li.addEvent('mouseover', function() {
					this.setStyle('overflow','visible');
					this.getParent('div.Level1').setStyle('overflow','visible');
					ul.get('tween').start('0');
				});
				li.addEvent('mouseout', function() {
					li.getParent('div.Level1').setStyle('overflow','hidden');
					ul.get('tween').start('-'+maxleft+'px').chain(function() {
						li.setStyle('overflow','hidden');
					});
				});
			}
		});
	}

});