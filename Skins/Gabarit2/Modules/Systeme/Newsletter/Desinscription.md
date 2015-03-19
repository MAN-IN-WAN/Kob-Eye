[MODULE Systeme/Structure/Gauche]
<div id="Milieu">
	[MODULE Systeme/Menu/ImageMenu]
	<div id="Data">
		[MODULE Systeme/Ariane]
		<h1>D&eacute;sinscription &agrave; la newsletter</h1>
		<div class="Article">
			//Formulaire de desinscription a la newsletter.	
			[IF [!BtnValid!]=Valider]
				[COUNT Newsletter/Contact/Email=[!Form_MailD!]|Present]
				[IF [!Present!]]
					//il faut modifier le champ newsletter du client
					[STORPROC Boutique/Client/Mail=[!Form_MailD!]|Cl]
						[METHOD Cl|Set]
							[PARAM]Newsletter[/PARAM]
							[PARAM]0[/PARAM]
						[/METHOD]
						[METHOD Cl|Save][/METHOD]
					[/STORPROC]
					[STORPROC Newsletter/Contact/Email=[!Form_MailD!]|LeContact|0|100]
						[METHOD LeContact|Delete][/METHOD]
					[/STORPROC]
					<p class="Description">Vous &ecirc;tes d&eacute;sormais d&eacute;sinscrit(e) et ne recevrez plus notre newsletter.</p>
				[ELSE]
					<p class="Description">Votre adresse ne figure pas dans notre liste de diffusion.</p>
				[/IF]
			[ELSE]
				<p class="Description">Pour valider votre d&eacute;sinscription, merci de saisir votre adresse e-mail et cliquer sur le bouton "Valider".</p>
				<form action="/Systeme/Newsletter/Desinscription" method="post" >
					<div class="LigneForm">
						<input type="text" name="Form_MailD" size="25" value="Entrez votre e-mail" />	
						<input type="submit" name="BtnValid" value="Valider" />
					</div>
				</form>
			[/IF]
		</div>
	</div>
</div>
<div class="Clear"></div>