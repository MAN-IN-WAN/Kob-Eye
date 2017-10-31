/**
   FRAMELOADER :: CORE
   Requetes automatiques en Ajax 

   (c) 2009 - _expressiv

**/

var FlFormHeader = 'application/x-www-form-urlencoded;ISO-8859-1';

var frameLoader = new Class({
	
	/** Gestion des options **/
	options: {
		Container        : 'Inner',         /*L'élément qui est mis à jour*/
		Overlay          : false,           /*Utilise-t-on un Overlay ?   */
		Icon             : false,           /*Nom de l'icone si il y a    */
		History          : true,            /*Affichage de l'URL histoy ? */
		FlashMenuId      : false,           /*Menu en flash si il y a     */
		DefaultPage      : '/Accueil.htm',  /*Page par défaut             */
		FlashEntete      : false,           /*Y-a-t-il une entete flash   */
		Actions          : [],              /*Action à lancer après req   */
		Title            : false,           /*Titre de la page            */
		killerActions    : []
	},
	
	/** Attributs */
	overlay  : null,
	milkbox : null,
	busy     : false,
	index    : 0,
	moduleKey: 0,
	stack    : [],

	/** Librairie des actions de base **/ 
	
	actions: {
	
		// Alist x Boolean -> MultiBox
		milkboxLoad : function(options,overlay){
		/*Gestion du multibox*/
		if (overlay) options.useOverlay = overlay;	
			//options.OS = Browser.Platform.name;
			//	    options.mbClass = '.mb';
		return new Milkbox({ resizeTransition:'back:out', topPosition:30 });
		},
	
		// ( () -> () ) -> ( Event -> () )
		stopEvThen : function(fct){
		return function(e){if (e) e.stop();
				fct();};
		},
	
		// Element -> Boolean
		bAjaxLink : function(el){
		return !(!el.href || 
			['lightbox','mb','nonFrame'].contains(el.rel) ||
			el.hasClass("nonFrame") ||
			el.hasClass("mb")||el.hasClass("internLink")||el.hasClass('bb_a_url')||el.hasClass('makePopup') ||
			el.href.search(/mailto/)!=-1 ) &&
			( el._allerA==undefined && el.href.search(/\#/)== -1 );
		},
	
		// Element -> () :: EDB
		splitAnchor : function(el){
		var anc = el._allerA.split('#');
		el._allerA = anc[0];
		el.href    = '#' + anc[1];
		},
	
		// Element -> () :: EDB
		elSplitRel : function(el){
		var temp = el.rel.split(':');
		if (temp[0]!='background') return ;
		el.bg = temp[1];
		el.rel = '';
		} ,
	
		// Url x Identifier [x String x Element] -> Request.HTML 
		getAjaxObject : function(urlR,targetR,methodR,dataR,killer){
			if (!methodR) methodR='post';
			else methodR = methodR.toLowerCase();
			if (killer)
				result = function(){
					if (this.options.Overlay)
						this.options.Overlay.stop();
					if (this.options.Icon)
						this.actions.iconHide(this.options.Icon);
					this.busy = false;	
					this.launchKillerActions();
				}.bind(this);
			else{
				var th=this;
				result = function() {
					if (url = this.getHeader('Ke-Url')){
						location.href = location.href.split('#')[0]+"#"+this.getHeader('Ke-Url')+".htm";
					}
					if (th.options.Overlay)
						th.options.Overlay.stop();
					if (th.options.Icon)
						th.actions.iconHide(th.options.Icon);
					th.busy = false;
					th.launchActions();
					th.scanLink(targetR);
					window.fireEvent('onFrameLoad');
					window.removeEvents('onFrameLoad');
					window.fireEvent('onFrameLoadAlways');
				};
			}
			///--> Ajouter la valeur des submits
			// Gestion du bug des submit
			if (methodR=='post'&&dataR!=undefined){
				//for (var i in r.POST)
				var t = document.moo(dataR).toQueryString();
				//recuperation de la valeur du submit sélectionné
				var Inputs = dataR.getElements('input[type=submit]');
				for (var i = 0;i < Inputs.length ; i++){
					if (Inputs[i].get('clicked')=='true'){
						t+='&'+Inputs[i].name+'='+Inputs[i].value;
						Inputs[i].set('clicked','');
					}
				}
				dataR = t;
			}//else if (methodR=='post') alert(dataR);
			var dict = {url:urlR,
					method: methodR,
					update : $(targetR),
					data:dataR,
					onSuccess : result};
			if (killer){r= new Request(dict);}
			else{
				r = new Request.HTML(dict);
			}
			if (dataR) r.setHeader('Content-Type',FlFormHeader);
			return r;
		},
	
		// Url x Boolean -> ()
		flashChangeMenu : function (lien){
		if (typeof($(this.options.FlashMenuId).changeMenu) == "function")
			$(this.options.FlashMenuId).changeMenu(lien);
		},
	
		// Identifier -> () :: EDB
		iconShow : function (icon){$(icon).setStyle('display','block');},
		iconHide : function (icon){$(icon).setStyle('display','none');},
	
		// Identifier -> () :: EDB
		overlayShow : function (overlay){overlay.show();},
		overlayHide : function (overlay){overlay.hide();},
	
		// () -> [String]
		getModAndKELink : function(){
		var link = this.History.getCurrentLocation().substring(1).split('.')[0];
		return [link.split('/')[0],link];
		},
	
		// Element -> ()
		makeNewpageLink : function(el){
		el.addEvent('click',this.actions.stopEvThen(function(){
			window.open(arguments[0],"");
		}.pass(el.href)));
		el.rel = "noMoreAjax";
		},
		/**
		* Traitement des listes
		* @param Element table
		*/
		makeList : function (el){
			//Si deja initialisé alors on sort
			if (el.initialized)return false;
			//Configuration de la propriete des filtres
			//BEGIN INITIALISATION
			//general
			el.params = [];
			el.lines = [];
			el.template = el.get('template');
			el.isComponent = true;
			el.url = el.get('url');
			el.filters = {};
			el.flag = false;
			el.sheight=el.getParent().getSize().y -20;
			el.overlay = el.get('overlay');
			el.initialized = true;
			var u = new Element('div');
			el.model = $(el.template).getFirst().dispose();
			el.container = $(el.template);
			//pagination
			el.paginationVar = el.get('paginationVar');
			el.pagination = el.get('pagination');
			if (el.pagination!=""){
				el.pagination = $(el.pagination);
				el.paginationFirst = el.pagination.getElementById('first').dispose();
				el.paginationLast = el.pagination.getElementById('last').dispose();
				el.paginationNext = el.pagination.getElementById('next').dispose();
				el.paginationBack = el.pagination.getElementById('back').dispose();
				el.paginationModel = el.pagination.getElementById('page').dispose();
			}
			//END INITIALISATION
			if (el.template==undefined)alert('LIST: attribut template manquant');
			if (el.url==undefined)alert('LIST: attribut url manquant');
			if (el.overlay==undefined)alert('LIST: attribut overlay manquant');
			if (el.model==undefined)alert('LIST: template '+el.template+' introuvable');
			//Implantation des fonctions de liste
			el.refresh = function (){
				//Affichage du chargement
				document._FLNC.options.Overlay.start(el.overlay);
				//Execution de la requete
				var t = new Request.JSON({
					url:el.url,
					secure:true,
					data:el.filters,
					onSuccess:function(json,result){
						//On vide la liste
						el.reset();
						//On cache l'overlay
						document._FLNC.options.Overlay.stop(el.overlay);
						//Affichage des lignes
						var items = json.list.items;
						for (var i=0;i<items.length;i++){
							var u = new Element('tbody');
							u.adopt(el.model.clone());
							var h = unescape(u.get('html'));
							h=h.substitute(items[i]);
							u.set('html',h);
							u = u.getFirst();
							el.lines.push(u);
							el.container.adopt(u);
							if (u.get('href'))document._FLNC.actions.makeXHRLink.bind(document._FLNC)(u);
							/*u._allerA = u.get('url');
							u.addEvent('click',function(ele){
								var T = document._FLNC.urlToArray(this._allerA);
								if (!document._FLNC.busy) document._FLNC.fireEvent('onChanged',T["Complete"]);
								//Cas ou le lien est situé dans un formulaire
								if (this.get('target')==undefined){
									var myForm = $('mainthisform');
								}else{
									if ($(this.get('target'))){
										document._FLNC.changePage(T["Complete"],undefined,undefined,$(this.get('target')),'post',this);
									}else var myForm = this.getParent('form');
								}
								myForm.action = this._allerA;
								myForm.fireEvent('submit');
							}.bind(u));*/
						}
						//Pagination
						if (el.pagination!=undefined)el.setPagination(json.list.start,json.list.total,json.list.itemperpage);
						document._FLNC.scanLink(el);
						el.fxHeader();
					}.bind(this),
					onError:function (text,error){
						document._FLNC.options.Overlay.stop(el.overlay);
						alert('LIST: Problème de connexion. Veuillez réessayer ou contacter votre support technique.');
					}
				}).send();
			};
			/**
			* Gestion des filtres et ordres.
			* Si un changement de filtre est détecté alors on reinitialise la pagination.
			*/
			el.set = function (vars,first) {
				var dirty = false;
				var t = vars.toQueryString();
				//Decomposition de la requete pour en extraire les filtres
				var ta = t.split('&');
				ta.each(function (item,index){
					if (el.filters[item.split('=')[0]]!=item.split('=')[1]&&item.split('=')[0]!=el.paginationVar)dirty=true;
					el.filters[item.split('=')[0]] = item.split('=')[1];
				});
				//recuperation de la valeur du submit sélectionné
				var Inputs = vars.getElements('input[type=submit]');
				for (var i = 0;i < Inputs.length ; i++){
					if (Inputs[i].get('clicked')=='true'){
						Inputs[i].set('clicked','');
						el.filters[Inputs[i].name] = Inputs[i].value;
						if (el.filters[Inputs[i].name]!=Inputs[i].value&&Inputs[i].name!=el.paginationVar) dirty=true;
					}
				}
				if (dirty&&!first) el.filters[el.paginationVar] = 1;
			};
			/**
			* Reinitialisation des lignes
			*/
			el.reset = function (){
				this.lines.each(function (item,index) {item.destroy();});
				this.lines.erase();
			};
			/**
			* Gestion de la pagination
			*/
			el.setPagination = function (offsetitem,totalitem,itemperpage){
				el.pagination.set('html','');
				//calcul du nombre de page
				var totalpage = (Math.floor(totalitem/itemperpage)==totalitem/itemperpage) ? totalitem/itemperpage : Math.floor(totalitem/itemperpage)+1;
				var currentpage = (offsetitem/itemperpage)+1;
				if (currentpage>1){
					//affichage first
					var u = el.paginationFirst.clone();
					u.set('value','1');
					el.pagination.adopt(u);
					//affichage back
					var u = el.paginationBack.clone();
					u.set('value',currentpage-1);
					el.pagination.adopt(u);
					if (currentpage>2){
						//first page	
						var u = el.paginationModel.clone();
						u.set('value',1);
						el.pagination.adopt(u);
						var u = new Element('div');
						u.addClass('information');
						u.set('style','line-height:15px;float:left;margin-left:2px;margin-right:2px;')
						u.set('html','...');
						el.pagination.adopt(u);
					}
				}
				//currentpage -1
				if (currentpage-1>0){
					var u = el.paginationModel.clone();
					u.set('value',currentpage-1);
					el.pagination.adopt(u);
				}
				//currentpage
				var u = el.paginationModel.clone();
				u.set('value',currentpage);
				u.addClass('CurrentPage');
				el.pagination.adopt(u);
				//currentpage +1 
				if (currentpage+1<=totalpage){
					var u = el.paginationModel.clone();
					u.set('value',currentpage+1);
					el.pagination.adopt(u);
				}
				//affichage pages
				if (currentpage<totalpage){
					if (currentpage<totalpage-1){
						var u = new Element('div');
						u.set('style','line-height:15px;float:left;margin-left:2px;margin-right:2px;')
						u.set('html','...');
						el.pagination.adopt(u);
						//last page	
						var u = el.paginationModel.clone();
						u.set('value',totalpage);
						el.pagination.adopt(u);
					}
					//affichage next
					var u = el.paginationNext.clone();
					u.set('value',currentpage+1);
					el.pagination.adopt(u);
					//affichage last
					var u = el.paginationLast.clone();
					u.set('value',totalpage);
					el.pagination.adopt(u);
				}
				//information
				var u = new Element('div');
				u.set('style','line-height:15px;')
				u.set('html','total page : '+totalpage+' total elements : '+totalitem+' elements par page : '+itemperpage);
				el.pagination.adopt(u);
				//valeur de la page en hidden
				var u = new Element('input');
				u.set('type','hidden')
				u.set('name',el.paginationVar);
				u.set('value',currentpage);
				el.pagination.adopt(u);
				//scanlink
				document._FLNC.scanLink(el.pagination);
			}
			/**
			* Positionnement du scroll
			*/
			el.scrollHeader = function() {
				if(el.flag) {
					return;
				}
				//Mise ajour des dimensions
				el.sheight=$('Scroller:fx:OuterDiv').getParent().getSize().y-20;
				var fh=$('Scroller:fx');
				var sd=$(el.get('id')+':scroller');
				fh.style.left=(0-sd.scrollLeft)+'px';
				$(el.get('id')+':scroller').style.height = el.sheight+'px';
				//Mise à jour des données
				
			};
			el.addScrollerDivs = function () {
				var sd=new Element("div");
				var tb=el;
				//on cree le container de l'entete
				sd.style.height=el.sheight+"px";
				sd.style.overflow='visible';
				sd.style.overflowX='auto';
				sd.style.overflowY='scroll';
				sd.style.width=tb.width;
				sd.id=el.get('id')+':scroller';
				sd.onscroll=el.scrollHeader;
				//on deplace la table
				tb.parentNode.adopt(sd);
				tb.dispose();
				sd.adopt(tb);
				//on cree l'entete
				var sd2= new Element("div");
				sd2.set('id','Scroller:fx:OuterDiv');
				sd2.style.cssText='position:relative;width:100%;overflow:hidden;overflow-x:hidden;padding:0px;margin:0px;';
				sd2.innerHTML='<div id="Scroller:fx" style="position:relative;width:9999px;padding:0px;margin-left:0px;"><div id="Scroller:content"></div></div>';
				sd.parentNode.insertBefore(sd2,sd);
			};
			el.fxHeader = function () {
				if(el.flag) {return;}
				el.flag=true;
				var tbDiv=el;
				tbDiv.rows[0].style.display='';
				var twp=tbDiv.getSize().x;
				var twi=100;
				twi=(($('Scroller:fx:OuterDiv').offsetWidth * twi) / 100)-15;
				twp=twi+'px';
				tbDiv.style.width=twp;
				var oc=tbDiv.rows[0].cells;
				var fh=$('Scroller:fx');
				var tb3=tbDiv.cloneNode(true);
				tb3.id='Scroller:content';
				tb3.style.marginTop = '0px';
				fh.replaceChild(tb3,$('Scroller:content'));
				$('Scroller:fx:OuterDiv').style.height=(oc[0].offsetHeight+2)+'px';
				tbDiv.style.marginTop = "-" + (tbDiv.rows[0].offsetHeight +2) + "px";
				el.scrollHeader();
				window.onresize=el.fxHeader;
				el.flag=false;
			}
			el.fxheaderInit = function() {
				el.flag=false;
				el.addScrollerDivs();
				el.fxHeader();
			};
			//Initialisation du scroll
			el.fxheaderInit();
			//detection des filtres/pagination deja existant
			var f = el.getParent('form');
			if (!f)alert('LIST: formulaire introuvable');
			el.set(f,true);
			//Population de la liste
			el.refresh();

		},
		/**
		* makeXhrLink
		* Definit un lien par defaut pour un element
		* @param el l'element à definir
		* @param target la cible par defaut
		*/
		makeXHRLink : function(el,target){
			//prevention contre un traitement supplementaire
			el.rel = "noMoreAjax";
			//enregistrement du lien sur une variable protégée
			el._allerA = el.get('href');
			//if (el._allerA.indexOf('#') != -1) this.actions.splitAnchor(el);
			//else
			//supression de l'attribut href 
			el.removeProperty("href");
			//if (el.rel!=undefined) this.actions.elSplitRel(el);
			el.addEvent('click',this.actions.stopEvThen(function(el,target){
				//definition d'un background sur le click
				if (el.bg) $('EnteteContainer').setStyle('background','url('+el.bg+')');
				//traitement de l'url
				var T = this.urlToArray(el._allerA);
				//definition du lien
				if (!(el.rel!='' && el.bg=='')){
					if (!this.busy) this.fireEvent('onChanged',T["Complete"]);
					//Cas ou le lien est situé dans un formulaire
					if (!el.get('target')){
						var myForm = $('mainform');
					}else{
						if ($(el.get('target'))){
							document._FLNC.changePage(T["Complete"],undefined,undefined,$(el.get('target')),'post',el);
							return;
						}else var myForm = el.getParent('form');
					}
					myForm.action = el._allerA;
					myForm.fireEvent('submit');
				}
			}.bind(this,[el,target,undefined])));
		},
	
		//Pour les form déclenche la validation du formulaire
		makeXHRForm : function(el,target,currentLocation,killer){
			if (!currentLocation) currentLocation = this.History.getCurrentLocation();
			if (!target) target = this.options.Container;
			//el.action = this.urlToArray(el.action,".xml")["Complete"];
			el.addEvent('submit',this.actions.stopEvThen(function(el,target,killer){
				if (el.get('target'))target = el.get('target');
				var T = this.urlToArray(el.action,".htm");
				if (T["Relative"] == "/"||el.action=="") el.action = this.History.getCurrentLocation();
				var T = this.urlToArray(el.action,".htm");
				if (!killer && !el.hasClass("refreshMyself")) this.fireEvent('onChanged',T["Complete"]);
				if (el.hasClass("refreshMyself")){
					target = el.getParent();
				}
				this.changePage(T["Complete"],undefined,undefined,target,el.method,el,killer);
			}.bind(this,[el,target,killer])));
		},
	
		//Pour les input[type=submit] delenche la validation du formulaire
		makeXHRButton : function(el){
			var myForm = el.getParent('form');
			var Inputs = myForm.getElements('input[type=submit]');
			for (var i = 0;i < Inputs.length ; i++){
				if (Inputs[i] == el){
					el.set('clicked','true');
				}
			}
		},
	
		//Pour les liens a avec rel="confirm"
		makePopupWindow : function(el){
			el._allerA = el.rel;
			el.rel     = "noMoreAjax";
			//Cas ou le lien est situé dans un formulaire
			if (el.get('target')==undefined){
				var myForm = $('mainform');
			}else var myForm = el.getParent('form');
			el.addEvent('click',this.actions.stopEvThen(function(){
				var T = this.urlToArray(el.get("href"))["Complete"];
				//Affichage du formulaire
				var confirmWindow = new MUI.Modal({
					id: 'htmlPopup',
					title: el.get('title'),
					width: 800,
					height: 600,
					modalOverlayClose:false,
					loadMethod:'xhr',
					contentURL:T,
					onContentLoaded:function () {
						this.scanLink($('htmlPopup_content'),$('htmlPopup_content'));
						$('htmlPopup_content').innerHTML+='<div class="PopUpImg" style="float:left;"></div><div><a class="KEBouton" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" href="'+el.get('href')+'" id="ConfirmValider">Valider</a><a class="KEBouton" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" id="ConfirmAnnuler">Annuler</a></div>';
						$('ConfirmAnnuler').addEvent('click',function (){
							confirmWindow.close();
						});
						$('ConfirmValider').addEvent('click',function (e){
							e.stop();
							var t = new Request.JSON({
								url:T,
								secure:true,
								data:$('htmlPopup_content').toQueryString()+'&SaveObject=Enregistrer',
								onSuccess:function(json,result){
									if (myForm!=undefined){
										myForm.action = el.get("redirectUrl");
										/*var Inputs = myForm.getElements('input[type=submit]');
										for (var i = 0;i < Inputs.length ; i++){
											//Inputs[i].set('value','');
											Inputs[i].set('name','');
										}*/
										myForm.fireEvent('submit');
									}else this.changePage(T,undefined,undefined,undefined,"POST",el,undefined);
								}.bind(this),
								onError:function (text,error){
									alert('Problème de connexion. Veuillez réessayer ou contacter votre support technique.');
								}
							});
							t.send();
							confirmWindow.close();
						}.bind(this));
					}.bind(this)
				});
			}.bind(this)));
		},
	
		//Pour les liens a avec rel="confirm"
		makeConfirmWindow : function(el){
			el._allerA = el.rel;
			//el.rel     = "noMoreAjax";
			//Cas ou le lien est situé dans un formulaire
			if (el.get('target')==undefined){
				var myForm = $('mainform');
			}else var myForm = el.getParent('form');
			el.addEvent('click',this.actions.stopEvThen(function() {
				var T = this.urlToArray(el.get("href"))["Complete"];
				//Affichage du formulaire
				var confirmWindow = new MUI.Modal({
					id: 'htmlConfirm',
					title: el.get('title'),
					width: 400,
					height: 120,
					modalOverlayClose:false,
					content:'<div class="PopUpImg" style="float:left;"></div><div style="padding-top:20px;padding-bottom:20px;padding-left:40px;">'+el.get('message')+'</div><div><a class="KEBouton" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" href="'+el.get('href')+'" id="ConfirmValider">Valider</a><a class="KEBouton" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" id="ConfirmAnnuler">Annuler</a></div>',
					onContentLoaded:function () {
						$('ConfirmAnnuler').addEvent('click',function (){
							confirmWindow.close();
						});
						$('ConfirmValider').addEvent('click',function (e){
							e.stop();
							var t = new Request.JSON({
								url:T,
								secure:true,
								data:document.moo(myForm).toQueryString(),
								onSuccess:function(json,result){
									if (myForm!=undefined){
										myForm.action = el.get("redirectUrl");
										/*var Inputs = myForm.getElements('input[type=submit]');
										for (var i = 0;i < Inputs.length ; i++){
											//Inputs[i].set('value','');
											Inputs[i].set('name','');
										}*/
										myForm.fireEvent('submit');
									}else this.changePage(T,undefined,undefined,undefined,"POST",el,undefined);
								}.bind(this),
								onError:function (text,error){
									alert('Problème de connexion. Veuillez réessayer ou contacter votre support technique.');
								}
							});
							t.send();
							confirmWindow.close();
						}.bind(this));
					}.bind(this)
				});
			}.bind(this)));
		},

		//Pour les input[type=submit&rel=save] declenche l'enregistrement par XHR et ensuite redirige la page si valide ou affiche les erreurs
		makeSaveButton : function(el){
			//Recuperation du formulaire
			var myForm = el.getParent('form');
			var target = this.options.Container;
			//CKEDITOR
			for ( instance in CKEDITOR.instances ){
	    			CKEDITOR.instances[instance].updateElement();
				/*var el = $(instance);
			        if (el) {
			            // set matching original element's value to data
			            el.set("text", CKEDITOR.instances[instance].getData());
			        }*/
	    		}
			//On desactive les champs submit non concernés
			var Inputs = myForm.getElements('input[type=submit]');
			for (var i = 0;i < Inputs.length ; i++){
				if (Inputs[i] == el){
					el.set('readonly','readonly');
				}else{
					//Inputs[i].set('value','');
					Inputs[i].set('name','');
				}
			}
			//Récupération de l'url
			var a = el.get("checkUrl");
			//Gestion des editeurs
			$$('textarea.mceEditor').each(function(el){
				if ($chk(el.getParent().getElement('span.mceEditor')))
				tinyMCE.execCommand('mceRemoveControl',false,el.get('id'));
			});
			if (this.options.Overlay)
				this.options.Overlay.start();
			//on effectue la requete de sauvegarde
			var t = new Request.JSON({
				url:a,
				secure:true,
				data:document.moo(myForm).toQueryString(),
				onSuccess:function(json,result){
					if (this.options.Overlay)
						this.options.Overlay.stop();
					if (!json){
						//Message erreur
						var confirmWindow = new MUI.Modal({
							id: 'htmlConfirm',
							title: 'ERREUR',
							width: 400,
							height: 120,
							modalOverlayClose:false,
							content:'<div class="PopUpImg" style="float:left;"></div><div style="padding-top:20px;padding-bottom:20px;padding-left:40px;overflow:auto;">Une erreur est survenue: <br />'+result+'</div><div><a class="KEBouton" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" href="" id="ConfirmValider">Reessayer</a><a class="KEBouton" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" id="ConfirmAnnuler">Annuler</a></div>',
							onContentLoaded:function () {
								$('ConfirmAnnuler').addEvent('click',function (){
									confirmWindow.close();
								});
								$('ConfirmValider').addEvent('click',function (e){
									e.stop();									
									t.send();
									confirmWindow.close();
								}.bind(this));
							}.bind(this)
						});
						
					}
					var myForm = el.getParent('form');
					if (json.success){
						//ENREGISTREMENT ET REDIRECTION
						//alert("Check form ok donc redirection "+el.get("redirectUrl"));
						var T = this.urlToArray(el.get("redirectUrl"));
						//Cas ou le lien est situé dans un formulaire
						if (myForm!=undefined){
							myForm.action = el.get("redirectUrl");
							/*var Inputs = myForm.getElements('input[type=submit]');
							for (var i = 0;i < Inputs.length ; i++){
								//Inputs[i].set('value','');
								Inputs[i].set('name','');
							}*/
							myForm.fireEvent('submit');
						}else this.changePage(T["Complete"],undefined,undefined,target,el.method,el,undefined);
					}else{
						//AFFICHAGE DES ERREURS DU FORMULAIRE
						//Recuperation de la div id=errors et affichage des erreurs
						var er = document.moo('errorscontainer');
						var msg = "<div id='errors'><h3>Erreur de saisie:</h3><ul>";
						for (var i = 0;i<json.errors.length;i++){
							msg += "<li>"+json.errors[i].message+"</li>";
							//Surlignage des champs en erreurs 
							var Inputs = myForm.getElements('input[name=Form_'+json.errors[i].field+']');
							for (var j = 0;j < Inputs.length ; j++){
								Inputs[j].setStyle('background-color','#ff0000');
								Inputs[j].setStyle('color','white');
							}
						}
						msg += "</ul></div>";
						er.innerHTML = msg;
						//Scroll to top class=Panneau
						var Inputs = myForm.getElements('div[class=Panneau]');
						for (var j = 0;j < Inputs.length ; j++){
							Inputs[j].scrollTo(0,0);
						}
					}
				}.bind(this),
				onError:function (text,error){
					alert('Problème de connexion. Veuillez réessayer ou contacter votre support technique.');
				}
			});
			t.send();
		},
	},
	
	// Alist x Alist -> ()
	initialize : function (options){
		this.setOptions(options);
		if (this.options.Title)
		document.title = this.options.Title;
		//Historique
		this.History = new HistoryManager();
		this.History.addEvent('onHistoryChange',
			function(hash){
				if (!hash) hash=this.options.DefaultPage;
				this.changePage(hash);
			}.bind(this));
		this.addEvent('onChanged',function(lien){
			this.History.addState(lien);
		}.bind(this)) ;
		window.addEvent('domready',function(){
			var url = window.location.href;
			var t = url.split('#');
			if ($defined(t[1])){
				if (t[1].search(/htm/) == -1) return;
				this.changePage(t[1],true);
			}
		}.bind(this));
	},
	
	prepareMb :function(milkboxOptions){
		if (this.options.MultiBox)
		this.milkbox = this.actions.milkboxLoad(milkboxOptions);
	},
	
	/**
	* scanLink
	* Scanne tous les liens et effectue les actions necessaires en fonction des tags definis
	* @param containerId Le conteneur à scanner
	* @param target la cible à recharger par defaut
	*/
	scanLink : function(containerId,target){
		//defintion du container par defaut
		if (containerId&&$(containerId)){ 
			var S = $(containerId);
		}else var S = document;
		//definition de la cible par defaut
		if (!target) target = this.options.Container;
		//Lien de confirmation
		S.getElements('a[rel=confirm]').each(
			this.actions.makeConfirmWindow.bind(this));
		//Ouverture des popups
		S.getElements('a[rel=popup]').each(
			this.actions.makePopupWindow.bind(this));
		//Traitement des listes
		S.getElements('table[rel=list]').each(
			this.actions.makeList.bind(this));
		//Lien externe
		S.getElements('a[rel=link]').each(
			this.actions.makeNewpageLink.bind(this));
		//Preparation du multibox
		this.milkbox.prepareGalleries(undefined,$$("a.mb"));
		this.milkbox.prepareEvents();
		//Traitement des liens standards
		S.getElements('a[rel!=noMoreAjax]').each(function(el){
			if(!this.actions.bAjaxLink(el)) return;
			this.actions.makeXHRLink.bind(this)(el,target);
		}.bind(this));
		//forme : rel = sous-url :: url :: target
		S.getElements('input[type=submit]').each(function(el){
			if (el.get('rel')=='save') el.addEvent('click',this.actions.stopEvThen(function(el){
					this.actions.makeSaveButton.bind(this,el)();
				}.bind(this,el)));
			else el.addEvent('click',function(el){
					this.actions.makeXHRButton.bind(this,el)();
				}.bind(this,el));
		}.bind(this));
		//Traitement des formulaires
		S.getElements("form").each(function(el){
			el.removeEvents('submit');
			if (el.get('id')!="main") this.actions.makeXHRForm.bind(this)(el,target);
		}.bind(this));
	},
	
	// Url [x Boolean x ? x Identifier x String x Element] -> () :: EDB
	changePage : function(lien,flash,init,target,arg2,element,killer){
		//Definition du container par defaut
		if (!target) target=this.options.Container;

		if (!lien) lien = this.History.getCurrentLocation();
		if (!lien) lien = this.urlToArray(document.location.href)["Complete"];
		if (lien=='javascript:;.htm'){
			//STOP LOADING
			if (this.options.Overlay)
				this.options.Overlay.stop();
			//BUG TINYMCE
			return;
		}

		if (this.busy && !killer) return ;
		// TEMP
	
		//BEGIN DIRTY WORKAROUND
		$$('textarea.mceEditor').each(function(el){
			//tinyMCE.execCommand('mceFocus', false, el.get('id'));
			//tinyMCE.execCommand('mceRemoveControl',false,el.get('id'));
		});
		$$('#mooRainbow').each(function(el){el.destroy();});
		$$('.swiff-uploader-box').each(function(el){el.destroy();});
		//END DIRTY WORKAROUND
		
		//On reinitialise la valeur de l'attribut action du formulaire principal		
		$('mainform').action="";

		//Gestion du menu en flash
		if (this.options.FlashMenuId && !flash)
		this.actions.flashChangeMenu(lien,flash).bind(this);

		//Affichage d'un icon ??
		if (this.options.Icon)
		this.actions.iconShow(this.options.Icon);
	
		//Affichage de l'overlay de chargement
		if (this.options.Overlay)
		this.options.Overlay.start(target);

		//Test du type de la cible
		if (element&&element.get('target')){
			//if (!$(element.get('target')))alert('FORM: target '+element.get('target')+' inconnu');
			//Test de la cible 
			var t = $(element.get('target'));
			if (t&&t.isComponent){
				t.set(element);
				t.refresh();
				return;
			}
		}
		//Execution de la requete
		this.busy = true;
		Ajx = this.actions.getAjaxObject.bind(this,[lien,target,arg2,element,killer])();
		Ajx.send();
	},
	
	//  String x String -> String
	urlToArray : function(urlSend, suffix){
		var url     = urlSend;
		var result  = new Array();
		var rawPath = '';
		if (!suffix) suffix = '.htm';
		result["Raw"] = url;

		if (url.search(/http/) != -1)
		url = '/' + url.split('/').slice(3).join('/');
		result["Relative"] = url;
		if (url.search(/\#/) != -1) {
			var anchor = url.split('#');
			url = anchor[1];
			result["Anchor"] = anchor[1];
		}
		if (url.search(/\?/) != -1) {
			var temp = url.split('?');
			url = temp[0];
			result["GetVars"] = temp[1];
		}
		rawPath = url;
		if (!['','/'].contains(url)){
			//url = url.split('.')[0] + suffix;
			url2 = url.split('/');
			url2 = url2[url2.length-1];
			//console.log('dernier occ '+url2);
			if (url2.split('.').length>1) {
				url2 = url2.substr(0,url2.lastIndexOf('.'));
			}
			//console.log('--> '+url2);
			url = url.split('/');
			url[url.length-1] = url2 + suffix;
			url=url.join('/');
			//console.log('TADAAAA '+url);
			//url = url.slice(url.lastIndexOf('.')+1)
		}else if (url != undefined)
			url += suffix;
		else
			rawPath = '';
		result["RawPath"] = rawPath;
		result["Absolute"] = url;
		if (result["GetVars"]!=undefined) url+="?"+result["GetVars"];
		result["Complete"] =url;
		return result;
	},
	
	// () -> () :: EDB
	launchActions : function(){
		for (var i = 0; i < this.options.Actions.length; i++)
		this.options.Actions[i](this.actions.getModAndKELink.bind(this)());
	},
	
	launchKillerActions : function(){
		for (var i = 0; i < this.options.killerActions.length; i++)
		this.options.killerActions[i](this.actions.getModAndKELink.bind(this)());
	},
	
	// (String -> () ) -> () :: EDB
	addToLoad : function(fct,popup){
		if (popup) window.addEvent('onPopupLoad',fct);
		else window.addEvent('onFrameLoad',fct);
	},
	
	quickChange : function(params){
		lien = this.History.getCurrentLocation();
		if (!lien) lien = this.urlToArray(document.location.href)["Complete"];
		lien = lien + "?" + params;
		this.changePage(lien);
	}
});



//Initialisation du FrameLoader
frameLoader.implement(new Events);
frameLoader.implement(new Options);

window.addEvent('domready',function()
		{
		    window.fireEvent('onFrameLoad');
		    window.removeEvents('onFrameLoad');
		});
