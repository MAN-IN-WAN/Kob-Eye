
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
			container: document.body,
			loading:0,
			onClick: Class.empty,
			width:'100%'
		};
	},

	initialize: function(options){
		this.setOptions(this.getOptions(), options);
		
		this.options.container = $(this.options.container);
		this.overlay = new Element('div').setProperty('id', 'Overlay').setStyles({
			position: 'absolute',
			left: '0px',
			top: '0px',
			width: this.options.width,
			zIndex: this.options.zIndex,
			backgroundColor: this.options.colour
		}).injectInside(this.options.container);
		if (this.options.loading){
			//On ajoute le chargement 
			this.loading = new Element('img').setProperty('src', '/Skins/Artfx/Img/Loading/loading.gif').setStyles({
				position: 'absolute',
				left: '50%',
				//margin:'0 0 0 -150px',
				top: '150px',
				width: '',
				zIndex: this.options.zIndex
			}).injectInside(this.overlay);
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
			this.overlay.setStyles({top: '0px', height: h}); 
		}else{ 
			var myCoords = this.options.container.getCoordinates(); 
			this.overlay.setStyles({
				top: '0px', 
				height: myCoords.height+'px', 
				left: '0px', 
				width: this.options.width
			}); 
		} 
	},
	
	show: function(){
		this.position();
		this.overlay.style.display='block';
		this.fade.start(0,this.options.opacity);
	},
	
	hide: function(){
		this.position();
		var O = this.overlay;
		this.fade.start(this.options.opacity,0).chain(function () {
			O.style.display='none';
		});
	}
	
});
Overlay.implement(new Options);

/*************************************************************/
