[IF [!EmailNewsletter!]]
	[IF [!Desabo!]]
		[STORPROC Newsletter/Contact/Email=[!EmailNewsletter!]|Con|0|1]
			[METHOD Con|DelParent][PARAM]Newsletter/GroupeEnvoi/1[/PARAM][/METHOD]
			[METHOD Con|DelParent][PARAM]Newsletter/GroupeEnvoi/2[/PARAM][/METHOD]
			[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/3[/PARAM][/METHOD]
			[METHOD Con|Save][/METHOD]
			[!LeMessage:=Vous êtes désabonné(e)!]
		[/STORPROC]
	[ELSE]
		[COUNT Newsletter/Contact/Email=[!EmailNewsletter!]|C]
		[IF [!C!]=0]
			[OBJ Newsletter|Contact|Con]
			[METHOD Con|Set]
				[PARAM]Email[/PARAM][PARAM][!EmailNewsletter!][/PARAM]
			[/METHOD]
			[METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
			[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/2[/PARAM][/METHOD]
			[METHOD Con|Save][/METHOD]
			[!LeMessage:=Votre inscription a bien été prise en compte-[!EmailNewsletter!]!]
		[ELSE]
 			[IF [!LeMessage!]!=]
			[STORPROC Newsletter/Contact/Email=[!EmailNewsletter!]|Con|0|1]
				[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/2[/PARAM][/METHOD]
				[METHOD Con|Save][/METHOD]
				[!LeMessage:=Vous êtes déjà inscrit(e)!]
			[/STORPROC]
			[/IF]
		[/IF]

	[/IF]
[/IF]
<div class="Titre">Offres, nouveauté, promotions...</div>
<div class="SousTitre">Inscrivez-vous à la newsletter</div>
//ABONNEMENT
<div class="input-append">
	<input class="col-md-8" id="EmailNewsletter" name="EmailNewsletter" type="text" placeholder="Saisissez votre adresse mail" value="[!EmailNewsletter!]">
	<a class="btn NewsletterAccueil" id="inscriptionNewsletter">Ok</a>
</div>
<div class="btnlien"><a id="desinscriptionNewsletter" href="#nogo">Se désabonner</a></div>
<script type="text/javascript">
	$('#inscriptionNewsletter').click(function (e){
		var mail = $('#EmailNewsletter').val();
		$('#myModalLabel').html("Newsletter");
		$('#lemodal').modal({
			keyboard: false,
			remote: '/Newsletter/InscriptionOK.htm?EmailNewsletter='+mail
		}).modal('show');
	});
	$('#desinscriptionNewsletter').click(function (e){
		$('#myModalLabel').html("Newsletter");
		var mail = $('#EmailNewsletter').val();
		$('#lemodal').modal({
			keyboard: false,
			remote: '/Newsletter/InscriptionOK.htm?EmailNewsletter='+mail+'&Desabo=1'
		}).modal('show');
	});
</script>
