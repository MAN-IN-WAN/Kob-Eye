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
<form action="/" method="post" enctype="application/x-www-form-urlencoded">
	<div class="input-append">
   		<input class="span9" id="EmailNewsletter" name="EmailNewsletter" type="text" placeholder="Saisissez votre adresse mail" value="[!EmailNewsletter!]">
    		<button class="btn"  name="Abo" rel="Shadowbox;">Ok</button>
		<div class="btnlien"><button  name="Desabo"   rel="Shadowbox;" >Se désabonner</button></div>
   		<input type="hidden" name="Popup" value="InscNewsletter">
   	 </div>
</form>
