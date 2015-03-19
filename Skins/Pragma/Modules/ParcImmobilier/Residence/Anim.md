<div id="AnimDetail">
	<div id="AnimCadre" class="siteWidth">
		[STORPROC [!Query!]/Donnee/Type=Perspective|Pers|0|100|Titre|ASC]
 			<img src="/[!Pers::URL!]" alt="[!Pers::Titre!]" />
		[/STORPROC]
	</div>
	<div id="AnimDetailMasque" style="background:url('/[!R::MasqueHTML!]') no-repeat"></div>
	<div id="AnimDetailLegende" class="arial">
		<div class="DateLivraison">
			Date de livraison : [!R::DateLivraison!]
		</div>
		<div class="Pictos">
			[STORPROC ParcImmobilier/PictoResidence/Residence/[!R::Id!]|PR]
				<img src="/[!PR::Picto!]" alt="[!PR::Titre!]" title="[!PR::Titre!]" />
			[/STORPROC]
		</div>
	</div>
	[IF [!R::BBC!]=1]
		<div id="LogoBBC" alt="Batiment Basse Consommation" title="Batiment Basse Consommation"></div>
	[/IF]
</div>