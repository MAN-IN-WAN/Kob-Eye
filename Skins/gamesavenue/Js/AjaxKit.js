var AjaxKit = new Class({
	getOptions: function(){
		return {
			container:document.body,
			Os:{
				Windows:1,
				Linux:1,
				Mac:1
			},
			Browser:{
				OmniWeb:-1,
				Safari:1,
				Opera:1,
				iCab:-1,
				Konqueror:-1,
				Firefox:1,
				Camino:-1,
				Netscape:-1,
				Explorer:6,
				Mozilla:1
			},
			Resolution:{
				Width:800,
				Height:600
			},
			backgroundColor: '#fff',
			foregroundColor:'#fff',
			panelColor: '#036690',
			errorColor: '#ff0000',
			textColor:'#036690',
			opacity: 1,
			zIndex: 1,
			check:'/Skins/Pragma/Js/Files/accept.png',
			notcheck:'/Skins/Pragma/Js/Files/delete.png',
			onClick: Class.empty,
			width:'100%'
		};
	},
	initialize: function(options){
		this.setOptions(this.getOptions(), options);
		//Affichage du fond
		this.options.container = $(this.options.container);
		this.Fond = new Element('div').setProperty('id', 'AjaxKitFond');
		/*this.Fond.style.position = 'absolute';
		this.Fond.style.left = '0px';
		this.Fond.style.top = '0px';
		//this.Fond.style.height = '100%';
		this.Fond.style.width = this.options.width;
		this.Fond.style.zIndex = this.options.zIndex;
		this.Fond.style.backgroundColor = this.options.backgroundColor;
		this.Fond.injectInside(this.options.container);*/
		//var ErrorPanel = this.launchtest();
		/*this.Fond.addEvent('click', function(){
			this.options.onClick();
		}.bind(this));*/
		/*this.fade = new Fx.Style(this.Fond, 'opacity',{
			duration:100,
			fps:25
		});*/
		this.position();
		//Verfifcation du cookie
		var co = this.getCookie('AjaxKitH2oEffects');
		if (!this.Error){
			//On efface le scroll
			document.body.style.overflow = "auto";
			//this.fade.start(0);
			fireEvent('Start');
		}else{
			if (co==null){
				//Creation du panneau de chargement
				this.Panel = new Element('div').setProperty('id','Panel');
				this.Panel.style.position = 'absolute';
				this.Panel.style.left = '50%';
				this.Panel.style.top = '250px';
				this.Panel.style.color = 'white';
				this.Panel.style.padding = '5px';
				this.Panel.style.width = '300px';
				//this.Panel.style.height = '300px';
				this.Panel.style.margin = '-150px 0 0 -150px';
				this.Panel.style.zIndex = this.options.zIndex+1;
				this.Panel.style.backgroundColor = this.options.panelColor;
				this.Panel.injectInside(this.Fond);
				//TITRE
				var TitreDiv = new Element('div').setStyles({
					display: 'block',
					margin:'2px',
					padding:'2px',
					'padding-top':'5px',
					background:this.options.foregroundColor,
					height: '20px',
					'text-align':'center',
					'font-size':'13px',
					'font-weight':'bold',
					color:this.options.textColor
				}).injectInside(this.Panel);
				TitreDiv.innerHTML = "TEST DE LA CONFIGURATION";
				//CHARGEMENT
				//var OsDiv = new Element('div').setStyles({
				//	display: 'block',
				//	margin:'2px',
				//	'margin-top':'5px',
				//	padding:'2px',
				//	background:this.options.foregroundColor,
				//	height: '20px',
				//	'text-align':'center'
				//}).injectInside(this.Panel);
				//On ajoute l'animation de chargement
				//this.loading = new Element('img').setProperty('src', this.options.loading).injectInside(OsDiv);;
				var Obj = this;
				ErrorPanel.injectInside(this.Panel);
				//Affichage des boutons
				var Nav = new Element('div').setStyles({
					display: 'block',
					margin:'2px',
					'margin-top':'5px',
					padding:'2px',
					height: '20px',
					'text-align':'center'
				}).injectInside(this.Panel);
				var WithoutJs = new Element('a').setStyles({
					float:'left',
					height:'16px',
					width:'40%',
					padding:'5px',
					'padding-left':'10px',
					'text-align':'center',
					border:"1px solid "+this.options.panelColor,
					'font-weight':'bold',
					background:this.options.foregroundColor,
					color:this.options.textColor
				}).injectInside(Nav);
				WithoutJs.innerHTML = "SANS EFFETS";
				WithoutJs.addEvent('click', function(){
					document.body.style.overflow = "auto";
					Obj.fade.start(0);
					//O,n sauvegarde la configuration dans un cookie
					Obj.setCookie("AjaxKitH2oEffects", "Without");
					val = this.getCookie(testCookieName);
				});
				var WithJs = new Element('a').setStyles({
					width:'40%',
					float:'right',
					height:'16px',
					padding:'5px',
					'padding-left':'10px',
					'text-align':'center',
					'font-weight':'bold',
					color:this.options.textColor,
					border:"1px solid "+this.options.panelColor,
					background:this.options.foregroundColor
				}).injectInside(Nav);
				WithJs.addEvent('click', function(){
					document.body.style.overflow = "auto";
					Obj.fade.start(0);
					Obj.setCookie("AjaxKitH2oEffects", "With");
					fireEvent('Start');
				});
				WithJs.innerHTML = "AVEC EFFETS";
				//Affichage du panneau
				var Dfx = new Fx.Styles(this.Panel,{
					duration:1000,
					fps:25
				});
				Dfx.start({
					top:[-400,((window.getHeight()-300)/2)],
					opacity:[0,1]
				});
			}else{
				if (co=="With"){
					this.fade.start(0);
					fireEvent('Start');
				}else{
					this.Fond.remove();
				}
			}
		}
		//window.addEvent('resize', this.position.bind(this));
	},
	position:function () {
		//if(this.options.container == document.body){ 
			var h = window.getScrollHeight()+'px'; 
			this.Fond.style.top='0px';
			this.Fond.style.height=h;
		//}else{ 
		/*	var myCoords = this.options.container.getCoordinates(); 
			this.Fond.style.top='0px';
			this.Fond.style.height=myCoords.height+'px';
			this.Fond.style.left= '-212px';
			this.Fond.style.width=myCoords.width+'px';
		*/
		//} 
	},
	launchtest:function () {
		var Reponse = new Element('div').setStyles({
				id:'AjaxKitReponse',
				//background: '#ff0000',
			display: 'block',
				padding:'2px',
				margin:'auto'
			});
		this.Error = 0;
		/** OS DETECTION */
		var NivOs = this.isOsOK(BrowserDetect.OS);
		if (NivOs){
			var OsDiv = this.createElem("Système d'exploitation :",BrowserDetect.OS,this.options.check).injectInside(Reponse);
			//Affichage commentaire
		}else{
			var OsDiv = this.createElem("Système d'exploitation :",BrowserDetect.OS,this.options.notcheck).injectInside(Reponse);
			this.Error = 1;
			//Affichage commentaire
			this.createComment(OsDiv,"Votre système d'exploitation ne vous permet de bénéficier de toutes les fonctionnalités.");
		}
		/** BROWSER DETECTION */
		if (this.isBrowserOK()>0){
			var BrDiv = this.createElem("Détails du navigateur :",BrowserDetect.browser+" "+BrowserDetect.version,this.options.check).injectInside(Reponse);
		}else{
			var BrDiv = this.createElem("Détails du navigateur :",BrowserDetect.browser+" "+BrowserDetect.version,this.options.notcheck).injectInside(Reponse);
			this.Error = 1;
			//Affichage commentaire
			this.createComment(BrDiv,"Votre navigateur n'est pas supporté . Veuillez le mettre à jour ou <a href='http://www.firefox.com'>installer mozilla Firefox</a> .");
		}
		/** JAVASCRIPT DETECTION */
		var JaDiv = this.createElem("Activation javascript :","OK",this.options.check).injectInside(Reponse);
		/** COOKIE DETECTION */
		if (this.isCookiesOK()){
			var CoDiv = this.createElem("Gestion des cookies :","OK" ,this.options.check).injectInside(Reponse);
		}else{
			this.Error = 1;
			var CoDiv = this.createElem("Gestion des cookies :","Erreur" ,this.options.notcheck).injectInside(Reponse);
		}
		/** FLASH DETECTION */
		if (this.detectFlash().mode){
			var FlDiv = this.createElem("Version du flash player :",this.detectFlash().version,this.options.check).injectInside(Reponse);
		}else{
			this.Error = 1;
			var FlDiv = this.createElem("Version du flash player :","Aucun Player Flash",this.options.notcheck).injectInside(Reponse);
			//Affichage commentaire
			this.createComment(FlDiv,"Cliquez sur le lien suivant pour installer le plugin adobe flash player.<a href='http://www.adobe.com/go/gntray_dl_getflashplayer_fr'>Installation adobe flash player</a>");
		}
		/** RESOLUTION DETECTION */
		if (1){
			var FlDiv = this.createElem("Dimensions fenêtre :",window.getWidth()+"x"+window.getHeight(),this.options.check).injectInside(Reponse);
		}else{
			this.Error = 1;
			var FlDiv = this.createElem("Dimensions fenètre :","Aucun Player Flash",this.options.notcheck).injectInside(Reponse);
		}
		return Reponse;
	},
	createComment:function (Div,Comment){
		var OsDiv = new Element('div').setStyles({
			margin:'auto',
			display:'block',
			'margin-top':'2px',
			'margin-bottom':'0px',
			padding:'5px',
			background: this.options.panelColor,
			color: this.options.errorColor,
			'text-align':'left'
		}).injectInside(Div);
		OsDiv.style.overflow = 'hidden';
		OsDiv.innerHTML = Comment;
		var Appear = new Fx.Style(OsDiv, 'height',{
			duration:1000,
			fps:25,
			wait:1000
		}).set(0);
		setTimeout(function () {
			Appear.start(50);
		},1000)
	},
	createCheck:function (Div,Color) {
		var voyant = new Element('div').setStyles({
			width: '16px',
			height:'16px',
			float:'right',
			'margin-top':'-14px',
			//position:'relative',
			//top:'-14px',
			'margin-right':'1px',
			background:'url('+Color+')'
		}).injectInside(Div);
		//var Cb = new Element('div').setStyles({clear:'right'}).injectInside(Div);
	},
	createElem:function (Label,Valeur,Color){
		var OsDiv = new Element('div').setStyles({
			display: 'block',
			margin:'auto',
			'margin-top':'5px',
			padding:'2px',
			//position:'relative',
			background:this.options.foregroundColor,
			'text-align':'right'
		});
		var Lab = new Element('div').setStyles({
			float:'left',
			'margin-top':'3px',
			height:'18px',
			padding:'2px',
			'padding-left':'10px',
			'text-align':'left',
			color:this.options.textColor
		}).injectInside(OsDiv);
		Lab.innerHTML = Label;
		var Val = new Element('div').setStyles({
			width:'50%',
			float:'right',
			height:'18px',
			padding:'5px',
			'padding-left':'10px',
			'text-align':'left',
			color:'#fff',
			background:this.options.panelColor
		}).injectInside(OsDiv);
		Val.innerHTML = Valeur;
		this.createCheck(Val,Color);
		var Cb = new Element('div').setStyles({clear:'both'}).injectInside(OsDiv);
		//Creatio du voyant
		return OsDiv;
	},
	whatWinVersion:function (){
		if(navigator.userAgent.indexOf("Windows NT 6.0")>-1) return "Windows Vista";
		if(navigator.userAgent.indexOf("Windows NT 5.2")>-1) return "Windows Server 2003; Windows XP x64 Edition";
		if(navigator.userAgent.indexOf("Windows NT 5.1")>-1) return "Windows XP";
		if(navigator.userAgent.indexOf("Windows NT 5.01")>-1) return "Windows 2000, Service Pack 1 (SP1)";
		if(navigator.userAgent.indexOf("Windows NT 5.0")>-1) return "Windows 2000";
		if(navigator.userAgent.indexOf("Windows NT 4.0")>-1) return "Windows NT 4.0";
		if(navigator.userAgent.indexOf("Windows 98; Win 9x 4.90")>-1) return "Windows Millennium Edition (Windows Me)";
		if(navigator.userAgent.indexOf("Windows 98")>-1) return "Windows 98";
		if(navigator.userAgent.indexOf("Windows 95")>-1) return "Windows 95";
		if(navigator.userAgent.indexOf("Windows CE")>-1) return "Windows CE";
	},
	isBrowserOK:function (){
		var _br = BrowserDetect.browser;
		if (this.options.Browser[BrowserDetect.browser]==-1) return -1;
		if (this.options.Browser[BrowserDetect.browser]>0){
			//check version
			if (BrowserDetect.version>=this.options.Browser[BrowserDetect.browser])return 1;
		}
		return -1;
	},
	isOsOK:function (_os){
	//	return false;
		if (this.options.Os[_os])return true;
		else if (this.options.Os[_os]==undefined)return true
		//if (_os=="Linux")return true;
		return false;
	},
	/** Cookies */
	setCookie:function  ( name, value, exp_y, exp_m, exp_d, path, domain, secure )	{
		var cookie_string = name + "=" + escape ( value );
		if ( exp_y ){
			var expires = new Date ( exp_y, exp_m, exp_d );
			cookie_string += "; expires=" + expires.toGMTString();
		}
		if ( path )cookie_string += "; path=" + escape ( path );
		if ( domain )cookie_string += "; domain=" + escape ( domain );
		if ( secure )cookie_string += "; secure";
		document.cookie = cookie_string;
	},
	getCookie:function ( cookie_name ){
		var results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );
		if ( results )return ( unescape ( results[2] ) );
		else return null;
	},	
	isCookiesOK:function (){
		// TEST
		var testCookieName   = 'testCookies';
		var testCookieValue  = 'cookieOK';
		this.setCookie(testCookieName, testCookieValue);
		val = this.getCookie(testCookieName);
		return testCookieValue == this.getCookie(testCookieName);
	},
	// cookie pour empecher le test 
	setCookBypass:function (name) {
	//	var name = 'm6_replay_test';
		var value = 'bypasstest';
		var days = 7;
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";	
	},
	/** FLASH */
	detectFlash:function (){
		FlashMode = 0;
		if (navigator.plugins && navigator.plugins.length > 0){
			if (navigator.plugins["Shockwave Flash"]){
				var plugin_version = "";
				var words = navigator.plugins["Shockwave Flash"].description.split(" ");
				for (var i = 0; i < words.length; ++i){
					if (isNaN(parseInt(words[i])))
					continue;
					plugin_version = words[i];
				}
				if (plugin_version >= 6){
					var plugin = navigator.plugins["Shockwave Flash"];
					var numTypes = plugin.length;
					for (j = 0; j < numTypes; j++){
						mimetype = plugin[j];
						if (mimetype){
							if (mimetype.enabledPlugin && (mimetype.suffixes.indexOf("swf") != -1))	FlashMode = 1;
							// Mac wierdness
							if (navigator.mimeTypes["application/x-shockwave-flash"] == null)FlashMode = 0;
						}
					}
				}
			}
		}
		if (window.ActiveXObject){
			try{
				for (j = 6; oQTime=new ActiveXObject('ShockwaveFlash.ShockwaveFlash.'+j); j++){
					FlashMode = 1;
					plugin_version=j
				}
			}catch(e) {}
		}
		do_dw_var = FlashMode;
		browser_flash_version = plugin_version;
		//alert(browser_flash_version);
		return {mode:do_dw_var, version:browser_flash_version}
	}
});
AjaxKit.implement(new Options);
AjaxKit.implement(new Events);
