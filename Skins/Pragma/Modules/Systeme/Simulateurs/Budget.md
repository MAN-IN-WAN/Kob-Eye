<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>PragmA immobilier vous aide &#224; simuler votre budget pour le financement de votre bien</title>
	<script type="text/javascript" src="/Skins/Commun/Js/mootools.js"></script>
	<script type="text/javascript" src="/Skins/Commun/Js/mootools-more.js"></script>
	<script src="/Skins/Pragma/Js/budget.js" language="javascript"></script>
	<style>
		body {
			background:#0072bb;
			color:#000000;
			font-size:12px;
			font-family:arial;
		}
		table {
			width:600px;
			margin:20px 0;
			background:#a1c5dc;
			border:1px solid #000000;
			color:#000000;
		}
		td {
			border:1px solid #000000;
		}
		input {border:1px solid #2c2c2c;}
				
	</style>
</head>

<body >
	<div class="CarreRes" >
		<div class="FondCarre"></div>
		<div class="Infos" style="padding-top:0">
			<form name="formbien" method="post" action="">
				<table width="95%" border="0" cellspacing="3" cellpadding="0"  >
					<tr> 
						<td colspan="3" style="font-size:14px;font-weight:bold;border-bottom:1px solid #ffffff;text-align:center;padding-top:5px;margin:5px 0;">
							<div class="Ville">Simulez votre budget pour l'achat d&lsquo;un bien<br/><br/></div>
						</td>
					</tr>
					<tr> 
						<td width="140"  style="padding-left:5px">
							<div class="Simul">Prix de votre bien</div>
						</td>
						<td width="180" align="right"> 
							<a href="javascript:modifValeur(document.formbien.prix_bien,-1000);"><img src="/Skins/[!Systeme::Skin!]/Img/BoutonMoins.jpg" width="16" height="16" border="0"></a>							
							<input type="text" size="15" style="text-align:right;"  name="prix_bien" onChange="javascript:calculBien(document.formbien);" value="[!Prix!]">
							<a href="javascript:modifValeur(document.formbien.prix_bien,1000);"><img src="/Skins/[!Systeme::Skin!]/Img/BoutonPlus.jpg" width="16" height="16" border="0"></a>
						</td>
						<td width="26">
							<div class="Simul">&nbsp;&nbsp;&euro;</div>
						</td>
					</tr>
					<tr> 
						<td width="140"  style="padding-left:5px">
							<div class="Simul">Votre apport personnel</div>
						</td>
						<td width="180" align="right"> 
							<a href="javascript:modifValeur(document.formbien.apport_perso,-1000);"><img src="/Skins/[!Systeme::Skin!]/Img/BoutonMoins.jpg" width="16" height="16" border="0"></a>
							<input type="text" size="15" style="text-align:right;"  name="apport_perso" onChange="javascript:calculBien(document.formbien);" value="0">
							<a href="javascript:modifValeur(document.formbien.apport_perso,1000);"><img src="/Skins/[!Systeme::Skin!]/Img/BoutonPlus.jpg" width="16" height="16" border="0"></a>
						</td>
						<td width="26" align="left">
							<div class="Simul">&nbsp;&nbsp;&euro;</div>
						</td>
					</tr>
					<tr>
						<td colspan="3" style="border-bottom:1px solid #ffffff;" >&nbsp;</td>
					</tr>
	
					<tr> 
						<td width="140"  style="padding-left:5px">
							<div class="Simul">Somme &agrave; financer</div>
						</td>
						<td width="180" align="right"> 
							<input type="text" size="15" style="text-align:right;"  name="somme_finance" onChange="javascript:calculBien(document.formbien);" readonly>
						</td>
						<td width="26">
							<div class="Simul">&nbsp;&nbsp;&euro;</div>
						</td>
					</tr>
					<tr>
						<td colspan="3" style="border-bottom:1px solid #ffffff;" >&nbsp;</td>
					</tr>
					<tr> 
						<td width="140"  style="padding-left:5px"><div class="Simul">Mensualit&eacute;</div></td>
						<td width="180" align="right"> 
							<a href="javascript:modifValeur(document.formbien.mensualite,-100);"><img src="/Skins/[!Systeme::Skin!]/Img/BoutonMoins.jpg" width="15" height="15" border="0"></a>
							<input type="text" size="15" style="text-align:right;"  name="mensualite" onChange="javascript:changeMensualiteBien(document.formbien);">
							<a href="javascript:modifValeur(document.formbien.mensualite,100);"><img src="/Skins/[!Systeme::Skin!]/Img/BoutonPlus.jpg" width="16" height="16" border="0"></a>
						</td>
						<td width="26">
							<div class="Simul">&nbsp;&nbsp;&euro;</div>
						</td>
					</tr>
					<tr> 
						<td width="140"  style="padding-left:5px"><div class="Simul">Dur&eacute;e</div></td>
						<td width="180" align="right">
							<a href="javascript:modifValeur(document.formbien.duree,-1);"><img src="/Skins/[!Systeme::Skin!]/Img/BoutonMoins.jpg" width="16" height="16" border="0" style="margin-top:5px;"></a>
							<input type="text" name="duree" size="8" style="text-align:right;" class="champtxt" onChange="javascript:changeDureeBien(document.formbien);" value="15">
							<a href="javascript:modifValeur(document.formbien.duree,1);"><img src="/Skins/[!Systeme::Skin!]/Img/BoutonPlus.jpg" width="16" height="16" border="0"></a>
						</td>
						<td width="26"><div>&nbsp;&nbsp;An(s)</div></td>
					</tr>
					<tr> 
						<td width="150"  style="padding-left:5px"><div class="Simul">Taux</div></td>
						<td width="180" align="right"> 
							<a href="javascript:modifValeur(document.formbien.taux,-0.1);"><img src="/Skins/[!Systeme::Skin!]/Img/BoutonMoins.jpg" width="16" height="16" border="0"></a>
							<input type="text" name="taux" size="8" style="text-align:right;"  onChange="javascript:calculBien(document.formbien);" value="3.9">
							<a href="javascript:modifValeur(document.formbien.taux,0.1);"><img src="/Skins/[!Systeme::Skin!]/Img/BoutonPlus.jpg" width="16" height="16" border="0"></a>
						</td>
						<td width="26"><div class="Simul">&nbsp;&nbsp;%</div></td>
					</tr>
					<tr>
						<td colspan="3" style="border-bottom:1px solid #ffffff;" >&nbsp;</td>
					</tr>
					<tr> 
						<td width="150"  style="padding-left:5px">
							<div class="Simul">Co&ucirc;t de votre emprunt</div>
						</td>
						<td width="180" align="right"> 
							<input type="text" name="cout_emprunt"  style="text-align:right;"  readonly size="15">
						</td>
						<td width="26">
							<div class="Simul">&nbsp;&nbsp;&euro;</div>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</body>
</html>

