[TITLE]Admin Kob-Eye | Expedition [/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
[STORPROC Boutique/Magasin|Mag][/STORPROC]
[!LeMess:=!]
[IF [!C_OkBl!]]
	[STORPROC [!Query!]|BL][/STORPROC]
	[!CDE:=[!BL::getCommande!]!]
	[!CLI:=[!CDE::getClient()!]!]
	[!LIV:=[!CDE::getAdresseLivraison()!]!]
//	[STORPROC Boutique/Adresse/Type=Livraison/Commande/[!CDE::Id!]|LIV|0|1][/STORPROC]
	// Mise à jour statut et envoie de mail en fonction
	[IF [!BL::Statut!]!=[!C_Statut!]]
		//on a demandé un maj du statut
		[METHOD BL|Set]
			[PARAM]Statut[/PARAM]
			[PARAM][!C_Statut!][/PARAM]
		[/METHOD]
		[METHOD BL|Save][/METHOD]
		[!LeMess+=Le statut a été mis à jour<br />!]
		[IF [!C_Statut!]=3]
			[LIB Mail|LeMail]
				[METHOD LeMail|From]
					[PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM]
				[/METHOD]
				[METHOD LeMail|To]
					[PARAM][!CLI::Mail!][/PARAM]
				[/METHOD]
				[METHOD LeMail|Subject]
					[PARAM][!Mag::Nom!] : Votre commande a été expédiée[/PARAM]
				[/METHOD]	
				[METHOD LeMail|Body][PARAM]
					[BLOC Mail]
						Bonjour [!CLI::Civilite!] [!CLI::Prenom!] [!CLI::Nom!]<br />
						L'équipe de [!Mag::Nom!] a le plaisir de vous informer que votre commande [!CDE::RefCommande!] a été expédiée aux coordonnés suivante :
						[IF [!BL::AdresseLivraisonAlternative!]]
							<br />Pour [!LIV::Civilite!] [!LIV::Prenom!] [!LIV::Nom!]<br /><br />
							<br />[!BL::ChoixLivraison!]<br />
						[ELSE]
							[!LIV::Civilite!] [!LIV::Prenom!] [!LIV::Nom!]<br /><br />
							[!LIV::Adresse!] <br />
							[!LIV::CodePostal!] [!LIV::Ville!] [!LIV::Pays!]<br />
						[/IF]
						<hr/>
						Toute l'équipe de [!Mag::Nom!] vous remercie de votre confiance.<br/><br/>
						<hr/>
						Ce mail est envoyer automatiquement, merci de na pas y répondre.
						<hr/>
						Pour nous contacter : [!CONF::MODULE::SYSTEME::CONTACT!]<br/><br/>
					[/BLOC]
				[/PARAM][/METHOD]
			[METHOD LeMail|Send][/METHOD]	
			[!LeMess+=<br />le mail d'expédition a été envoyé au client!]

		[/IF]
	[/IF]
[/IF]
[STORPROC [!Query!]|BL][/STORPROC]
[!C_Statut:=[!BL::Statut!]!]
<div id="Container">
	<div id="Arbo">
		[BLOC Panneau]
		[/BLOC]
	</div>
	<div id="Data">
		[BLOC Panneau]
			<form name="gereexpedition" method="post" action="[!Lien!]" >
				<div style="margin:50px;font-size:15px;">
					<div style="font-weight:bold;text-decoration:underline;">
						Gestion Expédition pour Bon de livraison  [!BL::NumBL!]
						<span style="color:#ff0000;font-weight:bold;"><br />[!LeMess!]<br /></span>
					</div>
					Le statut actuel est&nbsp;:&nbsp; &nbsp;
					[IF [!C_Statut!]=1]Commande à préparer[/IF]
					[IF [!C_Statut!]=2]Commande préparée[/IF]
					[IF [!C_Statut!]=3]Commande expédiée[/IF]
					[IF [!C_Statut!]=4]Bon annuler[/IF]
					<br /><br /><span style="text-decoration:underline;">Impression</span><br />
					Bon de Livraison : <input type="checkbox" name="C_Impression" value="Impression" /><br />
					Etiquette colis : <input type="checkbox" name="C_ImpressionEtiq" value="ImpressionEtiq" /><br />
					<br /><span style="text-decoration:underline;">Statut Livraison :</span><br />
					Non Préparé <input type="radio" name="C_Statut" value="1" [IF [!C_Statut!]=1] checked="checked"[/IF] /><br />Préparé <input type="radio" name="C_Statut" value="2" [IF [!C_Statut!]=2] checked="checked"[/IF] /><br />Expédié <input type="radio" name="C_Statut" value="3" [IF [!C_Statut!]=3] checked="checked"[/IF] /><br />Annulé (annulera la commande!) <input type="radio" name="C_Statut" value="4" [IF [!C_Statut!]=4] checked="checked"[/IF] /><br /><br />
					<input type="submit" name="C_Valider" value="ValiderBL" />
					<input type="hidden" name="C_OkBl" value="1" />
				</div>
				
			</form>
		[/BLOC]
	</div>
</div>

