<!--- contenu central décomposé en deux étages-->
[INFO [!Lien!]|I]
[STORPROC [!I::Historique!]|H|0|[!I::NbHisto!]]
	[!MenuDemande:=[!H::Value!]!]
[/STORPROC]
[COUNT Boutique/Categorie/Url=[!MenuDemande!]|P]
[IF [!P!]>0]
	[STORPROC Boutique/Categorie/Url=[!MenuDemande!]|Cat|0|1]
		[IF [!Cat::Image!]=]
			<div class="centrePartieHaut">[MODULE Systeme/Structure/Recherche_top]</div>
		[ELSE]
			<div class="centrePartieHaut" style="background:url(/[!Cat::Image!]) no-repeat 0 40px;">
				<form action="">
					[MODULE Systeme/Structure/Recherche_top]
				</form>
			</div>
		[/IF]
	[/STORPROC]
[ELSE]
	<div class="centrePartieHaut"></div>
[/IF]
<div class="centrePartieBas">
	<div class="colonne">[MODULE Boutique/Interface/TopVentes]</div>
	<div class="colonne">[MODULE Boutique/Interface/TopPromos]</div>
	<div class="colonne255">
		[MODULE Boutique/Interface/CoupCoeur]
		<div class="bloccarregris"><p class="p10">
			//[MODULE Publicite/PubContenu]
		</p></div>
	</div> <!-- fin colonne255 -->
</div>   <!-- fin  centrePartieBas -->

