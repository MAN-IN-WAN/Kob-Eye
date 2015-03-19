
[TITLE]Admin Kob-Eye | Importation de menus[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
<div id="Container">
	<div id="Arbo">
		[BLOC Panneau]
			<a href="/[!Lien!]">REFRESH</a>
		[/BLOC]
	</div>
	<div id="Data">
		[BLOC Panneau]
			[BLOC Panneau|background:white;position:relative;overflow:hidden;padding:5px;]
				<div class="BigTitle" style="font-weight:bold;;-moz-border-radius:5px 5px 0px 0px;clear:both;">Données xml</div>
				<textarea name="data" style="width:100%;height:200px;">[!data!]</textarea>
					
			[/BLOC]
			[IF [!SaveObject!]=Enregistrer]
				[BLOC Panneau|background:white;position:relative;overflow:hidden;padding:5px;]
					<div class="BigTitle" style="font-weight:bold;;-moz-border-radius:5px 5px 0px 0px;clear:both;">Résultat</div>
					<pre style="display:block;position:relative;width:100%;height:200px;overflow:auto;background-color: white">
						[STORPROC [!Query!]|G]
							//[METHOD G|importMenus][PARAM][!Utils::escape([!data!])!][/PARAM][/METHOD]
							[!G::importMenus([*data*])!]
						[/STORPROC]
					</pre>
				[/BLOC]
			[/IF]
			<div class="JSFormButton" style="overflow:hidden;height:60px;margin-right:6px;">
				[IF [!action_import!]=]
					<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Annuler</a>
					<input type="submit" class="KEBouton"  value="Enregistrer" name="SaveObject" style="float:right;"/>
				[ELSE]
					<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Fermer</a>
				[/IF]
			</div>
		[/BLOC]
	</div>
</div>
