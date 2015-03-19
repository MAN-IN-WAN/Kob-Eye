[STORPROC [!Query!]|Objet|0|1]
	[IF [!Action!]=Exporter]
		[INI memory_limit]80M[/INI]
		[INI max_execution_time]3600[/INI]	
		[!Recherche:=[!Query!]/Contact!]
		//FILTRE
		[!RechPrefixe:=Rech!]
		[OBJ [!Module::Actuel::Nom!]|Contact|T]
		[STORPROC [!T::GetFilter()!]|P|0|100]
			[IF [![!RechPrefixe!]Filter[!P::Nom!]!]!=]
				[IF [!Shlass!]][!Recherche+=&!][ELSE][!Shlass:=1!][!Recherche+=/!][/IF]
				[!Recherche+=m.[!P::Nom!]=[![!RechPrefixe!]Filter[!P::Nom!]!]!]
			[/IF]
		[/STORPROC]
		//RECHERCHE PROPRIETE
		[STORPROC [!T::SearchOrder()!]|P|0|100]
			[IF [![!RechPrefixe!]Prop[!P::Nom!]!]!=&&[!P::Filter!]=]
				[IF [!Shlass!]][!Recherche+=&!][ELSE][!Shlass:=1!][!Recherche+=/!][/IF]
				[!Recherche+=m.[!P::Nom!]~[![!RechPrefixe!]Prop[!P::Nom!]!]!]
			[/IF]
		[/STORPROC]
		[COUNT [!Recherche!]|Nb]
		[!NbPass:=[!Nb:/1000!]!]
		[!NbPass+=1!]
		"Email","Os","Modele","Version","Specialite","Activite"
		[STORPROC [!NbPass!]|n]
		[STORPROC [!Recherche!]|C|[!n:*1000!]|1000][!C::Email!],[!C::Os!],[!C::Modele!],[!C::Version!],[!C::Specialite!],[!C::Activite!]
		[/STORPROC][/STORPROC]
	[ELSE]
		[TITLE]Admin Kob-Eye | Importation d'un fichier[/TITLE]
		<div class="ContenuEntete"> 
			[MODULE Systeme/Interfaces/BarreAction]
		</div>
		
		<div class="ContenuData"> 
			<div class="Panel"  style="position:absolute;top:0;bottom:0px;">
				<h1>Exportation d'un fichier</h1>
				//Maintenant on ouvre le fichier en ecriture
				<form enctype="multipart/form-data" action="/[!Lien!].csv" method="post" name="frm" >
					//On repete maintenant l interface pour chaque langue
					[OBJ Newsletter|Contact|Obj]
					[BLOC Rounded|background-color:#9A9EA0;color:#FFFFFF;|margin-bottom:2px;]<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;"><span style="margin-left:5px;">Recherche propriété</span>[/BLOC]
					[STORPROC [!Obj::SearchOrder()!]|Prop]
						//Dabord les proprietes de type varchar ,private,titre,password
						[IF [!Prop::Filter!]=]
							[SWITCH [!Prop::Type!]|=]
								[CASE text]
									[!Prop::Type:=!]
									[MODULE Systeme/Interfaces/Formulaire/ModifProprietes?Prop=[!Prop!]&Prefixe=RechProp&Valeur=[!RechProp[!Prop::Nom!]!]]
								[/CASE]
								[DEFAULT]
									[MODULE Systeme/Interfaces/Formulaire/ModifProprietes?Prop=[!Prop!]&Prefixe=RechProp&Valeur=[!RechProp[!Prop::Nom!]!]]
								[/DEFAULT]
							[/SWITCH]
						[/IF]
					[/STORPROC]
					[OBJ [!Module::Actuel::Nom!]|[!Obj::ObjectType!]|T]
					[STORPROC [!T::GetFilter()!]|P|0|100]
						[BLOC Rounded|background-color:#9A9EA0;color:#FFFFFF;|margin-bottom:2px;]<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;"><span style="margin-left:5px;">Filtres sur propriétés</span>[/BLOC]
						[LIMIT 0|100]
							[MODULE Systeme/Interfaces/Formulaire/ModifProprietes?Prefixe=RechFilter&Prop=[!P!]&Valeur=[!RechFilter[!P::Nom!]!]] 
						[/LIMIT]
					[/STORPROC]
					//VALIDER
					<div class="Bouton" style="width:100%;height:15px;">
						<b class="b1"></b>
						<b class="b2" style="text-align:center;display:inline;left:15px;right:15px;position:absolute;">
							<input type="submit" style="background-color:transparent;margin:0;padding:0;color:white;margin-left:15px;margin-right:15px;line-height:15px;height:15px;margin-top:-3px;" value="Exporter" name="Action"/>
						</b>
						<b class="b3" style="position:absolute;right:0px;"></b>
					</div>
				</form>
			</div>
		</div>
	[/IF]
[/STORPROC]
