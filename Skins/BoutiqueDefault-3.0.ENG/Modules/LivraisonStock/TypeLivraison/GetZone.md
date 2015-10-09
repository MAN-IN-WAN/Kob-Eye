[TITLE]Admin Kob-Eye | Récupération d'une zone à partir du pays et du code postal[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
<div id="Container">
	<form action="" method="post" name="rech[!Test::TypeChild!]" class="FormRech">
		<div id="Arbo">
			[BLOC Panneau]
				[BLOC Rounded|background:#057390;color:#FFFFFF;|margin-bottom:5px;]
					<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
					<span style="margin-left:5px;">Recherche de zone</span>
				[/BLOC]
				<div class="ProprieteModif">
					<div class="ProprieteTitreModif  PropModif " style="width:25%;float:left;">Pays</div>
					<div class="ProprieteValeurModif">
					<input type="text" class="Champ" name="Form_Pays" value="[!Form_Pays!]" >
					</div>
				</div>
				<div class="ProprieteModif">
					<div class="ProprieteTitreModif  PropModif " style="width:25%;float:left;">CodePostal</div>
					<div class="ProprieteValeurModif">
					<input type="text" class="Champ" name="Form_CodePostal" value="[!Form_CodePostal!]" >
					</div>
				</div>
				<div class="JSFormButton" style="overflow:hidden;height:60px;margin-right:6px;">
					<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Retour</a>
					<input type="submit" class="KEBouton SubmitButton"  value="Rechercher" name="SaveObject" style="float:right;"/>
				</div>
				<input type="hidden" name="LAST_URL" value="[!LAST_URL!]" />
			[/BLOC]
		</div>
		<div id="Data">
			[BLOC Panneau]
				[BLOC Rounded|background:#057390;color:#FFFFFF;|margin-bottom:5px;]
					<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
					<span style="margin-left:5px;">Résultat de recherche [!Form_Pays!] | [!Form_CodePostal!]</span>
				[/BLOC]
				<h1>RESULTATS</h1>
				<ul>
				[STORPROC [!Query!]|Z][/STORPROC]
				[STORPROC [!Z::GetZone([!Form_Pays!],[!Form_CodePostal!])!]|Zo]
					<li>[!Zo::ObjectType!] [!Zo::Code!]</li>
				[/STORPROC]
				</ul>
			[/BLOC]
		</div>
	</form>
</div>
