[STORPROC [!Query!]|BL][/STORPROC]
[TITLE]Admin Kob-Eye | Impression Bon de livraison[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
<div id="Container">
	<div id="Arbo">
		[BLOC Panneau]
		[/BLOC]
	</div>
	<div id="Data">
		[BLOC Panneau]
			<h1>Impression Bon de livraison </h1>
			<div style="margin:50px;font-size:15px;font-weight:bold;text-decoration:underline;">
				<a href="/LivraisonStock/BonLivraison/[!BL::Id!]/BonDeLivraison"  rel="link"  >Imprimer le bon de livraison</a>
			</div>
			[IF [!BL::Etiquette!]=][!BL::updateInfosLivraison()!][/IF]
			[IF [!BL::Etiquette!]!=]
				<div style="margin:50px;font-size:15px;font-weight:bold;text-decoration:underline;">
					<a href="[!BL::Etiquette!]" rel="link"   >Imprimer l'étiquette du colis</a>
				</div>
			[/IF]

		[/BLOC]
	</div>
</div>

