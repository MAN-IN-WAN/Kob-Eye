<div class="EnteteComposant EnteteNewsletter">
	Newsletter
</div>
<div class="ContenuComposant ContenuComposantNewsletter">
	<div class="InscriptionNeswletter">
		Inscrivez votre adresse e-mail pour recevoir la newsletter des Vignerons de la Voie Romaine
	</div>
	[IF [!Envoi!]=OK&&[!contactmail!]!=Votre adresse e-mail...]
		[COUNT Newsletter/Contact/Email=[!contactmail!]|C]
		[IF [!C!]=0]
			[OBJ Newsletter|Contact|Con]
			[METHOD Con|Set]
				[PARAM]Email[/PARAM][PARAM][!contactmail!][/PARAM]
			[/METHOD]
			[METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
			[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/2[/PARAM][/METHOD]
			[METHOD Con|Save][/METHOD]
			<div class="MessageNewsletter">Votre inscription a bien été prise en compte.</div>
		[ELSE]
			<div class="MessageNewsletter">Vous recevez déjà la newsletter !</div>
		[/IF]
	[ELSE]
		<form method="post" action="/[!Lien!]">
			<input type="text" name="contactmail" id="InscriptionNewsletterMail" />
			<input type="submit" name="Envoi" value="OK" />
		</form>
	[/IF]
</div>

// Surcouche JS
<script type="text/javascript">
	window.addEvent('domready', function() {
		FieldDefaultText( $('InscriptionNewsletterMail'), 'Votre adresse e-mail...' );
	});
</script>