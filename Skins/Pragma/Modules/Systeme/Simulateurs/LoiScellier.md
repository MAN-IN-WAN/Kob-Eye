<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
   	 <title>Simulateur loi scellier</title>
	
	<script type="text/javascript" src="/Skins/Commun/Js/mootools.js"></script>
	<script type="text/javascript" src="/Skins/Commun/Js/mootools-more.js"></script>
	<script type="text/javascript" src="/Skins/[!Systeme::Skin!]/Js/simulateur.js"></script>
	<script type="text/javascript">
		window.addEvent('domready', function() {
			calcul();
			$('simulateurForm').getElements('input').addEvent('blur', calcul);
		});
	</script>
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

<body   >
	<div class="CarreRes" >

		<div class="Infos" style="padding-top:0;">z
			<form id="simulateurForm" action=""   >
				<table width="95%" border="0" cellspacing="3" cellpadding="0" >
					<tr> 
						<td colspan="3" style="font-size:14px;font-weight:bold;border-bottom:1px solid #ffffff;text-align:center;padding-top:5px;margin:5px 0;">
							<div class="Ville">Simulez Loi Scellier<br/><br/></div>
						</td>
					</tr>
					<tr><td colspan="3" height="16" ></td></tr>
					<tr> 
						<td width="300"  style="padding-left:5px">
							<div class="SimulScellier">Votre revenu imposable annuel (0 à 150000 €)</div>
						</td>
						<td width="19" height="16" align="right">
							<input type="text" size="18"  style="text-align:right;" maxlength="6" id="revenu_value" value="0" onblur="this.value = adjustValue(0, 150000, this.value)" />
						</td>
						<td width="26">
							<div class="SimulScellier">&nbsp;&nbsp;&euro;</div>
						</td>
					</tr>
					<tr> 
						<td width="300"  style="padding-left:5px">
							<div class="SimulScellier">Votre revenu foncier annuel (0 à 10700 €)</div>
						</td>
						<td width="19" height="16" align="right">
							<input type="text" size="18"  style="text-align:right;" maxlength="6" id="revenu_foncier_value" value="0" onblur="this.value = adjustValue(0, 10700, this.value)" />
						</td>
						<td width="26">
							<div class="Simul">&nbsp;&nbsp;&euro;</div>
						</td>
					</tr>
					<tr>
						<td width="300"  style="padding-left:5px">
							<div class="Simul">Nombre de parts</div>
						</td>
						<td width="19" height="16" align="right">
							<input type="text"   style="text-align:right;" size="10" maxlength="3" id="nb_parts_value" value="1" />
						</td>
						<td width="26">
							<div class="SimulScellier"></div>
						</td>
					</tr>
					<tr>
						<td width="300"   height="16" style="padding-left:5px">
							<div class="Simul">Votre imposition est de</div>
						</td>
						<td width="19" height="16" align="right">
							<input type="text"  style="text-align:right;" size="18" readonly="readonly" id="imposition_value" value="0" />	
						</td>
						<td width="26">
							<div class="Simul">&nbsp;&nbsp;&euro;</div>
						</td>
					</tr>
					<tr>
						<td width="300"  height="16"  style="padding-left:5px">
							<div class="Simul">Votre <abbr title="Taux Marginal d'Imposition">TMI</abbr> est de</div>
						</td>
						<td width="19" height="16" align="right">
							<input type="text"  style="text-align:right;" size="18" readonly="readonly" id="tmi_value" value="0" />
						</td>
						<td width="26">
							<div class="Simul">&nbsp;&nbsp;%</div>
						</td>
					</tr>
					<tr><td colspan="3" height="16" ><div class="titreArt">Vous exercez...</div></td></tr>
					<tr><td colspan="3" align="center" height="16" >
						<span style="padding-right:15px;"><input type="radio" name="liberal" id="salarie" value="0" /> Une profession salariée</span>
						<input type="radio" name="liberal" id="liberal" value="1" checked="checked" /> Une profession libérale
					</td></tr>
					<tr><td colspan="3" height="16" ></td></tr>
					<tr> 
						<td width="300" height="16"  style="padding-left:5px">
							<div class="SimulScellier">Montant de l'investissement (75000 à 300000 €)</div>
						</td>
						<td width="19" height="16" align="right">
							<input type="text" size="18"  style="text-align:right;" maxlength="6" id="montant_investissement_value" value="75000" onblur="this.value = adjustValue(75000, 300000, this.value)" />
						</td>
						<td width="26">
							<div class="SimulScellier">&nbsp;&nbsp;&euro;</div>
						</td>
					</tr>
					<tr>
						<td width="300"  style="padding-left:5px">
							<div class="Simul">Durée du crédit</div>
						</td>
						<td width="19" height="16" align="right">
							<input type="text" size="10"  style="text-align:right;" id="duree_credit_value" value="20" />
						</td>
						<td width="26">
							<div class="Simul">&nbsp;&nbsp;ans</div>
						</td>
					</tr>
					<tr>
						<td width="300"  style="padding-left:5px">
							<div class="Simul">Taux du crédit</div>
						</td>
						<td width="19" height="16" align="right">
							<input type="text" size="10"  style="text-align:right;" id="taux_credit_value" value="4" /
						</td>
						<td width="26">
							<div class="Simul">&nbsp;&nbsp;%</div>
						</td>
					</tr>
					<tr>
						<td width="300"  style="padding-left:5px">
							<div class="Simul">Durée de la simulation</div>
						</td>
						<td width="19" height="16" align="right">
							<input type="text" id="duree_simulation_value" size="10" style="text-align:right;"  value="9" onblur="this.value = adjustValue(9, 15, this.value)" /> 
						</td>
						<td width="26">
							<div class="Simul">&nbsp;&nbsp;ans</div>
						</td>
					</tr>
					<tr><td colspan="3" height="16" ><div class="titreArt">Résultat de la simulation :</div></td></tr>
					<tr><td colspan="3" height="16" >
						<p >Vous réaliserez une économie d'impôts de <span id="economie_impot" name="economie_impot" style="font-weight:bold"></span> €
						(dont <span id="texte_csgcrds" name="texte_csgcrds"></span> € de <abbr title="Contribution Sociale Généralisée">CSG</abbr> <abbr title="Contribution au Remboursement de la Dette Sociale">CRDS</abbr>)
						sur <span id="texte_duree" name="texte_duree"></span> ans.<br />
						Le capital constitué au bout de cette durée sera de <span id="capital_constitue" name="capital_constitue" style="font-weight:bold;"></span> €.</p>
					</td></tr>
					<tr><td colspan="3" height="16" >
						<p style="display:none">Votre épargne s'élevera donc à priori à <span id="epargne_moyenne" name="epargne_moyenne"></span> € par mois.*<br /><br />
						*Ce simulateur est basé sur la rentabilité nette de charges moyenne constatée sur le marché.<br />
						<strong>L'épargne moyenne placée vous ferait gagner :</strong><br />
						. Sur une assurance vie à 4,5 % : <span id="assurance_vie" name="assurance_vie"></span> €<br />
						. Sur un livret A à 1,25 % : <span id="livret_a" name="livret_a"></span> €<br />
						. Sur un PEL à 2,5 % : <span id="pel" name="pel"></span> €</p>
					</td></tr>
					<tr><td colspan="3" height="16" ><div class="titreArt">Qui finance votre projet ?</div></td></tr>
					<tr><td colspan="3" height="16" >
						<div id="graph"></div>
					</td></tr>
					<tr><td colspan="3" height="16" >
							<div id="messagetxt"></div>
					</td></tr>
				</table>
			</form>
		</div>
	</div>
</body>