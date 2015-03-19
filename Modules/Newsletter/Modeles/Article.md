<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/html4/loose.dtd">
<html>

		[STORPROC Newsletter/Lettre/[!LetId!]|Let|0|1][/STORPROC]
	<head>
		<meta http-equiv="Content-Language" content="fr">
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
		<title>NewsLetter Pragma-immobilier </title>
		<style type="text/css">
			body {color:[!Let::CouleurPolice!];background-color:[!Let::CouleurFond!];font-family: arial;font-size: 10px;}
			p { font-family: arial;font-size: 10px;color: [!Let::CouleurPolice!]; }
			img {border: 0;}
			.bb_bold{font-weight:bold;}
			.bb_italic{font-style:italic;}
			.bb_underline{text-decoration:underline;}
		</style>
	</head>
	<body  bgcolor="[!Let::CouleurFond!]" color="[!Let::CouleurPolice!]"  style="background-color:[!Let::CouleurFond!];">
		<div align="center" >
			<p>Si vous ne visualisez pas cet e-mail, <a href="http://[!Let::Domaine!]/Newsletter/Modele/[!Let::Modele!].htms?LetId=[!Id!]">cliquez ici</a></p>
			<table width="700" cellspacing="0" cellpadding="0" bgcolor="[!Let::CouleurFond!]" style="">
				[IF [!Let::Image!]]
					<tr>
						<td>
							[IF [!Let::Lien!]]<a href="[!Let::Lien!]" >[/IF]<img src="http://[!Let::Domaine!]/[!Let::Image!]" />[IF [!Let::Lien!]]</a>[/IF]
						</td>
					</tr>
				[/IF]
				[IF [!Let::Intro!]]
					<tr>
						<td style="padding:5px 5px 10px 5px;" align="left" valign="top">
							<div style="overflow:hidden;margin-top:5px;color:#000;">
								<font size="2">[!Let::Intro!]</font>
							</div>
						</td>
					</tr>
				[/IF]
				[STORPROC Newsletter/Lettre/[!Let::Id!]/Article|Art|0|10|Ordre|ASC]
					<tr>
						<td style="padding:5px 5px 0 5px;" valign="top">
							
							<div style="overflow:hidden;margin-top:5px;text-align:justify;"><a name="[!Art::Id!]"></a>
								[IF [!Art::Image!]||[!P!]]
								[COUNT Newsletter/Lettre/[!Let::Id!]/Article|A]
								[IF [!Math::Floor([!Pos:/2!])!]==[!Pos:/2!]]
									<div style="float:right;display:block;position:relative;width:200px;margin-left:10px;">
								[ELSE]
									<div style="float:left;display:block;position:relative;width:200px;margin-right:10px;">
								[/IF]
										[IF [!Art::Image!]!=]
											<img src="http://[!Let::Domaine!]/[!Art::Image!]" alt="[!Art::Titre!]" style="border:0;width:200px;margin:0 10px 5px 0;"/>
										[/IF]
										[STORPROC Newsletter/Article/[!Art::Id!]/Image|Pict]
											<img src="http://[!Let::Domaine!]/[!Pict::URL!]" alt="[!Pict::Titre!]" style="border:0;width:200px;margin:0 10px 5px 0;"/>
										[/STORPROC]
									</div>
								[/IF]
								<div style="padding:0;margin-top:0;text-align:justify;display:block;font-size:12px;line-height:20px;">
									<strong>[!Art::Titre!]</strong><br />
									[IF [!Art::Chapo!]]<em>[!Art::Chapo!]</em><br /><br />[/IF]
									[!Art::Contenu!]<br /><br />
								
								[COUNT Newsletter/Article/[!Art::Id!]/Lien|L]
								[IF [!L!]]
									[STORPROC Newsletter/Article/[!Art::Id!]/Lien|Lie]
										<a target="_blank" href="[IF [!Lie::URL!]~http][!Lie::URL!][ELSE]http://[!Lie::URL!][/IF]" title="[!Lie::Titre!]" style="display:block;width:100%;"><font size="2">[!Lie::Titre!]</font></a>
									[/STORPROC]
								[/IF]
								
								</div>
							</div>
							
							<a href="#top" title="Revenir en haut de la lettre d'information" style="display:block;width:100%;text-align:right;">Aller en Haut</a>
						</td>
					</tr>
				[/STORPROC]
				<!-- mentions légales -->
				<tr>
					<td align="center">
						<p>Vous recevez ce message car vous êtes abonné sur notre site :
						http://[!Let::Domaine!]<br />
						Si vous souhaitez vous désabonner <a href="http://[!Let::Domaine!]/Newsletter/Desabonner">cliquez ici</a><br />
						Conformément à l'article 34 de la loi Informatique et Liberté du 6 janvier 1978, vous disposez d'un droit
						d'accès, de modification, de rectification et de suppression des données vous concernant.
						</p>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>

