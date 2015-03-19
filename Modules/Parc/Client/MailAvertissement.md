[TITLE]Admin Kob-Eye | Importation d'un fichier[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
<div id="Container">
	<div id="Arbo">
		[BLOC Panneau][/BLOC]
	</div>
	<div id="Data">
		<form enctype="multipart/form-data" action="" method="post" name="frm" >
		[BLOC Panneau]

[STORPROC [!Query!]|C]
	[IF [!MAILSEND!]]
		[LIB PHPMailer|M]
		[METHOD M|setFrom][PARAM]contact@abtel.fr[/PARAM][/METHOD]
		[METHOD M|addAddress][PARAM][!C::Email!][/PARAM][/METHOD]
		[METHOD M|MsgHTML][PARAM]
[!CONTENT!]
		[/PARAM][/METHOD]
		[METHOD M|set][PARAM]Subject[/PARAM][PARAM][!SUBJECT!][/PARAM][/METHOD]
		[METHOD M|Send][/METHOD]
		<div class="success">Mail envoyé</div>
					<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Fermer</a>
	[ELSE]
		[BLOC Panneau|background:white;position:relative;overflow:hidden;padding:5px;]
			<div class="Propriete">
				<div class="ProprieteTitre">Sujet</div>
				<div class="ProprieteValeur">&nbsp;
					<input name="SUBJECT" value="[ABTEL] Avertissement hébergement" size="100" />
				</div>
			</div>
			<div class="Propriete">
				<div class="ProprieteTitre">Message : </div>
				<div class="ProprieteValeur">&nbsp;
					<textarea name="CONTENT" cols="30" lines="15" style="width:95%;height:200px;"></textarea>
				</div>
			</div>
			<input type="hidden" name="MAILSEND" value="Envoyer"/>
		[/BLOC]
			<div class="JSFormButton" style="overflow:hidden;height:60px;margin-right:6px;">
				<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Annuler</a>
				<input type="submit" class="KEBouton"  value="Enregistrer" name="SaveObject" style="float:right;"/>
			</div>
	[/IF]
[/STORPROC]
		[/BLOC]
		</form>
	</div>
</div>
