[TITLE]Admin Kob-Eye | Informations Module[/TITLE]
[IF [!DateDebut!]=][!DateDebut:=[!Date::getDate(Y-m-d,[!TMS::Now:-2592000!])!]!][/IF]
[IF [!DateFin!]=][!DateFin:=[!Date::getDate(Y-m-d,[!TMS::Now!])!]!][/IF]

//[MODULE Systeme/Interfaces/FilAriane]

<div id="Container" style="top:0;">
    //[MODULE Systeme/Interfaces/BarreModule]
    //Liste des blocs icones
	<div class="CatTitle">
	    <span style="margin-left:5px;">Acc&egrave;s rapides</span>
	</div>
	[STORPROC [!Module::Actuel::Db::AccessPoint!]|ObjClass]
	    <a href="/[!Module::Actuel::Nom!]/[!ObjClass::titre!]">
		<div class="BlocIconePub" >
		    <div style="background:url([!ObjClass::Icon!]) no-repeat white;height:100px;">
		    <h1 style="float:right;">[IF [!ObjClass::Description!]!=][!ObjClass::Description!][ELSE][!ObjClass::titre!][/IF]</h1>
		    </div>
		</div>
	    </a>	
	    
	[/STORPROC]
	<div style="clear:both;"></div>
	[STORPROC [!Module::Actuel::Db::Dico!]|ObjClass]
	    [BLOC Rounded|background:#8C8C8C;color:#FFFFFF;|margin-bottom:5px;margin-top:30px;]
		<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;"/>
		<span style="margin-left:5px;">Dictionnaires</span>
		

	    [/BLOC]
	    [LIMIT 0|100]
		<a href="/[!Module::Actuel::Nom!]/[!ObjClass::titre!]">
		    <div class="BlocIconePub Dico" style="">
			<div style="background:url([!ObjClass::Icon!]) no-repeat white;height:55px;">
			<div>[IF [!ObjClass::Description!]!=][!ObjClass::Description!][ELSE][!ObjClass::titre!][/IF]</div> 
			</div>
		    </div>
		</a>
	    [/LIMIT]
	[/STORPROC]
	<div style="clear:both;"></div>

	//Affichage d'une statistique
	[IF [!CONF::MODULE::[!Module::Actuel::Nom!]::TRIGGER!]]
		[BLOC Rounded|background:#8C8C8C;color:#FFFFFF;|margin-bottom:5px;margin-top:30px;]
			<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;"/>
			<span style="margin-left:5px;">Statistiques</span>
		[/BLOC]
		//AFFICHAGE DES COURBES TOTAUX
		[STATS [!Module::Actuel::Nom!]|S]
		[STORPROC [!S!]|S1][/STORPROC]
		[STORPROC [!S1::Functions!]|F|0|2]
			//AFFICHAGE DU RECAPITLATIF DES TOTAUX
			[IF [!F::Type!]=Total]
				[MODULE Systeme/Statistiques/Total?Module=[!Module::Actuel::Nom!]&Trigger=[!S1::Name!]&Fonction=[!F::Name!]&Titre=[!F::Name!]&Id=[!Pos!]&DateDebut=[!DateDebut!]&DateFin=[!DateFin!]]
			[/IF]
			//AFFICHAGE DES CLASSEMENTS
			[IF [!F::Type!]=Classement]
				[MODULE Systeme/Statistiques/Classement?Module=[!Module::Actuel::Nom!]&Trigger=[!S1::Name!]&Fonction=[!F::Name!]&Titre=[!F::Name!]&Id=[!Pos!]&DateDebut=[!DateDebut!]&DateFin=[!DateFin!]]
			[/IF]
		[/STORPROC]
	[/IF]
</div>
