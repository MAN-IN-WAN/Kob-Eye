[IF [!SaveObject!]!=]
	//Alors on enregistre les proprietes
	[STORPROC [!CONF::GENERAL::LANGUAGE!]|Lang]
		[STORPROC [!O::Proprietes([!Key!])!]|Prop]
			[METHOD O|Set]
				[PARAM][!Prop::Nom!][/PARAM]
				[PARAM][!Form_[!Prop::Nom!]!][/PARAM]
			[/METHOD]
		[/STORPROC]
	[/STORPROC]
	//Sauvegarde l objet
	[IF [!O::Verify!]]
		[IF [!Clone!]>1]
			[STORPROC [!Clone:-1!]|C]
				[!Ob:=[!O::getClone()!]!]
				[METHOD Ob|Save][/METHOD]
			[/STORPROC]
		[/IF]
		[METHOD O|Save][/METHOD]
	[ELSE]
		<ul class="Error">
		<li><h1>Erreur d'enregistrement [!O::ObjectType!]</h1></li>
		[STORPROC [!O::Error!]|E]
			<li>[!E::Message!]</li>
			//Generation d une variable d error pour informer le champ en question
			[!C_[!E::Prop!]Cv_Error:=1!]
		[/STORPROC]
		</ul>		
	[/IF]
[/IF]
<div style="overflow:hidden;position:relative;display:block;">
	<div style="overflow:hidden;width:100%;float:left;margin:7px;">
		[STORPROC [!CONF::GENERAL::LANGUAGE!]|Lang]
			[INFO [!Query!]|HistoObj]
			[STORPROC [!HistoObj::Historique!]|Histo|0|100]
				[IF [!Pos!]!=[!NbResult!]]
					[!BackUrl:=[!HistoObj::Module!]/[!Histo::DataSource!]/[!Histo::Value!]!]
				[/IF]
			[/STORPROC]
			[!Test:=[!O::getElements([!Key!])!]!]
			[STORPROC [!Test!]|C]
				<div class="BigTitle">Saisie des propri&eacute;t&eacute;s de langue [!Key!]</div>
				[LIMIT 0|1000]
					[BLOC Panneau|background:white;position:relative;overflow:hidden;padding:0px;padding-bottom:5px;]
						<div class="BigTitle" style="text-align:center;font-variant:small-caps;-moz-border-radius:5px 5px 0px 0px;background-color:#666;">[!Key!]</div>
						<div style="margin:5px">
						[STORPROC [!C!]|T]
							[!K:=[!Key!]!]
							[STORPROC [!T!]|P]
							[IF [!K!]=media||[!K!]=block]
								<div style="clear:both"></div>
							[/IF]
							[LIMIT 0|1000]
								[SWITCH [!P::type!]|=]
									[CASE fkey][/CASE]
									[CASE rkey][/CASE]
									[DEFAULT]
										[MODULE Systeme/Interfaces/Formulaire/ModifProprietes?ObjectTT=[!O::ObjectType!]&DisplayReload=[!DisplayReload!]&Prop=[!O::getProperty([!P::name!])!]&Type=[!Type!]&&O=[!O!]]
									[/DEFAULT]
								[/SWITCH]
							[/LIMIT]
							[/STORPROC]
						[/STORPROC]
						</div>
					[/BLOC]
				[/LIMIT]
			[/STORPROC]
		[/STORPROC]
	</div>
</div>
