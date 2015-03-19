<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Language" content="fr">
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
		<title>Newsletter FNAE-ZUS : Fédération Nationale des Associations d’Entrepreneurs en Zones Urbaines Sensibles</title>
		<style type="text/css">
			.bb_bold{font-weight:bold;}
			.bb_italic{font-style:italic;}
			.bb_underline{text-decoration:underline;}
			.TabNewsletter td{
				padding:10px;
			}
		</style>
	</head>
	<body>
		<div align="center">
			<table width="700" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
				<tr>
					<td bgcolor="#cccccc">
						<img src="" alt="Logo du site" border="0"/>
					</td>
					<td bgcolor="#cccccc">
						Coordonnées du site
					</td>
				</tr>
				[STORPROC Newsletter/Lettre/[!Id!]|Let]
					[STORPROC Newsletter/Lettre/[!Let::Id!]/Article|Arti]
						<tr>
							<td>
								<table cellspacing="10" cellpadding="0" bgcolor="#ffffff">
									[IF [!Let::Intro!]]
									<tr>
										<td colspan="2">
											<font face="Arial" size="2" color="#000000">[!Let::Intro!]</font><br /><br />
										</td>
									</tr>
									[/IF]
									[LIMIT 0|10]
									<tr>
									[IF [!Arti::Image!]!=]
										<td valign="top">
											<img src="[!Domaine!]/[!Arti::Image!].limit.100x100.jpg" alt="[!Arti::Titre!]" border="0"/>
										</td>
									[/IF]
									<td [IF [!Arti::Image!]=]colspan="2"[/IF]>
										<font face="Arial" size="2" color="#000000"><strong><u>[!Arti::Titre!]</u></strong></font><br /><br />
										<font face="Arial" size="2" color="#000000">
											<em>[!Arti::Chapo!]</em><br /><br />
											[!Arti::Contenu!]<br /><br />
										</font>
										[IF [!Arti::Lien!]!=]
											<a target="_blank" href="[!Arti::Lien!]" title="[!Arti::Titre!]"><font face="Arial" size="2" color="#000000">[!Arti::Lien!]</font></a><br /><br />
										[/IF]
									</td>
									</tr>
									[/LIMIT]
									[IF [!Let::Conclu!]]
									<tr>
										<td colspan="2">
											<font face="Arial" size="2" color="#000000">[!Let::Conclu!]</font>
										</td>
									</tr>
									[/IF]
								</table>
							</td>
						</tr>
					[/STORPROC]
				[/STORPROC]	
				<tr>
					<td align="center" bgcolor="#979797" colspan="2">
						<font face="Arial" size="1" color="#ffffff">
							Vous recevez ce message car vous vous &ecirc;tes abonn&eacute; sur notre site :<br /> <a target="_blank" href="http://gabarits.expressiv.fr/"><font color="#000000">http://gabarits.expressiv.fr</font></a><br />Si vous souhaitez vous d&eacute;sabonner <a target="_blank" href="http://gabarits.expressiv.fr/Desinscription-newsletter"><font color="#000000">cliquez ici</font></a><br />Conform&eacute;ment &agrave; l'article 34 de la loi Informatique et Libert&eacute; du 6 janvier 1978, vous disposez d'un droit<br />d'acc&egrave;s, de modification, de rectification et de suppression des donn&eacute;es vous concernant.
						</font>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>

