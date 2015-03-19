[TITLE]Admin Kob-Eye | [!Action!] O [!Form_[!Prop::Nom!]_explore!][/TITLE]
//SAUVEGARDE DE L URL REFERENTE
[IF [!LAST_URL!]=]
	[!LAST_URL:=[!Systeme::Connection::LastUrl!]!]
[/IF]
[BLOC Panneau|overflow:auto;]
	<div id="errorscontainer"></div>
	<div class="JSFormButton" style="overflow:hidden;height:25px;margin-right:6px;">
		<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Annuler</a>
		<input type="submit" class="KEBouton SubmitButton"  VALUE="Enregistrer" name="SaveObject" style="float:right;" rel="save" checkUrl="/[!Query!]/SaveJson.xml" redirectUrl="/[!Query!]"/>
		[IF [!Action!]=Ajouter&&[!popup!]!=true]
			<input type="submit" class="KEBouton SubmitButton" value="Enregistrer et ajouter &agrave; nouveau" name="SaveObject" style="float:right;margin-right:10px;"  rel="save" checkUrl="/[!Query!]/SaveJson.xml" redirectUrl="/[!Lien!]"/>
		[/IF]
	</div>
	[MODULE Systeme/Interfaces/Formulaire/Form?O=[!O!]]
	<div class="JSFormButton" style="overflow:hidden;height:60px;margin-right:6px;">
		<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Annuler</a>
		<input type="submit" class="KEBouton SubmitButton"  value="Enregistrer" name="SaveObject" style="float:right;" rel="save" checkUrl="/[!Query!]/SaveJson.xml" redirectUrl="/[!Query!]"/>
		[IF [!Action!]=Ajouter&&[!popup!]!=true]
			<input type="submit" class="KEBouton SubmitButton" value="Enregistrer et ajouter &agrave; nouveau" name="SaveObject" style="float:right;margin-right:10px;"  rel="save" checkUrl="/[!Query!]/SaveJson.xml" redirectUrl="/[!Lien!]"/>
		[/IF]
	</div>
	<input type="hidden" name="LAST_URL" value="[!LAST_URL!]" />
	<script type="text/javascript">
		Fl.toggleMce();
		Fl.toggleConditionals();
		Fl.toggleCalendars();
		Fl.toggleColorPickers();
	</script>
[/BLOC]