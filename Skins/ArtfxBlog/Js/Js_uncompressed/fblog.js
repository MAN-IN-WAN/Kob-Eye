	var opened = null;var openedDiv = null;
		var SlideList = new Class({
			initialize: function(menu, options) {
				this.setOptions(this.getOptions(), options);
				this.menu = $(menu), this.current = this.menu.getElement('li.current'); 
				this.menu.getElements('li').each(function(item){
				item.addEvent('click',function(){document.location.replace(this.getElement('a').href);});
				item.addEvent('mouseover', function(){ this.moveBg(item,false); }.bind(this));
				item.addEvent('mouseout', function(){ this.moveBg(this.current,true); }.bind(this));
				}.bind(this)); 
				this.back = new Element('li').addClass('background').adopt(new Element('div').addClass('left')).injectInside(this.menu);
				this.back.fx = this.back.effects(this.options);
				if(this.current) this.setCurrent(this.current);
				this.moveBg(this.current,true);
			},
			setCurrent: function(el, effect){
				this.back.setStyles({left: (el.offsetLeft+15)+'px'});
				this.current = el;
			},
			getOptions: function(){
			return {
				transition: Fx.Transitions.sineInOut,
				duration: 500, wait: false,
				onClick: Class.empty
			};
			},
			clickItem: function(event, item) {
				if(!this.current) this.setCurrent(item, true);
				this.current = item;
				this.options.onClick(new Event(event), item);
			},
			moveBg: function(to,leave) {
				if(!this.current) return;
				if (!leave)
				{
					this.back.fx.custom({
						top: [this.back.offsetTop, to.offsetTop],
						left: [this.back.offsetLeft, to.offsetLeft+15],
						opacity:1
					});
					//alert(to.getElement('a').href);
				}else
				{
					this.back.fx.custom({
						top: [this.back.offsetTop, to.offsetTop],
						left: [this.back.offsetLeft, to.offsetLeft+15],
						opacity : 0
					});
				}
				
			}
		});
		SlideList.implement(new Options);
		window.addEvent('domready', function() {
			new SlideList($E('ul', 'menu1'), {transition: Fx.Transitions.backOut, duration: 700, onClick: function(ev, item) { ev.stop(); }});
			new SlideList($E('ul', 'menu2'), {transition: Fx.Transitions.backOut, duration: 700, onClick: function(ev, item) { ev.stop(); }});
			new SlideList($E('ul', 'menu3'), {transition: Fx.Transitions.backOut, duration: 700, onClick: function(ev, item) { ev.stop(); }});
		});

