<!--Avis sur un jeu-->
<!--- contenu central -->
// BLOCK Evaluations
<table cellspacing="0" cellspading="0"  class="tableEval">
[COUNT Boutique/Produit/[!P::Id!]/NoteProduit|NbResult]
	[IF [!NbResult!]>0]
		<tr class="tableEval" cellspacing="0" cellspading="0">
			<td class="tableEvalEnteteCote">
				<img src="/Skins/gamesavenue/Images/bando-vendeur-gauche.png">
			</td>
			<td class="tableEvalEntete" style="text-align:left;">Date</td>
			<td class="tableEvalEntete">Commentaires</td>
			<td class="tableEvalEntete"  style="border:none;">Note</td>
			<td class="tableEvalEnteteCote" style="border:none;">
				<img src="/Skins/gamesavenue/Images/bando-vendeur-droite.png">
			</td>
		</tr>
		[!AvisPair:=0!]
		[STORPROC Boutique/Produit/[!P::Id!]/NoteProduit|AVIS|0|100|tmsCreate|DESC]
			[STORPROC Boutique/Client/NoteProduit/[!AVIS::Id!]|CLAV|0|1][/STORPROC]
			[IF [!Utils::isPair([!AvisPair!])!]]  
				<tr class="tableEval" cellspacing="0" cellspading="0">
			[ELSE]
				<tr class="tableEval" cellspacing="0" cellspading="0" style="background:#ebebeb;">
			[/IF]
			[!AvisPair+=1!]
			
				<td class="tableEvalContenuCoteLeft"></td>
				<td class="tableEvalContenu">
					<span class="blocambiance_color">[!Utils::getDate(d/m/Y,[!AVIS::tmsCreate!])!]</span>
				</td>
				<td class="tableEvalContenu">
					<span class="blocambiance_color">[!CLAV::Pseudonyme!]<br></span>
					<span class="blocPagestexte">[!AVIS::Commentaires!]</span>
				</td>
				<td class="tableEvalContenu" style="border:none;padding-right:none;text-align:center;">
					<span class="blocambiance_color">[!AVIS::Note!]</span>
				</td>
				<td class="tableEvalContenuCoteRight"></td>
			</tr>
		[/STORPROC]
	[ELSE]
		<b class="coinFinGrisborderbottom">
			<b class="coinFinGris1">&nbsp;</b>
			<b class="coinFinGris2">&nbsp;</b>
			<b class="coinFinGris3">&nbsp;</b>
			<b class="coinFinGris4">&nbsp;</b>
		</b>
		<tr class="tableEval" cellspacing="0" cellspading="0">
			<td class="tableEvalContenuCoteLeft"></td>
			<td class="tableEvalContenu" colspan="3" style="border:none;padding-right:none;">
				<span class="blocProduitPrix blocambiance_color">Pas d'avis pour ce produit</span>
			</td>
			<td class="tableEvalContenuCoteRight"></td>
		</tr>

	[/IF]
</table>
<b class="coinFinGrisborderbottom">
	<b class="coinFinGris4">&nbsp;</b>
	<b class="coinFinGris3">&nbsp;</b>
	<b class="coinFinGris2">&nbsp;</b>
	<b class="coinFinGris1">&nbsp;</b>
</b>