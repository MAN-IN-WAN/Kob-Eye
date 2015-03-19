var frameLoader = new Class({
	options: {
		Overlay:false,
		MultiBox:true,
		Container:'Inner',
		OverlayContainer:'Container',
		History:true,
		FlashMenuId:false,
		DefaultPage:'/Accueil.htm',
		Icon:false
	},
	initialize:function (options,MultiBoxOptions){
		this.setOptions(options);
		document.Fl = this;
		//FRAMELOADER
		this.DivBase = this.options.Container;
		//OVERLAY
		if (this.options.Overlay){
			this.overlay = new Overlay({container:this.options.OverlayContainer,loading:this.options.loading,width:'100%',opacity:.40});
		}
		//MULTIBOX
		this.MultiboxOptions = MultiBoxOptions;
		if (!(BrowserDetect.browser=="Explorer"&&BrowserDetect.version<6&&this.options.MultiBox)){
			if (this.options.Overlay){
				 this.MultiboxOptions.useOverlay = true;
				 this.MultiboxOptions.overlay = this.MbOverlay;
			}
			this.MultiboxOptions.OS = BrowserDetect.OS;
			this.MultiBox = new MultiBox('mb', this.MultiboxOptions);
		}
		//Scanne toutes les balises a et modifie le lien
		this.scanLink();
		this.Busy = false;
		//Initialisation de l'historique
		this.index = 0;
		this.moduleKey = 0;
		this.Icon = this.options.Icon;
		if (this.options.History){
			var Fl = this;
			this.History = new HistoryManager();
			this.addEvent('onChanged', function(Lien){
				Fl.History.addState(Lien);
				Fl.index++;
			}.bind(this));
			this.History.addEvent('onHistoryChange', function(hash) {
				if (hash=='')hash=Fl.options.DefaultPage;
				Fl.changePage(hash);
				Fl.FirsTime = true;
			});
		}
	},
	scanLink:function () {
		//recupere la collection de tous les liens
		$$('a').each(function (el){
			if (el.rel == "link") {
				if (!el.alreadyScanned){
					var lien = el.href;
					el.addEvent('click',function(e){
						// capture de l�v�nement
						var myEvent = new Event(e);
						// on le eutralise
						myEvent.stop();
						window.open(lien,"");
					});
					el.alreadyScanned = true;
				}
			}else if (!el.href||el.rel == "lightbox"|| el.rel == "mb"|| el.rel == "nonFrame"||el.className == "mb"||el.className == "bb_a_url") {
			}else if (el.href.search(/mailto/)!= -1) {
			}else if (el.href.search(/\#/)!= -1&&el._allerA==undefined) {
			}else if (el._allerA==undefined) {
				el._allerA = el.href;
				if (el._allerA.search(/\#/) != -1){
					var anc = el._allerA.split('#');
					el._allerA = anc[0];
					el.href="#"+anc[1];
				}else{
					el.removeProperty("href");
				}
				if (el.rel!=undefined&&el.rel!=""){
					var Temp = el.rel;
					Temp = Temp.split(':');
					if (Temp[0]=="background"){
						el.bg = Temp[1];
						el.rel="";
					}
				}

				el.addEvent('click',function(lien,author){
					
					if (this.bg!=undefined&&this.bg!="") {
						//Recuperation de l url
						$('EnteteContainer').style.background= 'url('+this.bg+')';
						/*$('EnteteContainer').style.background-repeat='no-repeat';
						$('EnteteContainer').style.background-position='center center';*/
					}
					var Temp = document.Fl.Url2Array(this._allerA);
					var option = "";
					if (this.rel!= ""&&this.bg=="") {
						option = "nonFlash";
					}
					if (author!="flash"&&option!="nonFlash"){
						var flash = document.id(document.Fl.FlashEntete);
						if (flash!=undefined)flash.changeMenu(Temp["RawPath"]);
						document.Fl.changePage(Temp["Complete"]);
					}
				});
			}
		});
		//Scan de la page
		this.MultiBox.scanPage();
		$$('form').each(function (el2){
			if (el2.className!="loginform"){
				//On recherche les submit du formulaire
				//On traite l attribut action
			        //el2.HTML = el2.HTML + "<input type='hidden' name='XMLHTTPRequest' value='true'";
				var Temp = document.Fl.Url2Array(el2.action,".xml");
				el2.action = Temp["Complete"];
				el2.addEvent('submit',function(e){
					// capture de l�v�nement
					var myEvent = new Event(e);
					// on le eutralise
					myEvent.stop();
					if (document.Fl.overlay!=undefined)document.Fl.overlay.show();
					if (document.Fl.Icon)
					    {
						$$(document.Fl.Icon).setStyle("display","block");
					    }
					// r�cuparation des donn�es du formulaire et envoie de la requ�te
					var Temp2 =document.Fl.Url2Array(el2.action,".htm");
					document.Fl.fireEvent('onChanged',Temp2["Complete"]);
					var Req = new Request.HTML({
						url:Temp2["Complete"],
						evalScripts: true,
						method: (el2.method!=undefined)?el2.method:"post",
						data: el2,
						update: document.Fl.DivBase,
						onComplete:function (e) {
							//alert("VALIDATION DU FORMULAIRE "+Temp2["Complete"]);
							if (document.Fl.overlay!=undefined)document.Fl.overlay.hide();
							if (document.Fl.Icon)
							    {
								$$(document.Fl.Icon).setStyle("display","none");
							    }
							document.Fl.scanLink();
							window.fireEvent("onframeload");
						}
					});
					Req.setHeader('Content-Type','application/x-www-form-urlencoded;ISO-8859-1');
					Req.send();
				});
			}
		});

	},
	changePage:function (Lien,flash,init) {
		this.fireEvent('onChanged',Lien);
		//Communication FLASH
		if (this.options.FlashMenuId)
		    if (typeof(document.id(this.options.FlashMenuId).changeMenu)=="function"&&!flash) document.id(this.options.FlashMenuId).changeMenu(Lien);
		if (document.Fl.Icon)
		    {
			$$(document.Fl.Icon).setStyle("display","block");
		    }
		if (this.options.Overlay)this.overlay.show();
		if (window.getScrollTop()>110)var myFx = new Fx.Scroll(window).scrollTo(0,110);
// 		$('Contenu').style.overflow='hidden';
		this.Busy = true;
		//On charge dans l emplacement
		var Ajx = new Ajax(Lien, {method: 'get', evalScripts: true, update:document.Fl.DivBase,onComplete:function () {
			    if (document.Fl.overlay!=undefined){
				document.Fl.overlay.hide();}
			    if (document.Fl.Icon)
				{
				    $$(document.Fl.Icon).setStyle("display","none");
				}
			document.Fl.Busy=false;
			document.Fl.scanLink();
			window.fireEvent("onframeload");
			}
		    });
		Ajx.request({XMLHTTPRequest:'true'});
	},
	Url2Array:function (url,suffix) {
		if (suffix==undefined) suffix=".htm";
		var Result = new Array;
		Result["Raw"] = url;
		if (url.search(/http/) != -1){
			//Chemin absolu avec domaine donc on supprime le domaine et on remplace
			//Par un chemin relatif
			var splitUrl = url.split('/');
			var shortUrl = "";
			for (i=3;i<splitUrl.length;i++)
			{
				shortUrl +=  "/" + splitUrl[i];
			}
			//Voici le chemin relatif reconstitu�
			url = shortUrl;
		}
		Result["Relative"] = url;
		//On supprime les ancres
		if (url.search(/\#/) != -1){
			var anc = url.split('#');
			url = anc[0];
			Result["Anchor"] = anc[1];
		}
		if (url.search(/\?/) != -1){
			//Chemin avec variable get.
			//ON isole les variables get du chemin pour pouvoir recomposer l url.
			var Temp = url.split('?');
			url = Temp[0];
			Result["GetVars"] = Temp[1];
		}
		//Construction de la requete complete
		if (url!=""&&url!="/") {
			//On supprime l extension
			var Temp = url.split(".");
			//url relative / absolue
			var RawPath = url;
			url = Temp[0]+suffix;
		}else{
			//url relative ou Path est l emplacement
			if (url!=undefined) {
				var RawPath = url;
				url=url+suffix;
			}else{
				var RawPath = "";
			}
		}
		Result["RawPath"] = RawPath;
		Result["Absolute"] = url;
		if (Result["GetVars"]!=undefined)url+="?"+Result["GetVars"];
		Result["Complete"] = url;
		return Result;
	}
});
frameLoader.implement(new Events);
frameLoader.implement(new Options);
window.addEvent('domready',function()
		{
		    window.fireEvent('onframeload');
		});
