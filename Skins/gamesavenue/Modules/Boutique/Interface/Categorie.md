// ici s'affichera la navigation en fonction de la categorie
[MODULE Systeme/Structure/CouleurUnivers]
[!MenuDemande:=!]
[INFO [!Lien!]|I]
[STORPROC [!I::Historique!]|H|1|1]
	[!MenuDemande:=[!H::Value!]!]
[/STORPROC]
<div class="colonne225" >
	<div class="interieurColonneGrise">
		[IF [!MenuDemande!]!=]
			[STORPROC Boutique/Categorie/[!MenuDemande!]|Cat|0|1|Ordre|ASC]
				[IF [!Cat::Icone!]!=]
					<img src="/[!Cat::Icone!]" height=133px">
					[!Affichage:=1!]
				[/IF]
				[MODULE Boutique/Interface/SousCategorie?O=[!Cat!]&U=/[!Systeme::CurrentMenu::Url!]/[!MenuDemande!]]
			[/STORPROC]
		[/IF]
		[IF [!Affichage!]=1]<div class="fincolonneGrise"></div>[/IF]
	</div>
</div>
