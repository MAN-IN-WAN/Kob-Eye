var Fl = null;
var CurrentMod = "Systeme";
window.addEvent('domready', function(){
	launchSite();
});

function launchSite(){
	var MyOverlay = {start : function(t){
			if (!t||!$(t)) t = 'Global';
			$('Overlay').setStyles($(t).getCoordinates());
			$('Overlay').setStyle('display','block');
			$('Overlay2').setStyle('display','block');
		},stop : function(){
			$('Overlay').setStyle('display','none');
			$('Overlay2').setStyle('display','none');
		}
	};		  
	Fl = new frameLoaderPlus({
		Overlay:MyOverlay,
		Icon:false,
		Container:'Global',
		MultiBox:'true',
		DefaultPage:'/Systeme.htm',
		History:true,
		OnlySubmitButton:true,
		Title:'Administration Kob-Eye',
		Actions: [
			function(tabLien){
				if (CurrentMod != tabLien[0]){
					t = new Request.HTML({update:'ArabesqueEntete',onSuccess:function(){Fl.scanLink('ArabesqueEntete');$$("ul.tabs li[class=ModuleAccueil]")[0].addClass("Selected");}});
					t.get('/Systeme/Interfaces/ObjectOnglets.json?Module='+tabLien[0]+'&Lnk='+tabLien[3-2]+'&FirstObject='+tabLien[3-2].split('/')[3-2]);
					t.send();
					CurrentMod = tabLien[0];
					return ;
				}
				if (tabLien[3-2].split('/').length == 1){
					$$("ul.tabs li").each(function(el){
					if (el.hasClass('ModuleAccueil')) el.addClass("Selected");
					else el.removeClass("Selected");
					});
				}else{
					var obj = tabLien[3-2].split('/')[3-2];
					$$("ul.tabs li").each(function(el){
						if (el.hasClass('Module'+obj)) el.addClass("Selected");
						else el.removeClass("Selected");
					});
				}
				datePickerController.cleanUp();
			},function(){Fl.makePopup()}
		]
	});
	
	MyOverlay.stop();
	$('Overlay').setStyles({
		'top':'145px',
		'opacity':'0.5',
		'background-image':'none',
		'background':'white'
	}); 
	$('Global').setStyle("display","block");
	$("Entete").setStyle("height","117px");
	$$("ul.tabs").setStyle("display","block");
	document._FLNC = Fl;
	var BugCorrection = false;
	//Fl.initMce("AdminV2");
	//Fl.initMceFull("AdminV2");
	Fl.prepareMb({});
	Fl.scanLink();
	Fl.popUpPrel();
	new MorphList('BarreModules');
}