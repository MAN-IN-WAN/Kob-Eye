<div class="colonne220">
	<b class="coinFinGrisbordertop">
		<b class="coinFinGris1">&nbsp;</b>
		<b class="coinFinGris2">&nbsp;</b>
		<b class="coinFinGris3">&nbsp;</b>
		<b class="coinFinGris4">&nbsp;</b>
	</b>
	<div class="titreDernierAvis">&nbsp;</div>
	[STORPROC Boutique/NoteProduit|AVIS|0|2|tmsCreate|DESC]
 		[STORPROC Boutique/Produit/NoteProduit/[!AVIS::Id!]|PAV|0|1][/STORPROC]
		[STORPROC Boutique/Client/NoteProduit/[!AVIS::Id!]|CLAV|0|1][/STORPROC]
		<div class="coinFinGriscontent">
			<div class="contenuColonneAvis">
				<div class="imageaccoletexte">
					[IF [!PAV::Image!]!=]
						<img src="/[!PAV::Image!]" class="img_liste"/>
					[ELSE]
						<img src="/Skins/gamesavenue/Images/defaut_image.jpg" class="img_liste"/>
					[/IF]
				</div>
				<div class="titreAvis">[!CLAV::Pseudonyme!]</div>
				<div class="TexteDsCadreAvis">[!AVIS::Commentaires!]</div>
			</div>
		</div>
	[/STORPROC]
		
	<b class="coinFinGrisboderbottom">
		<b class="coinFinGris4">&nbsp;</b>
		<b class="coinFinGris3">&nbsp;</b><b class="coinFinGris2">&nbsp;</b><b class="coinFinGris1">&nbsp;</b>
	</b>
</div>