<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Language" content="fr">
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
		<title>Newsletter H2O Voyages, sp&eacute;cialiste de la plong&eacute;e</title>
		<style type="text/css">
			.bb_bold{font-weight:bold;}
			.bb_italic{font-style:italic;}
			.bb_underline{text-decoration:underline;}
		</style>
	</head>
	<body>
		<div align="center">
			<table width="700" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
				<tr>
					<td bgcolor="#032251">
						<img src="[!Domaine!]/Skins/H2O/Img/Logo.jpg" alt="Logo H2O voyages" border="0"/>
					</td>
				</tr>
				[STORPROC Newsletter/Lettre/[!LetId!]|Let]
					[STORPROC Newsletter/Lettre/[!Let::Id!]/Article|Arti]
						<tr>
							<td bgcolor="#031634" align="left">
								<table cellspacing="5" cellpadding="0" bgcolor="#031634">
									[IF [!Let::Intro!]]
										<tr>
											<td colspan="2">
												<font face="Arial" size="2" color="#ffffff">[!Let::Intro!]</font><br /><br />
											</td>
										</tr>
									[/IF]
									[LIMIT 0|10]
										<tr>
											<td>
												[IF [!Arti::Image!]!=]
													[IF [!Arti::Lien!]!=]
														<a target="_blank" href="[!Arti::Lien!]" title="[!Arti::Titre!]"><img src="[!Domaine!]/[!Arti::Image!].limit.200x400.jpg" alt="[!Arti::Titre!]" border="0"/></a>
													[ELSE]
														<img src="[!Domaine!]/[!Arti::Image!]" alt="[!Arti::Titre!]" border="0"/>
													[/IF]
												[/IF]
											</td>
											<td>
												<font face="Arial" size="2" color="#EABE39"><strong><u>[!Arti::Titre!]</u></strong></font><br />
												<font face="Arial" size="2" color="#ffffff">
													<em>[!Arti::Chapo!]</em><br />
													[!Arti::Contenu!]<br /><br />
												</font>
												[IF [!Arti::Lien!]!=]
													<a target="_blank" href="[!Arti::Lien!]" title="[!Arti::Titre!]"><font face="Arial" size="2" color="#EABE39">[!Arti::Lien!]</font></a>
												[/IF]
											</td>
										</tr>
									[/LIMIT]
									[IF [!Let::Conclu!]]
										<tr>
											<td colspan="2">
												<font face="Arial" size="2" color="#ffffff">[!Let::Conclu!]</font>
											</td>
										</tr>
									[/IF]
								</table>
							</td>
						</tr>
					[/STORPROC]
				[/STORPROC]	
				<tr>
					<td align="center" bgcolor="#032251">
						<font face="Arial" size="1" color="#ffffff">
							H2O Voyage - 10 boulevard Henri ARNAULD- 49100 Angers<br />Site : <a href="http://www.h2ovoyage.com" target="_blank"><font color="#EABE39">www.h2ovoyage.com</font></a> Tel : +33 (0)2.41.24.69.00. - E-m@il : <a href="http://www.h2ovoyage.com" target="_blank"><font color="#EABE39">france@h2ovoyage.com</font></a>- licence n° 049-02-0001 - IATA n° 20-2 5034 4<br /><br />AVERTISSEMENT : <br />Votre adresse &eacute;lectronique est conserv&eacute;e par H2O Voyage dans l'unique but de vous envoyer la lettre d’information H2O Voyage, <br /><a target="_blank" href="http://www.h2ovoyage.com/Desinscription"><font color="#EABE39">SI VOUS SOUHAITEZ VOUS DESABONNER CLIQUEZ ICI</font></a><br /><br />Vous disposez d'un droit d'acc&egrave;s, de modification, de rectification et de suppression des donn&eacute;es qui vous concernent (art. 34 de la loi Informatique et libert&eacute;s). Pour l'exercer, adressez-vous &agrave; H2O Voyage - 74 avenue des Aygalades - 13014 Marseille.<br /><br />Merci de ne pas répondre directement sur cette adresse email, veuillez vous rediriger vers <a href="http://www.h2ovoyage.com/Contact" target="_blank"><font color="#EABE39">notre formulaire de contact.</font></a><br /><br />
						</font>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>

