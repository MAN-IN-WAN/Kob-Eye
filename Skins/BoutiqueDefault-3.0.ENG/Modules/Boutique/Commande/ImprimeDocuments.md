[STORPROC [!Query!]|CDE][/STORPROC]
[STORPROC [!Query!]/Facture|FA][/STORPROC]
[!BL:=[!CDE::getBonLivraison!]!]

[TITLE]Admin Kob-Eye | Impression documents de la commande[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
<div id="Container">
	<form action="" method="post" name="rech[!Test::TypeChild!]" class="FormRech">
		<div id="Arbo">
			[BLOC Panneau]
			[/BLOC]
		</div>
		<div id="Data">
			[BLOC Panneau]
				<h1>Impressions pour la Commande [!CDE::RefCommande!]</h1>
				<div style="margin:50px;font-size:15px;font-weight:bold;text-decoration:underline;">
					<a href="/LivraisonStock/BonLivraison/[!BL::Id!]/BonDeLivraison"  rel="link"  >Imprimer le bon de livraison</a>
				</div>
				<div style="margin:50px;font-size:15px;font-weight:bold;text-decoration:underline;">
					<a href="/Boutique/Facture/[!FA::Id!]/FacturePdf"  rel="link"    >Imprimer la facture</a>
				</div>
				[IF [!BL::Etiquette!]=][!BL::updateInfosLivraison()!][/IF]
				[IF [!BL::Etiquette!]!=]
					<div style="margin:50px;font-size:15px;font-weight:bold;text-decoration:underline;">
						<a href="[!BL::Etiquette!]"  rel="link"   >Imprimer l'étiquette du colis</a>
					</div>
				[/IF]
			[/BLOC]
		</div>
	</form>
</div>
