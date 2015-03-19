/**
Script: 		n2Select.js
Credits:		NN2LUV - http://nn2luv.com/
Framework:		Mootools 1.2.1 - mootools.net.

Script site:
	http://nn2luv.com/sugar/n2select/

Inspiration:
	- Ideas and mootools 1.11 code by [comboBoo.js, Bruno Torrinha]( www.torrinha.com)
	- Ideas of label click, key events and support HTML selects by [adamnfish]( http://www.mooforum.net/member/adamnfish/)
	- Ideas of key shortcut to option and code by [PJMODOS]( http://www.mooforum.net/member/PJMODOS/)

Discussion:
	http://www.mooforum.net/scripts12/n2select-select-with-mootools-former-mooselect-t744.html
*/


/**
* Another extend, check original, if not in extended put into extended, return extended
* Faster than $merge in some cases
*/
function $extendEx(original, extended) {
	for (var key in (original || {}))
		if (!$defined(extended[key])) extended[key] = original[key];

	return extended;
};


/**
* n2Select Class
*/
var n2Select = new Class({

	Implements: [Events, Options],

	options: {
		klass: 'n2Select',
		options: {
		/*
			value: {
				html: string,
				klass: string,
				disabled: boolean
			}
		*/
		},
		selected: false, // default selected value
		/*
		onChange: $empty(),
		*/
		show: 2 // minimum items to show on list
	},
	
	initialize: function(el, options) {
		this.setOptions(options);

		this.elm = $(el);
		this.open = false;
		
		// create custom select
		this.build();

		// we keep an array of our sellinks to hide when click out of them
		n2Select.anchors.push(this.sSelect);
		
		// we keep an array of our objects to re-position when window resize
		n2Select.instances.push(this);
		
		// add events
		this.addSEvents();
	},

	/* generator functions */
	build: function() {
		// generate object from select object & passed options
		this.gen();
		
		// check if selected a disabled item, if so selected first item
		if (this.options.selected === false) this.options.selected = this.elm.get('value');

		if (this.options.options[this.options.selected].disabled === true) {
			for (var op in this.options.options) 
				if (!this.options.options[op].group && !this.options.options[op].disabled) {
					this.options.selected = op;
					break;
				}

			this.elm.set('value', this.options.selected);
		}

		// position
		var el = this.elm,
			pos = this.elm.pos = this.pos(el),

		// styles
			styles = $extend(el.getStyles('font-family',/* 'font-weight',*/ 'font-size'), {
				lineHeight: pos.height
			}),

		// options
			opt = this.options;
		
		// select element
		this.sSelect = new Element('a', {
			'rel': 'noMoreAjax',
			'class': opt.klass + '-anchor',
			'styles': $extendEx(styles, {
				top: pos.top,
				left: pos.left,
				width: pos.width - 25,
				height: pos.height
			}),
			'href': 'javascript:void(0)'
		})
		.inject(this.elm, 'after');
		
		// more position
		pos.width += 5;
		pos.height += 2;

		// select list
		this.sList = new Element('div', {
			'class': opt.klass + '-list',
			'styles': $extendEx(styles, {
				top: pos.top + pos.height,
				left: pos.left,
				width: Browser.Engine.trident ? pos.width : ''
			})
		})
		.set('tween', {duration: 150})
		.inject(this.sSelect, 'after');
		
		this.sScroll = new Fx.Scroll(this.sList, {link: 'cancel'});
		
		// IE6 select objects hack
		if (Browser.Engine.trident4) {
			this.sShim = IFrame({
				src: 'about:blank',
				frameBorder: 0,
				background: 'transparent',
				'styles': {
					top: pos.top + pos.height,
					left: pos.left,
					width: pos.width + 2,
					zIndex: 0,
					position: 'absolute'
				}
			})
			.inject(this.sList, 'after');
		} else
			this.sShim = 0;
		
		// more position
		this.sList.pos = {
			below: pos.top + pos.height,
			above: pos.top - 2 // border-top-size + border-bottom-size
		};

		// item list
		var height = pos.height,
			cnt = 0;

		if (Browser.Engine.trident4)
			$extend(styles, {height: height});
		else
			$extend(styles, {minHeight: height, height: 'auto'});

		//for (var op in opt.options) {
		for (var i=0, l=opt.options.order.length; i<l; i++) { // this helps work with Google Chrome
			var op = opt.options.order[i],
				opp = opt.options[op],
				el2 = new Element('div', {
					'class':
						opt.klass + (cnt%2 ? '-odd' : '-even') + ' ' + 
						opt.klass + (opp.group === true ? '-group' : (opp.sub === true ? '-sub' : '-item')) + 
						(opp.disabled ? ' ' + opt.klass + '-disabled' : '') + 
						(opp.klass ? ' ' + opp.klass : '') + ' ' + 
						(!opp.group && !opp.disabled ? ' ' + opt.klass + '-selectable' : ''),
					'styles': styles,
					'html': opp.html
				})
				.inject(this.sList);
			if (opp.disabled) el2.set('html', el2.get('text'));
			if (opp.klass) el2.klass = opp.klass;
			el2.val = op;

			cnt++;

			if (op+'' === opt.selected+'') {
				this.over(el2);
				this.change(el2, true);
			}
			
			if (!opp.group && !opp.disabled) this.addIEvents(el2);
		}
		delete opt.options/*.order*/; // we may not need the options anymore
		
		// where to find our options
		this.regx = 'div[class*=' + this.options.klass + '-selectable]';

		$extend(this.sList, {
			cnt: cnt,
			ith: height,
			mih: height*opt.show,
			mah: height*cnt
		});

		// more position
		this.sList.pos.above -= this.sList.mah;
		if (this.sList.offsetWidth < pos.width) this.sList.setStyle('width', pos.width);

		// hide elements
		el.setStyle('visibility', 'hidden');
		this.sList.setStyle('display', '');
		this.hide();
	},
	
	destroy: function(show) {
		this.removeSEvents();
		this.sSelect.destroy();

		this.sList.getChildren(this.regx).each(function(el) {
			this.removeIEvents(el);
			el.destroy();
		}.bind(this));
		this.sList.getChildren().destroy();
		this.sList.destroy();

		if (this.sShim) this.sShim.destroy();

		// show HTML select
		if (show) this.elm.setStyle('visibility', 'visible');
	},
	
	gen: function() {
		var R = {}, O = this.options.options, order = []; // it seems Google Chrome sorts options so we need and order array here to work with Google Chrome; IE, FF & Opera does not need this

		function gGrp(el) {
			var k = el.get('label');
		
			if (!O[k]) R[k] = {html: k}; else R[k] = O[k];
			if (el.get('disabled')) R[k].disabled = true;
			
			R[k].group = true;
			
			order.push(k);
		
			el.getElements('option').each(function(e) {
				gOpt(e, k, 1);
			});
		};
	
		function gOpt(el, par, sub) {
			var k = el.get('value');

			if (!O[k]) R[k] = {html: el.get('text')}; else R[k] = O[k];
			if (el.get('disabled')) R[k].disabled = true;
			if (sub && (R[par] && R[par]['disabled']) || (O[par] && O[par]['disabled'])) R[k].disabled = true;
			if (el.get('class')) R[k].klass = el.get('class');
			
			if (sub) R[k].sub = true;
			
			order.push(k);
		};
	
		this.elm.getChildren().each(function(el) {
			if (el.match('optgroup'))
				gGrp(el);
			else
				gOpt(el);
		});
		
		for (var op in O) if (!R[op]) {
			R[op] = O[op];
			
			order.push(op);
		}
		
		R.order = order;
		
		this.options.options = R;
	},
	/* generator functions */
	
	/* anchor functions */
	addSEvents: function() {
		this.sSelect
			.addEvents({
				click: this.click.bind(this),
				blur: this.hide.bind(this),
				keydown: this.key.bind(this)
			});
			//.addEvent((Browser.Engine.trident || Browser.Engine.webkit) ? 'keydown' : 'keypress', this.key.bind(this));
	},

	removeSEvents: function() {
		this.sSelect
			.removeEvents({
				click: this.click.bind(this),
				blur: this.hide.bind(this),
				keydown: this.key.bind(this)
			});
			//.removeEvent((Browser.Engine.trident || Browser.Engine.webkit) ? 'keydown' : 'keypress', this.key.bind(this));
	},

	click: function() {
		if (this.open) this.hide(); else this.show();
	},

	key: function(e) {
		if (e.key == 'tab') return true;

		e = new Event(e);// e.stop();

		var el = 0,
			funcs = {
				esc: 'hide',
				enter: 'click',
				up: 'prev',
				down: 'next',
				36: 'first',
				35: 'last'
			};
			
		switch (e.key) {
			case 'esc':
			case 'enter':
				e.stop();
				this[funcs[e.key]]();
			break;
		
			default:
				switch (e.key) {
					case 'up':
					case 'down':
						e.stop();
						el = this[funcs[e.key]]();
					break;
				}

				switch (e.code) {
					case 36:
					case 35:
						e.stop();
						el = this[funcs[e.code]]();
					break;
				}

				// key shortcut to item
				if (!el) {
					var i, curr, list = this.sList.getElements(this.regx);
					for (i = 0, curr = this.next(); i < list.length; i++, curr = curr.getNext(this.regx)) {
						if (!curr) curr = this.first();
						if (e.key == curr.get('text').charAt(0).toLowerCase()) {
							el = curr;
							break;
						}
					}
				}

				if (el) {
					e.stop();
					this.over(el);
					this.change(el);
				} else if (e.key.length == 1) {
					e.stop();
				}
			break;
		}
	},
	
	first: function() {
		return this.sList.getFirst(this.regx);
	},
	
	last: function() {
		return this.sList.getLast(this.regx);
	},
	
	next: function() {
		return this.selected.getNext(this.regx);
	},
	
	prev: function() {
		return this.selected.getPrevious(this.regx);
	},
	/* anchor functions */
	
	/* list functions */
	show: function() {
		if (n2Select.active !== 0) n2Select.active.hide();
		n2Select.active = this;
		
		// more position
		var pos = 'below', height = window.getSize().y + window.getScroll().y - this.sList.pos[pos], items = this.sList.cnt;
		
		if (height >= this.sList.mih) {
			if (height < this.sList.mah) {
				items = Math.floor(height/this.sList.ith);
			}
		} else {
			pos = 'above';
		}

		pos = {
			top: this.sList.pos[pos],
			height: items*this.sList.ith
		};
		
		this.sList.setStyles(pos);
		
		// IE6 select objects hack
		if (this.sShim) this.sShim.setStyles(pos).set('opacity', 1);

		this.open = true;
		this.sList.tween('opacity', 1);
	},

	hide: function() {
		this.open = false;
		this.sList.tween('opacity', 0);
		
		// IE6 select objects hack
		if (this.sShim) this.sShim.set('opacity', 0);

		n2Select.active = 0;
	},
	/* list functions */
	
	/* item functions */
	over: function(el) {
		var k = this.options.klass + '-selected';
		
		if (this.selected) this.selected.removeClass(k);
		this.selected = el.addClass(k);
	},
	
	change: function(el, first) {
		var opt = el.val;
		
		if ((this.options.selected != opt) || first) {
			this.sSelect.set('html', el.get('html'));

			if (this.sSelect.klass) {
				this.sSelect.removeClass(this.sSelect.klass);
				this.sSelect.klass = 0;
			}
			
			if (el.klass) {
				this.sSelect.addClass(el.klass);
				this.sSelect.klass = el.klass;
			}
			
			this.sScroll.toElement(el);
			
			this.options.selected = opt;
			this.elm.set('value', opt);
		
			this.fireEvent('change', opt);
		}
	},

	select: function(el) {
		this.hide();
		this.change(el);
	},

	addIEvents: function(el) {
		el.addEvents({
			mouseenter: this.over.bind(this, el),
			click: this.select.bind(this, el)
		});
	},

	removeIEvents: function(el) {
		el.removeEvents({
			mouseenter: this.over.bind(this, el),
			click: this.select.bind(this, el)
		});
	},
	/* item functions */

	/* re-position functions */
	pos: function(el) {
		return el.getCoordinates();
		
		/*
		var orgWidth = el.getStyle('width');
		el.setStyles({'position':'absolute', 'width':el.offsetWidth});
			
		var pos = el.getCoordinates();
		pos.left -= el.getStyle('margin-left').toInt();
		pos.top -= el.getStyle('margin-top').toInt();

		el.setStyles({'position':'relative', 'width':orgWidth});
		
		return pos;
		*/
	},
	
	repos: function() {
		var p2 = this.pos(this.elm),
			p1 = this.elm.pos,
			dx = p2.left - p1.left,
			dy = p2.top - p1.top;
			
		[this.sSelect, this.sList, this.sShim].each(function(el) {
			if (el) el.setStyles({
				top: el.getStyle('top').toInt() + dy,
				left: el.getStyle('left').toInt() + dx
			});
		});
		
		this.elm.pos = p2;
	}
	/* re-position functions */
});

n2Select.anchors = [];
n2Select.active = 0;
n2Select.instances = [];

// hide when click out of our sellinks
window.addEvent('domready', function() {
	window.document.addEvent('click', function(e) {
		if ((!n2Select.anchors.contains(e.target)) && (n2Select.active !== 0)) {
		        n2Select.active.hide();
		    	n2Select.active = 0;
		}
	});

	window.addEvent('resize', function() {
		n2Select.instances.each(function(el) {
			el.repos();
		});
	});
});
