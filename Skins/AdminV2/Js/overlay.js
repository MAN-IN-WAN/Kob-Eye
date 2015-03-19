
/**************************************************************

	Script		: Overlay
	Version		: 1.1
	Authors		: Samuel birch
	Desc		: Covers the window with a semi-transparent layer.
	Licence		: Open Source MIT Licence

**************************************************************/

var Overlay = new Class({
	
	getOptions: function(){
		return {
			colour: '#000',
			opacity: 0.65,
			zIndex: 1,
			container: 'Generale',
			loading:false,
			onClick: Class.empty,
			width:'100%'
		};
	},

	initialize: function(options){
		this.setOptions(this.getOptions(), options);
		this.options.container = $(this.options.container);
		this.overlay = new Element('div').setProperty('id', 'Overlay');
		this.overlay.style.position = 'absolute';
		this.overlay.style.left = '0px';
		this.overlay.style.top = '0px';
		this.overlay.style.width = this.options.width;
		this.overlay.style.zIndex = this.options.zIndex;
		this.overlay.style.backgroundColor = this.options.colour;
		this.overlay.injectInside(this.options.container);
		if (this.options.loading){
			//On ajoute le chargement 
			this.loading = new Element('img').setProperty('src', this.options.loading);
			this.loading.style.position = 'absolute';
			this.loading.style.left = '50%';
			this.loading.style.top = '150px';
			this.loading.style.width = '';
			this.loading.style.zIndex = this.options.zIndex;
			this.loading.injectInside(this.overlay);
		}
	
		this.overlay.addEvent('click', function(){
			this.options.onClick();
		}.bind(this));
		
		this.fade = new Fx.Style(this.overlay, 'opacity',{
			duration:250,
			fps:25
		}).set(0);
		this.position();
		
		window.addEvent('resize', this.position.bind(this));
	},
	
	position: function(){ 
		if(this.options.container == document.body){ 
			var h = window.getScrollHeight()+'px'; 
			this.overlay.style.top='0px';
			this.overlay.style.height=h;
		}else{ 
			var myCoords = this.options.container.getCoordinates(); 
			this.overlay.style.top='0px';
			this.overlay.style.height=myCoords.height+'px';
			this.overlay.style.left= '-212px';
			this.overlay.style.width=myCoords.width+'px';
		} 
	},
	
	show: function(){
		this.position();
		this.fade.start(0,this.options.opacity);
	},
	
	hide: function(){
		this.position();
		this.overlay.style.height='0px';
		this.fade.start(this.options.opacity,0);
	}
	
});
Overlay.implement(new Options);

/*************************************************************/
