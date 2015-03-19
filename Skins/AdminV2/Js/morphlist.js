/*
 Moogets - MorphList 0.8 (formerly SlideList, aka Fancy Menu)
	- MooTools version required: 1.2
	- MooTools components required: 
		Core: Fx.Tween, Fx.Morph, Selectors, Element.Event and dependencies
		More: -

	Changelog:
		- 0.1: First release
		- 0.2: MooTools 1.2 compatible
		- 0.3: Now alters morphs width/height/top properties by default
		- 0.4: Morphing code now part of the function and not of the event.
		- 0.5: setOpacity changed to fade('show') so that visibility css is altered.
		- 0.6: 'left' class changed to 'inner', background options now can be passed in constructor. removed 'on' on fireEvent calls
		- 0.7: setCurrent and morphTo can be chained
		- 0.8: syntax changes
*/

/* Copyright: Guillermo Rauch <http://devthought.com/> - Distributed under MIT - Keep this message! */

var MorphList = new Class({   
	
	Implements: [Events, Options],
	
	options: {/*             
		onClick: $empty,
		onMorph: $empty,*/
		bg: { 'class': 'background', 'html': '<div class="inner"></div>' },
		morph: { 'link': 'cancel' }
	},
	
	initialize: function(menu, options) {
		var that = this;
		this.setOptions(options);
		this.menu = $(menu);
		this.menuitems = this.menu.getChildren();
		this.menuitems.addEvents({
			mouseenter: function(){ that.morphTo(this); },
			mouseleave: function(){ that.morphTo(that.current); },
			mouseup: function(ev){ that.click(ev, this); }
		});       
		this.bg = new Element('li', this.options.bg).inject(this.menu).fade('hide').set('morph', this.options.morph);
		this.setCurrent(this.menu.getElement('.current'));
	},          

	click: function(ev, item) {
	    //ev.stop();
	    this.setCurrent(item, true);
	    //this.fireEvent('click', [ev, item]);
	    /*var Test = Fl.urlToArray(item.getElement('a').href); 
	    Fl.fireEvent('onChanged',Test["Complete"]);
	    Fl.changePage(Test["Complete"]);*/
	},
	
	setCurrent: function(el, effect){  
		if(el && ! this.current) {
			this.bg.set('styles', { left: el.offsetLeft, width: el.offsetWidth, height: el.offsetHeight, top: el.offsetTop });
			(effect) ? this.bg.fade('in') : this.bg.fade('show');
		}
		if(this.current) this.current.removeClass('current');
		if(el) this.current = el.addClass('current');    
		return this;
	},         
         
	morphTo: function(to) {
		if(! this.current) return false; 
		this.bg.morph({ left: to.offsetLeft, top: to.offsetTop, width: to.offsetWidth, height: to.offsetHeight });
		this.fireEvent('morph', to);
		return this;
	}

});