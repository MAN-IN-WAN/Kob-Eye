[MODULE Systeme/Interfaces/FilAriane]
<div id="Container">
[INFO [!Query!]|Test]
[SWITCH [!Test::TypeSearch!]|=]
	[CASE Child]
		//<form [IF [!Test::Reflexive!]=]target="Liste[!Test::TypeChild!]"[/IF] name="liste" method="post">
		[OBJ [!Module::Actuel::Nom!]|[!Test::TypeChild!]|o]
		[TITLE]Admin Kob-Eye | Liste des objets de type [!Test::TypeChild!][/TITLE]
			<div id="Arbo">
				[BLOC Panneau]
					//Bouton ajouter
					<a href="/[!Query!]/Ajouter" class="KEBouton">Ajouter [!Test::TypeChild!]</a>
					<div style="position:relative;clear:both;">
						//Formulaire de recherche exacte
						[MODULE Systeme/Interfaces/Formulaire/Recherche?Obj=[!o!]]
					</div>
				[/BLOC]
			</div>
			<div id="Data">
				[!Filter:=!]
				[BLOC Panneau]
				//Gestion des containers
				[STORPROC [!o::typesEnfant!]|Enf]
					[IF [!Enf::container!]=1]
					[IF [!Test::ObjectType!]==[!Enf::Titre!]]
					[!Q:=[!Module::Actuel::Nom!]!]
						[STORPROC [!Test::Historique!]|T|0|100]
							[IF [!Pos!]<[!NbResult:-1!]]
						[!Q+=/[!T::DataSource!]!]
						[IF [!T::Value!]!=]
							[!Q+=/[!T::Value!]!]
							[/IF]	
							[/IF]	
						[/STORPROC]
						[STORPROC [!Q!]|P|0|1]
						<div class="Bouton" style="width:100%;">
						<b class="b1"></b>
						<b class="b2" style="text-align:center;display:inline;left:15px;right:15px;position:absolute;">
							<a href="/[!Module::Actuel::Nom!]/[!P::ObjectType!]/[!P::Id!]" style="width:100%;">Retour au [!o::ObjectType!]</a>
						</b>
						<b class="b3" style="position:absolute;right:0px;"></b>
						</div>				    
						[/STORPROC]
					[ELSE]
						[STORPROC [!o::typesParent!]|Par]
						[IF [!Par::Title!]==[!Enf::Title!]]
							[!Filter:=[!Par::Champ!]!]
						[/IF]
						[/STORPROC]
					[/IF]
					[/IF]
				[/STORPROC]
					[IF [!Test::Reflexive!]]
						[MODULE Systeme/Interfaces/Arborescence?Chemin=[!Query!]&NbChamp=4&Type=Full&TypeEnf=[!Test::TypeChild!]&Visit[!Test::TypeChild!]=[!Visit[!Test::TypeChild!]!]]
					[ELSE]
						[MODULE Systeme/Interfaces/Liste?Chemin=[!Query!]&Type=Full&Filter=[!Filter!]&NbChamp=4]
					[/IF]
				[/BLOC]
			</div>
		//</form>
	[/CASE]
	[CASE Direct]
		<div id="Arbo">
			//<form [IF [!Test::Reflexive!]=]target="Liste[!Test::TypeChild!]"[/IF] name="liste" method="post">
			[BLOC Panneau]
				[INFO [!Query!]|I]
				//Bouton ajouter
				<a href="/[!I::LastChild!]/Ajouter" class="KEBouton">Ajouter [!I::TypeChild!]</a>
				[MODULE Systeme/Interfaces/AdminNav]
			[/BLOC]
			//</form>
		</div>
		<div id="Data">
				//Detection de l existence d une priorite sur un element
				[INFO [!Query!]|Test]
				[!Default:=Prop!]
				[STORPROC [!Test::typesEnfant!]|Enf]
					[IF [!Enf::Behaviour!]="List"][!Default:=[!Enf::Titre!]!][/IF]
				[/STORPROC]
				[IF [!NavObj!]=][!NavObj:=[!Default!]!][/IF]
				[STORPROC [!Query!]|Obj|0|1]
					[IF [!NavObj!]=||[!NavObj!]=Prop]
						[MODULE Systeme/Interfaces/Etat?Obj=[!Obj!]]
					[ELSE]
						[BLOC Rounded|background:#057390;color:#FFFFFF;|margin-bottom:5px;]
							<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
							<span style="margin-left:5px;">[!Obj::getFirstSearchOrder!] </span>
						[/BLOC]
						//Si c est une liste ou bien une arborescence
						[OBJ [!Module::Actuel::Nom!]|[!NavObj!]|Test]
						[IF [!Test::Reflexive!]]
							//C est une arborescence
							[MODULE Systeme/Interfaces/Arborescence?Chemin=[!Query!]/[!NavObj!]&TypeEnf=[!NavObj!]&Requete=[!Query!]&Visit[!QueryLastObject!]=[!VisitQuery!]&Type=Full]
						[ELSE]
							//Si il y a un historique C est une liste
							[MODULE Systeme/Interfaces/Liste?Chemin=[!Query!]/[!NavObj!]&TypeEnf=[!NavObj!]&Type=Full&Top=23&Bottom=0&NbChamp=4]
						[/IF]
					[/IF]
				[/STORPROC]
		</div>
	[/CASE]
	[DEFAULT][/DEFAULT]
[/SWITCH]
</div>
