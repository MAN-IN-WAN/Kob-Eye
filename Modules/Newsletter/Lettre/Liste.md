[HEADER]
	<link rel="canonical" href="[!Domaine!]/[!Systeme::CurrentMenu::Url!]/[!Cat::Link!]" />
[/HEADER]
//Liste des newsletter
<div class="Redaction">
	<div class="TitreCategorie">
		<h1>Liste des NewsLetter</h1>
	</div>
	[STORPROC Newsletter/Lettre/Publier=1|Let|0|20|tmsCreate|DESC]
		<div class="ContenuArticle" >
			<a href="[!Domaine!]/Newsletter/Modeles/[!Let::Modele!].htm?Id=[!Let::Id!]" title="[!Let::Sujet!]" target="_blank" >
				[!Let::Sujet!]
			</a>
		</div>
	[/STORPROC]
	// Affichage formulaie d'inscription
	<div class="CpContact">
		<div class="TitreArticle">
			<h2>Inscription &agrave; la NewsLetter de [!Domaine!]</h2>
		</div>	
		<div class="ContenuArticle" >
			[IF [!FormSys_Valid!]=OK]
				[!C_Error:=0!]
				[IF [!Form_Mail!]=][!Form_Mail_Error:=1!][!C_Error:=1!][/IF]
				//Si il y a des erreurs, on les affiche
				[IF [!C_Error!]]
					<div class="BlocError">
						<p>Veuillez remplir les champs obligatoires suivants :</p>
						<ul>
							[IF [!Form_Mail_Error!]]<li>Merci de renseigner une adresse email valide</li>[/IF]
						</ul>
					</div>
				[ELSE]
					//On compte le nombre de mails enregistres et on exclut les doublons
					[COUNT Newsletter/GroupeEnvoi/1/Contact/Email=[!Form_Mail!]|Test]
					[IF [!Test!]=0]
						//creation de l'objet contact a enregistrer a la newsletter
							[OBJ Newsletter|Contact|Con]
							[METHOD Con|Set]
								[PARAM]Email[/PARAM]
								[PARAM][!Form_Mail!][/PARAM]
							[/METHOD]
							[METHOD Con|AddParent]
								[PARAM]Newsletter/GroupeEnvoi/1[/PARAM]
							[/METHOD]
							[METHOD Con|Save][/METHOD]
							
						//Envoi du mail lorsque le mail est enregistre
							[LIB Mail|LeMail]
							[METHOD LeMail|Subject][PARAM]Enregistrement à la Newsletter de [!Domaine!][/PARAM][/METHOD]
							[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::NOREPLY!][/PARAM][/METHOD]
							[METHOD LeMail|ReplyTo][PARAM][!CONF::MODULE::SYSTEME::NOREPLY!][/PARAM][/METHOD]
							[METHOD LeMail|To][PARAM][!Form_Mail!][/PARAM][/METHOD]
							[METHOD LeMail|Body]
								[PARAM]
									[BLOC Mail]
										Bonjour,<br />f&eacute;licitations, vous &ecirc;tes inscrit(e) &agrave; la newsletter de [!Domaine!].<br />
										Merci et &agrave; bient&ocirc;t sur <a href="[!Domaine!]">[!Domaine!]</a>
									[/BLOC]
								[/PARAM]
							[/METHOD]
							[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
							[METHOD LeMail|BuildMail][/METHOD]
							[METHOD LeMail|Send][/METHOD]
					[/IF]
				[/IF]
			[/IF]
			[IF [!FormSys_Valid!]!=OK||[!Test!]!=||[!C_Error!]=1]
				<div style="margin-bottom:20px;padding:5px 0 5px 0;">
					<p style="font-family:Georgia,Arial,Verdana,Helvetica,sans-serif;font-style:italic;padding-bottom:5px;">Pourquoi vous inscrire &agrave; notre newsletter ?</p>
					<p>Notre "newsletter" est une lettre d'information qui vous permet d'être r&eacute;guli&egrave;rement inform&eacute; des activités de notre site.<br /><br />Cette lettre est gratuite. Pour vous y inscrire, merci de saisir votre adresse email et de cliquer sur le bouton "Inscription".</p>
					//Message de confirmation ou d erreur
					[IF [!Test!]=0]
						<div class="BlocError" style="font-weight:bold;background-color:#ADFF81;">F&eacute;licitations, vous &ecirc;tes d&eacute;sormais inscrit(e) &agrave; notre newsletter !</div>
					[ELSE]
						[IF [!FormSys_Valid!]=OK&&[!C_Error!]=0]
							<div class="BlocError" style="font-weight:bold;">Vous &ecirc;tes d&eacute;j&agrave; inscrit(e) !</div>
						[/IF]
					[/IF]
					<form action="/[!Lien!]" method="post" style="padding:10px 0 10px 0;" id="Inscription">
						<table>
							<tr>
								<td>
									<input type="text" class="[IF [!Form_Mail_Error!]]Error[ELSE]InputMail[/IF]" name="Form_Mail" size="25" value="Entrez votre e-mail" onclick="this.value='';"/>
								</td>
								<td>
									[BLOC Bouton|width:112px;margin-top:10px;||text-align:center;width:70px;|]
										<input type="hidden" name="FormSys_Valid" value="OK"/>
										<input type="submit" class="Envoyer" value="Inscription" />
									[/BLOC]
								</td>
							</tr>
						</table>
					</form>
					Conformément à l'article 34 de la loi Informatique et Liberté du 6 janvier 1978, vous disposez d'un droit d'accès, de modification, de rectification et de suppression des données vous concernant.
				</div>
			[/IF]
		</div>
		<div class="ContenuArticle" id="desinscription" >
			//Formulaire de desinscription a la newsletter.
			<div class="TitreArticle">
				<h2>D&eacute;sabonnement &agrave; la NewsLetter de [!Domaine!]</h2>
			</div>	
			<div class="blocMessage">
				[IF [!FormSys_ValidD!]=OK]
					[COUNT Newsletter/Contact/Email=[!Form_MailD!]|Present]
					[IF [!Present!]]
						[STORPROC Newsletter/Contact/Email=[!Form_MailD!]|LeContact|0|100]
							[METHOD LeContact|Delete][/METHOD]
						[/STORPROC]
						<div class="BlocError" style="font-weight:bold;background-color:#ADFF81;">Vous &ecirc;tes d&eacute;sormais d&eacute;sinscrit(e) et ne recevrez plus notre newsletter.</div>
					[/IF]
				[/IF]
				[IF [!FormSys_ValidD!]!=OK||[!Present!]=0]
					Nous sommes heureux de vous compter parmi nos abonn&eacute;s, toutefois, vous pouvez &agrave; tout moment arr&ecirc;ter votre abonnement.<br /><br />Pour cela, veuillez saisir votre adresse email et cliquer sur le bouton "D&eacute;sabonnement".
					[IF [!Present!]=0]
						<div class="BlocError" style="font-weight:bold;">Votre adresse ne figure pas dans notre liste de diffusion.</div>
					[/IF]
					<form action="/[!Lien!]" method="post" style="padding:10px 0 10px 0;" id="DesInscription">
						<table>
							<tr>
								<td>
									<input class="InputMail" type="text" onclick="this.value='';" value="Entrez votre e-mail" size="25" name="Form_MailD">
								</td>
								<td>
									[BLOC Bouton|width:112px;margin-top:10px;||text-align:center;width:70px;|]
										<input type="submit" class="Envoyer" value="D&eacute;sabonnement" />
										<input type="hidden" name="FormSys_ValidD" value="OK"/>
									[/BLOC]
								</td>
							</tr>
						</table>
					</form>
				[/IF]
			</div>
		</div>
	</div>
</div>



