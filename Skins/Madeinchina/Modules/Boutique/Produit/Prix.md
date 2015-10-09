[STORPROC [!Query!]|Prod|0|1][/STORPROC]
[!Promo:=0!]
[!Promo:=[!Prod::GetPromo!]!]

<div class="BlocFichPrix">
	// Gestion visibilité à partir de
	[IF [!P::getTarif!]=0]
		<div class="PrixDansFiche" id="tarif">Gratuit</div>
	[ELSE]
		<div class="PrixDansFiche" id="tarif">[IF [!P::MultiTarif!]]<span style="font-size:10px;" id="apartir">à partir de</span>[/IF] [!Math::PriceV([!P::getTarif!])!] [!CurrentDevise::Sigle!]</div>
		[IF [!Prod::TypeProduit!]=5]
			<div class="PrixALunite" id="tarifunite" >soit [!Math::PriceV([!Prod::getTarif!])!] [!CurrentDevise::Sigle!] l'unité</div>
		[/IF]
		[IF [!Prod::TypeProduit!]!=5]
			// ici il faudra mettre le produit ht hors promo
			[IF [!Prod::getTarif!]<[!Prod::getTarifHorsPromo!]]
				<div class="Prixremise">prix initial <span class="barre">[!Math::PriceV([!Prod::getTarifHorsPromo!])!] [!CurrentDevise::Sigle!]</span></div>
			[/IF]
		[/IF]
	[/IF]
</div>

//[IF [!Promo!]!=0&&[!Promo!]!=]
//	<div id="tarifNonPromo">Au lieu de <span class="barre">[!Math::PriceV([!Prod::getTarifHorsPromo!])!][!CurrentDevise::Sigle!]</span></div>
//[/IF]
//[IF [!P::PPC!]!=0&&[!P::PPC!]!=]
//	<div id="tarifPPC">Prix public conseillé : [!Math::PriceV([!P::PPC!])!] [!CurrentDevise::Sigle!] TTC</div>
//[/IF]

