[TITLE]Admin Kob-Eye | Importation d'un fichier[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
<div id="Container">
	<div id="Arbo">
		[BLOC Panneau][/BLOC]
	</div>
	<div id="Data">
		[BLOC Panneau]
			[BLOC Panneau|background:white;position:relative;overflow:hidden;padding:5px;]
				<div class="BigTitle" style="font-weight:bold;;-moz-border-radius:5px 5px 0px 0px;clear:both;">Propriétés</div>
					[IF [!Action!]="Modifier"]
						<h1>LISTE DES DOMAINES</h1>
						<ul>
						[STORPROC [![!domainlist!]:/%RC%!]|D|0|500]
							<li>[!Pos!] [!D!]
							[OBJ Parc|Domain|Do]
							[METHOD Do|Set][PARAM]Url[/PARAM][PARAM][!D!][/PARAM][/METHOD]
							[METHOD Do|AddParent][PARAM][!Query!][/PARAM][/METHOD]
							[IF [!Do::Save()!]]OK[ELSE]Erreur[/IF]
							</li>
						[/STORPROC]
						</ul>
					[ELSE]
						//Maintenant on ouvre le fichier en ecriture
						<form enctype="multipart/form-data" action="" method="post" name="frm" >
						<div class="Propriete">
							<div class="ProprieteTitre">Liste des domaine (1 par ligne) </div>
							<div class="ProprieteValeur">&nbsp;
								<textarea name="domainlist" style="width:500px;height:400px;"></textarea>
							</div>
						</div>
						<input type="hidden" name="Action" value="Modifier"/>
					[/IF]

			[/BLOC]
			<div class="JSFormButton" style="overflow:hidden;height:60px;margin-right:6px;">
				[IF [!action_import!]=]
					<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Annuler</a>
					<input type="submit" class="KEBouton"  value="Enregistrer" name="SaveObject" style="float:right;"/>
				[ELSE]
					<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Fermer</a>
				[/IF]
			</div>
			</form>
		[/BLOC]
	</div>
</div>