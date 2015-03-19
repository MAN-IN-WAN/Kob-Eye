//Barre d'outil
<div class="BarreAction">
	<div id="Gauche">
		
	</div>
	<div id="Contenu">
		[IF [!Modifier!]]<a href="/[!Query!][IF [!lastValue!]=&&[!QueryObj!]=]/[!Id!][/IF]/Modifier" id="Modifier">Modifier</a>
			<img src="/Skins/AdminV2/Img/BarreAction/SeparationOutil.png" />[/IF]
		[IF [!Ajouter!]]<a href="/[!Query!]/Ajouter" id="Ajouter">Ajouter</a>
			<img src="/Skins/AdminV2/Img/BarreAction/SeparationOutil.png" />[/IF]
		[IF [!Supprimer!]]<a href="/[!Query!][IF [!lastValue!]=&&[!QueryObj!]=]/[!Id!][/IF]/Supprimer" id="Supprimer">Supprimer</a>
			<img src="/Skins/AdminV2/Img/BarreAction/SeparationOutil.png" />[/IF]
		[IF [!Precedent!]]<a href="#nogo" id="Back">Pr&eacute;c&eacute;dent</a>
			<img src="/Skins/AdminV2/Img/BarreAction/SeparationOutil.png" />[/IF]
		[IF [!Suivant!]]<a href="#nogo" id="Next">Suivant</a>[/IF]
		[IF [!Commander!]]<a href="/Boutique/Commande/Valider" id="Commander">Commander</a>[/IF]
		[IF [!Panier!]]<a href="#nogo"  class="info"><!--<img src="/Skins/AdminV2/Img/PopUp/ListePanier.jpg" />-->
		<!--	[BLOC Panneau|position:absolute;left:15%;bottom:370px;width:70%;|position:absolute;background-color:blue;display:block;z-index:50;]
			[BLOC Rounded|position:relative;||padding-top:2px;padding-bottom:2px;]
					<div class="TitrePopUp" style="text-align:left;">
						<span>ATTENTION !</span>
						<img src="/AdminV2/Img/PopUp/ListeFermer.jpg" alt="" style="position:absolute;right:5px;top:-2px;"/>
					</div>
			[/BLOC]
			<div class="PopUpImg" style="float:left;">
				
			</div>
			<div style="margin-left:60px;margin-top:10px;">
					Attention! Vous &ecirc;tes sur le point de supprimer une adresse de facturation.
					Si vous supprimez une adresse, vous ne pourrez pas revenir en arri&egrave;re.<br />
					Voulez-vous continuer ?
			</div>
			<div>
				<div class="Bouton" style="padding-right:80px;">
					<b class="b1"></b>
						<b class="b2">
							<input type="submit" name="detail" value="Valider" onfocus="this.blur()"/>
						</b>
					<b class="b3"></b>
				</div>
				<div class="Bouton">
					<b class="b1"></b>
						<b class="b2">
							<input type="submit" name="panier" value="Annuler" onfocus="this.blur()"/>
						</b>
					<b class="b3"></b>
				</div>
		
			</div>
			[/BLOC]-->
		</a>[/IF]
	</div>
	<div id="Droite"></div>
</div>