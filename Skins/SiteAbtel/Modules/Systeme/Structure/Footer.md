// ligne du bas de tous les sites
[IF [!AddNewsletter!]]
	[COUNT Newsletter/GroupeEnvoi/1/Contact/Email=[!emailnewsletter!]|Test]
	[IF [!Test!]>0]
		[!MessageInfos:=Vous &ecirc;tes d&eacute;j&agrave; inscrit(e).!]
	[ELSE]	
		//creation de l'objet contact a enregistrer a la newsletter
		[STORPROC Newsletter/Contact/Email=[!emailnewsletter!]|Con]
			[NORESULT]
				[OBJ Newsletter|Contact|Con]
				[METHOD Con|Set]
					[PARAM]Email[/PARAM]
					[PARAM][!emailnewsletter!][/PARAM]
				[/METHOD]
			[/NORESULT]
		[/STORPROC]
		[METHOD Con|AddParent]
			[PARAM]Newsletter/GroupeEnvoi/1[/PARAM]
		[/METHOD]
		[METHOD Con|Save][/METHOD]

		//Envoi du mail lorsque le mail est enregistre
		[LIB Mail|LeMail]
		[METHOD LeMail|Subject][PARAM]Enregistrement à la newsletter de A VOIR !!!!!!!!!!!!!![/PARAM][/METHOD]
		[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
		[METHOD LeMail|ReplyTo][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
		[METHOD LeMail|To][PARAM][!emailnewsletter!][/PARAM][/METHOD]
		[METHOD LeMail|Bcc][PARAM][!CONF::MODULE::SYSTEME::CONTACTALPHA!][/PARAM][/METHOD]
		[METHOD LeMail|Body]
			[PARAM]
				[BLOC Mail]
					Bonjour,<br />f&eacute;licitation, vous &ecirc;tes inscrit(e) &agrave; la newsletter de A VOIR !!!!!!!!!!!!!!.<br />
				[/BLOC]
			[/PARAM]
		[/METHOD]
		[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
		[METHOD LeMail|BuildMail][/METHOD]
		[METHOD LeMail|Send][/METHOD]
		[!MessageInfos:=F&eacute;licitation vous &ecirc;tes inscrit(e) à notre newsletter.!]
	[/IF]
[/IF]
<div id="newsletterBottom" class="col-md-5">
	<form action="/" method="post" >
		<input type="hidden" name="AddNewsletter" value="1"/>
		<label for="emailNewsletter">Inscription à la newsletter</label>
		<input id="emailNewsletter" type="text" placeholder="Votre Adresse E-mail" name="emailNewsletter" />
		<button type="submit">></button>	
	</form>		
</div>
<div id="bottomMenu" class="col-md-7 ">
	[MODULE Systeme/Menu/BootstrapMenuBottom]
</div>
[IF [!Systeme::CurrentMenu::Url!]!=]
<div id="moreFooter" class="col-md-12">
	[MODULE Systeme/Structure/MoreFooter]
</div>
[/IF]