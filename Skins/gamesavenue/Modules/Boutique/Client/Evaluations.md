//Evaluations d un vendeur
<!--- contenu central -->
[!EvalPair+=0!]
[COUNT Boutique/Evaluation/Client/[!V::Id!]|NbEval]
[IF [!NbEval!]>0]
	// BLOCK Evaluations
	<div class="VendeurBlocCentre" >
		<table cellspacing="0" cellspading="0"  class="tableEval">
			<tr class="tableEval" cellspacing="0" cellspading="0">
				<td class="tableEvalEnteteCote">
					<img src="/Skins/gamesavenue/Images/bando-vendeur-gauche.png">
				</td>
				<td class="tableEvalEntete" style="text-align:left;">Evaluation</td>
				<td class="tableEvalEntete">Réception</td>
				<td class="tableEvalEntete">Etat</td>
				<td class="tableEvalEntete">Rapidité</td>
				<td class="tableEvalEntete"  style="border:none;">Emballage</td>
				<td class="tableEvalEnteteCote" style="border:none;">
					<img src="/Skins/gamesavenue/Images/bando-vendeur-droite.png">
				</td>
			</tr>
			[STORPROC Boutique/EValuation/Client/[!V::Id!]|EVALU]
				[IF [!Utils::isPair([!EvalPair!])!]]  
					<tr class="tableEval" cellspacing="0" cellspading="0">
				[ELSE]
					<tr class="tableEval" cellspacing="0" cellspading="0" style="background:#ebebeb;">
				[/IF]
				[!EvalPair+=1!]
				
					<td class="tableEvalContenuCoteLeft"></td>
					<td class="tableEvalContenu">
					[STORPROC [!Math::Floor([!EVALU::Note!])!]]
							<img src="/Skins/gamesavenue/Images/etoile_notation.png">&nbsp;
						[/STORPROC]
						<br />[!EVALU::Description!]
					</td>
					<td class="tableEvalContenuCoche"><img src="/Skins/gamesavenue/Images/validation.png"></td>
					<td class="tableEvalContenuCoche"><img src="/Skins/gamesavenue/Images/validation.png"></td>
					<td class="tableEvalContenuCoche"></td>
					<td class="tableEvalContenuCoche" style="border:none;padding-right:none;"><img src="/Skins/gamesavenue/Images/validation.png"></td>
					<td class="tableEvalContenuCoteRight"></td>
				</tr>
			[/STORPROC]
		</table>
	</div>	
	<b class="coinNoirFondBlancborderbottom">
		<b class="coinFinGris4">&nbsp;</b>
		<b class="coinFinGris3">&nbsp;</b>
		<b class="coinFinGris2">&nbsp;</b>
		<b class="coinFinGris1">&nbsp;</b>
	</b>
[ELSE]
	<span class="blocProduitPrix blocambiance_color">Aucun avis pour ce vendeur</span>

[/IF]