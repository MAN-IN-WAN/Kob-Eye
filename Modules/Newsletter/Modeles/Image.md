<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
[STORPROC Newsletter/Lettre/[!Id!]|Let|0|1][/STORPROC]
<html>
	<head>
		<meta http-equiv="Content-Language" content="fr">
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
		<title>Newsletter [!Let::Titre!]</title>
		<style type="text/css" media="screen">
			body {color:[!Let::CouleurPolice!];background-color:[!Let::CouleurFond!];}
			html {color:[!Let::CouleurPolice!];background-color:[!Let::CouleurFond!];}
			p { font-family: arial;font-size: 10px;color: [!Let::CouleurPolice!]; }
			img {border: 0;}
		</style>
	</head>
	<body bgcolor="[!Let::CouleurFond!]" color="[!Let::CouleurPolice!]" style="background-color:[!Let::CouleurFond!];">
		<div align="center">
			<p>Si vous ne visualisez pas cet e-mail, <a href="http://[!Let::Domaine!]/Newsletter/Modele/[!Let::Modele!].htm?Id=[!Id!]">cliquez ici</a></p>
			<!-- partie haute -->
			<table border="0" height="364" cellspacing="0" cellpadding="0">
					<tr>
						<td>
							[IF [!Let::Lien!]!=]
								<a href="[!Let::Lien!]" ><img src="http://[!Let::Domaine!]/[!Let::Image!]" alt="[!Let::Sujet!]" border="0" ></a>
							[ELSE]
								<img src="http://[!Let::Domaine!]/[!Let::Image!]" alt="[!Let::Sujet!]" border="0" >
							[/IF]
						</td>
					</tr>
			</table>
			<!-- mentions légales -->
			<table width="600" height="100" border="0" cellspacing="8" cellpadding="0" align="center" class="tab">
				<tr>
					<td align="center">
						<p>Vous recevez ce message car vous êtes abonné sur notre site :<br />
						http://[!Let::Domaine!]<br />
						Conformément à l'article 34 de la loi Informatique et Liberté du 6 janvier 1978, vous disposez d'un droit
						d'accès, de modification, de rectification et de suppression des données vous concernant.
						</p>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>