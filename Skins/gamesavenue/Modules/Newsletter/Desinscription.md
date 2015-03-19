[MODULE Systeme/Ariane]
[MODULE Systeme/Structure/Gauche]
[MODULE Systeme/Structure/Droite]	
<div id="Milieu">
	<div class="Contenu">
		<h5>D&eacute;sinscription &agrave; la newsletter</h5>
		<div class="Article" style="border:none;">
			//Formulaire de desinscription a la newsletter.	
			[IF [!BtnValid!]=Valider]
			    <p>
			    [MODULE Newsletter/DoDesinscription?EMAIL=[!Form_MailD!]]
			    </p>
			[ELSE]
				<p>Pour valider votre d&eacute;sinscription, merci de saisir votre adresse e-mail et cliquer sur le bouton "Valider".</p>
				<form action="/Desinscription" method="post" >
					<div class="LigneForm">
						<input type="text" name="Form_MailD" size="25" value="Entrez votre e-mail" class="InputMail"/>
						[BLOC Bouton|width:100px;float:left;||width:70px;|]
							<input type="submit" name="BtnValid" value="Valider" class="BtnNewsLet" />
						[/BLOC]	
					</div>
				</form>
			[/IF]
		</div>
	</div>
</div>
