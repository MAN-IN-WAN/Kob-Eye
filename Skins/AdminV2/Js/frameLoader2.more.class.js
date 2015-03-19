/**
   FRAMELOADER :: MORE
   Requetes automatiques en Ajax -- Plugins

   (c) 2009 - _expressiv

**/

/*http://www.howtocreate.co.uk/tutorials/javascript/browserwindow*/
// function getSize() {
//     var myWidth = 0, myHeight = 0;
//     if( typeof( window.innerWidth ) == 'number' ) {
// 	//Non-IE
// 	myWidth = window.innerWidth;
// 	myHeight = window.innerHeight;
//     } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
// 	//IE 6+ in 'standards compliant mode'
// 	myWidth = document.documentElement.clientWidth;
// 	myHeight = document.documentElement.clientHeight;
//     } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
// 	//IE 4 compatible
// 	myWidth = document.body.clientWidth;
// 	myHeight = document.body.clientHeight;
//     }
//     return [myWidth,myHeight];
// }



var frameLoaderPlus = new Class({

    Extends: frameLoader,
    initialize: function(opt,optMult){this.parent(opt,optMult);},
    currentPopup : [],

    /** Gestion d'une pile */
    stack: {
	content: [],

	date_picker: new DatePicker('.ncalendar', { pickerClass: 'datepicker_vista', timePicker:true, format:'Y-m-d H:i' }),

	// String x (Any -> ()) -> () 
	applyFilter : function (fname, fct){
	    var fctFilter = function(item){return fname==item.name};
	    var myArray   = this.content.filter(fctFilter);
	    for(var i=0;i<myArray.length;i++)
	    {
		if (fname == myArray[i].name)
		    fct(myArray[i].content);
	    }
	},

	// String -> () 
	removeFilter : function (fname){
	    var fctFilter= function(item){return fname!=item.name};
	    this.content = this.content.filter(fctFilter);
	},

	// String x Any -> ()
	addToStack   : function (fname,fcontent){
	    var elem = {name : fname, content : fcontent};
	    this.content[this.content.length] = elem;
	}
    },
    // () -> ()
    toggleMce : function(popup)
    {
	//this.initMce('AdminV2');
	/*	var t = this;
	    $$(".mceEditor").each(function(el){
		//tinyMCE.execCommand('mceAddControl',false,el.get('id'));
		new tinymce.Editor(el.get('id'),  {
			theme : "advanced",
			mode : "textareas",
			plugins : "safari,preview,bbcode,fullscreen,table,inlinepopups,paste,contextmenu,iespell,xhtmlxtras",
			extended_valid_elements : "a[class|name|href|target|title|onclick|rel]",
			theme_advanced_buttons1 : "bold,italic,underline,undo,redo,table,forecolor,removeformat,code,bullist,numlist,fullscreen,pastetext,pasteword,selectall,image",
			theme_advanced_buttons2 : "justifyleft,justifycenter,justifyright,justifyfull,|,indent,outdent,|,cite,link",
			theme_advanced_buttons3 : "",
			theme_advanced_buttons4 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_styles : "Code=codeStyle;Quote=quoteStyle;Popup=lienExterne",
			content_css : "/Skins/AdminV2/Css/bbcode.css",
			entity_encoding : "raw",
			add_unload_trigger : false,
			auto_cleanup_word : false,
			remove_linebreaks : false,
			force_br_newlines : false,
			convert_newlines_to_brs : false,
			convert_urls : false,
			button_tile_map : true,
			cleanup_serializer : 'bbcode',
			paste_create_paragraphs : false,
			paste_create_linebreaks : false,
			paste_use_dialog : true,
			paste_auto_cleanup_on_paste : false,
			paste_convert_middot_lists : false,
			paste_unindented_list_class : "unindentedList",
			paste_convert_headers_to_strong : true,
			paste_insert_word_content_callback : "convertWord",
			paste_remove_spans :"true",
			paste_remove_ps :"true",
			paste_remove_styles :"true",
			paste_strip_class_attributes :"all"
		}).render();
	    }.bind(this));
	    $$(".EditorFull").each(function(el){
		//tinyMCE.execCommand('mceAddControl',false,el.get('id'));
		new tinymce.Editor(el.get('id'), {
			theme : "advanced",
			mode : "textareas",
			plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
			extended_valid_elements : "a[class|name|href|target|title|onclick|rel]",
				theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
				theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
				theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_styles : "Code=codeStyle;Quote=quoteStyle;Popup=lienExterne",
			content_css : "/Skins/AdminV2/Css/bbcode.css",
			entity_encoding : "raw",
			add_unload_trigger : false,
			auto_cleanup_word : false,
			remove_linebreaks : false,
			force_br_newlines : true,
			convert_newlines_to_brs : false,
			convert_urls : false,
			button_tile_map : true,
			paste_create_paragraphs : false,
			paste_create_linebreaks : false,
			paste_use_dialog : true,
			paste_auto_cleanup_on_paste : false,
			paste_convert_middot_lists : false,
			paste_unindented_list_class : "unindentedList",
			paste_convert_headers_to_strong : true,
			paste_insert_word_content_callback : "convertWord",
			paste_remove_spans :"true",
			paste_remove_ps :"true",
			paste_remove_styles :"true",
			paste_strip_class_attributes :"all"
    		}).render();
	    }.bind(this));*/
	//this.addToLoad(laterFct,popup);
	
	$$(".EditorBBCode").each(function(el){
		CKEDITOR.replace(el.get('name'), {
			extraPlugins : 'bbcode',
			toolbar :
			[
				['Source', '-', 'Save','NewPage','-','Undo','Redo'],
				['Find','Replace','-','SelectAll','RemoveFormat'],
				['Link', 'Unlink', 'Image'],
				'/',
				[ 'Bold', 'Italic','Underline'],
				['NumberedList','BulletedList','-','Blockquote'],
				['TextColor', '-', 'Smiley','SpecialChar', '-', 'Maximize']
			]
		});
	}.bind(this));
	$$(".EditorFull").each(function(el){
		CKEDITOR.replace(el.get('name'), {
	    		toolbar: 'Basic'
		});
	}.bind(this));
    },

    // () -> ()
    toggleConditionals : function(popup)
    {
	var laterFct = function(popup)
	{
	    $$(".ChangeOnReload").each(function(el){
		el.addEvent('change',function(el,popup){
		    var f = el.getParents('form')[0];
		    f.getElements('input[type=submit]').each(function(el)
							    {
								el.set('name','NOSUBMIT');
							    });
		    //		    f.removeEvents('submit');
		    if (popup){
			$$('#Popup textarea.mceEditor').each(function(el){
			    if ($chk(el.getParent().getElement('span.mceEditor')))
				tinyMCE.execCommand('mceRemoveControl',false,el.get('id'));
			});
			opt = this.currentPopup;
			opt.data = f;
			this.openPopup(opt);
			datePickerController.cleanUp();	
		    }
		    else 
			this.changePage(undefined,undefined,undefined,undefined,f.method,f);
		}.bind(this,[el,popup]));
	    }.bind(this));
	}.bind(this,popup);
	this.addToLoad(laterFct,popup);
    },

    // () -> ()
    toggleCalendars : function(popup)
    {
	var laterFct = function(){
		new DatePicker('.ncalendar', { pickerClass: 'datepicker_vista', timePicker:true, format:'Y-m-d H:i:s', allowEmpty: true });
	    /*$$('.ncalendar').each(function(el){
		datePickerController.createDatePicker({id:el.get('id'),noFadeEffect:true,staticPos:false,dragDisabled:true});
	    });*/
	};
	this.addToLoad(laterFct,popup);	
    },

    // () -> ()
    toggleColorPickers : function(popup)
    {
	var laterFct = function(){
	    $$('.colorP').each(function(el) {
		var img = new Element('img');
		img.set('id',el.get('id')+"IMG");
		img.set('src','/Skins/AdminV2/Img/rainbow.png');
		img.setStyle('width','16px');
		img.setStyle('height','16px');
		el.getParent().adopt(img);
		var r = new MooRainbow(img.get('id'), {
		    'startColor': [58, 142, 246],
		    'onChange': function(color) {
			el.value = color.hex;
			el.setStyle('background-color',color.hex);
		    }
		});
	    });
	};
	this.addToLoad(laterFct,popup);	
    },

    setSwitchers: function (selector,functionChoose,functionLeave)
    {
	var laterFct = function(){
	    $$(selector).each(function(el){
		el.addEvent('click',this.actions.stopEvThen(function(el,sel,fc,fl){
		    fc(el);
		    $$(sel).each(function(el2){if (el2!=this) fl(el2);}.bind(el));
		}.bind(this,[el,selector, functionChoose, functionLeave])));
	    }.bind(this));
	}.bind(this);
	this.addToLoad(laterFct);
    },

    checkForm: function (formObject,quValue,jsonFile,popup)
    {
	var laterFct = function(){
		//if (formObject==null){
		alert('check');
		formObject.removeEvents('submit');
		formObject.addEvent('submit',this.actions.stopEvThen(function(f,q,j,popup){

		//On ajoute un élément qui contient la requete
		var inHidden = new Element('input');
		inHidden.set('type','hidden');
		inHidden.set('name','Qu');
		inHidden.set('value',q);
		f.grab(inHidden);
		var jSon = new Request.JSON({method:'post',data:f,url:j});
		jSon.addEvent('success',function(j,f,popup){
		    this.options.Overlay.stop();
		    this.busy = false;
		    if (j.form_result) 
		    {
			f.removeEvents('submit');
			var T = this.urlToArray(f.action,".htm");
			f.getElements("input[type='submit']").each(function(el){el.type="hidden";});
			if (!this.busy && !popup) this.fireEvent('onChanged',T["Complete"]);
			this.changePage(T["Complete"],this.options.Container,undefined,undefined,f.method,f,popup);
		    }
		    else{ 
			var ErrorDiv = f.getElement(".Errors");
			ErrorDiv.getElement('ul').empty();
			ErrorDiv.setStyle('display','block');
			for(var i=0;i<j.errors.length;i++)
			{
			    var li = new Element('li');
			    li.set('html',j.errors[i]);
			    ErrorDiv.getElement('ul').grab(li);
			}
			if (!popup) myFx = new Fx.Scroll($$('#Container .p22')[0],{
			    offset: {'x': -200, 'y': -50}}).toTop();
			else  myFx = new Fx.Scroll($$('#Popup')[0],{
			    offset: {'x': -200, 'y': -50}}).toTop();
			for(var i=0;i<j.errors_field.length;i++)
			{
			    $$(".Champ"+j.errors_field[i]).removeClass('ChampObligatoire');
			    $$(".Champ"+j.errors_field[i]).addClass('ChampErreur');
			}
		    }
		}.bindWithEvent(this,[f,popup]));
		this.options.Overlay.stop();
		if (!this.busy)
		    {
			jSon.send();
			this.busy = true;
		    }
	    }.bind(this,[formObject,quValue,jsonFile,popup])));
	}.bind(this);
	this.addToLoad(laterFct,popup);
    },

    closePopup : function(mce)
    {
	if ($('PopupContainer').hasClass('Full'))
	{
	    $('PopupContainer').removeClass('Full');
	    var  t = $('Popup').getStyle('height');
	    $('Popup').setStyle('height',$('Popup')._oldHeight);
	    $('Popup')._oldHeight = t;				    
	}
	$('Popup').empty();
	$('PopupOverlay').setStyle('display','none');
	$('PopupContainer').setStyle('display','none');
    },

    openPopup: function(options)
    {
	$('Popup').set('html','<div style="text-align:center;margin-top:100px;">Veuillez patienter...</div>');
	$('PopupOverlay').setStyle('display','block');
	$('PopupContainer').setStyle('display','block');
	var Ajx = new Request.HTML({url:options.url + "&popup=true",
				    update:$('Popup'),
				    noCache:true,
				    method:'post',
				    data:options.data});
	this.currentPopup = options;
		    Ajx.addEvent('success',function(cL,reload,params){
			this.options.killerActions = [];
			if (reload) this.options.killerActions[this.options.killerActions.length]=
			    function(lien){
				this.closePopup(true);
				this.options.killerActions.pop();
				this.quickChange(params);
			    }.bind(this);
			else this.options.killerActions[this.options.killerActions.length]=
			    function(lien){
				this.closePopup(true);
				this.options.Overlay.stop();
				this.options.killerActions.pop();
			    }.bind(this);
			myFx = new Fx.Scroll($$('#Popup')[0],{
			    offset: {'x': -200, 'y': -50}}).toTop();
			this.scanLink('Popup','Popup',cL,true);
			window.fireEvent('onPopupLoad');
			window.removeEvents('onPopupLoad');
		    }.bind(this,[options.changeLink,options.reload,options.params]));
		    Ajx.send();
    },

    popUpPrel : function()
    {
	$('PopupContainer').makeDraggable({handle:$$('#PopupContainer .BarreTitre')[0]});
	//Affectation des boutons
	$('PopupMaximize').addEvent('click',function(e){
	    $('PopupContainer').toggleClass('Full');
	    var  w = getSize();
	    if (!$('Popup')._oldHeight){
		$('Popup')._oldHeight = $('Popup').getStyle('height');	
		$('Popup').setStyle('height',w[1]-50);			
	    }
	    else{
		var  t = $('Popup').getStyle('height');
		$('Popup').setStyle('height',$('Popup')._oldHeight);
		$('Popup')._oldHeight = t;
	    }
	    e.stop();
	    return false;
	});
	$('PopupClose').addEvent('click',function(e){
	    this.options.killerActions.pop();
	    e.stop();
	    this.closePopup(true);
	}.bind(this));
    },

    makePopup: function()
    {
	var laterFct = function(){
	    $$('.makePopup').each(function(link) {
		if (link._passe) return;
		link._passe = true;
		link.addEvent('click',this.actions.stopEvThen(function(urlTarget){
		    var myLink = urlTarget.split('::');
		    urlTarget = myLink[0];
		    if (myLink.length > 2 && myLink[2] == "true") 
			r = true;
		    else
			r = false;
		    if (myLink.length > 3)
			p = myLink[3];
		    else p = "";
		    this.openPopup({url : urlTarget,
				    changeLink : myLink[1],
				    reload : r,
				    params :p});
		}.bind(this,link.rel)));
	    }.bind(this));
	}.bind(this);
	this.addToLoad(laterFct);
    },

    makeUpload : function(containerId,listId,ke,mod,obj,popup)
    {
	var laterFct = function(containerId,listId,ke,mod,obj){
	    var up = new FancyUpload2($(containerId).getElement(".Content"),$(listId),{
		path : '/Skins/AdminV2/Js/Swiff.Uploader.swf',
		multiple : false,
		verbose : false,
		target: $(containerId).getElement(".Browse"),
		method : 'get',
		url : '/Systeme/Interfaces/Formulaire/Upload.htm?Module='+mod+'&obj='+obj+'&KE_SESSID='+ke,
		onLoad: function(containerId,target) {
		    $(containerId).getElement('.Toggle').removeEvents('click');
		    $(containerId).getElement('.Toggle').addEvent('click',function(e){
			e.stop();
			this.getElement('.Content').setStyle('display','block');
			this.getElement('.Result').setStyle('display','none');
			this.getElement('.Result input').set('value','');
		    }.bind($(containerId)));
		    target.addEvents({
			click: function() {
			    return false;
			},
			mouseenter: function() {
			    this.addClass('hover');
			},
			mouseleave: function() {
			    this.removeClass('hover');
			    this.blur();
			},
			mousedown: function() {
			    this.focus();
			}
		    });
		}.pass([containerId,$(containerId).getElement(".Browse")]),
		onSelectSuccess : function(cId){
		    up.start();
		}.pass(containerId),
		onFileSuccess: function(containerId, response) {
		    var json = new Hash(JSON.decode(response, true) || {});
		    this.getElement(".Content").setStyle("display","none");
		    this.getElement(".Result").setStyle("display","block");
		    var r= this.getElement(".Result span");
		    r.set('html',json.url);
		    var i= this.getElement(".Result input");
		    i.set('value',json.url);
		}.bind($(containerId)),
		onFileError: function(file) {
			alert(file.errorMessage);
		}.bind($(containerId))

	    });	
	}.bind(this,[containerId,listId,ke,mod,obj]);
	this.addToLoad(laterFct,popup);
    }
});