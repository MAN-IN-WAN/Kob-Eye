<!--Boutique/Menu/Pied -->
<div class="LigneTitreBlanc">
	<div class="TitreArticleBlanc"><h1>Consoles et autres</h1></div>
	<div class="traitBlanc">&nbsp;</div>
</div>
<div class="ColSousMenuGauche">
	<!-- element du sous menu limité à 5 lignes dans la maquette -->
	[STORPROC Boutique/Categorie/Menu=1|Men|0|5|Ordre|ASC]
		[IF [!Men::NomLong!]!=]
			<div class="elementMenuPied"><a href="/GamesAvenue/[!Men::Url!]" >- [!Men::NomLong!]</a></div>
		[ELSE]
			<div class="elementMenuPied"><a href="/GamesAvenue/[!Men::Url!]" >- [!Men::Nom!]</a></div>
		[/IF]
	[/STORPROC]
</div>
<div class="ColSousMenuDroite">
	<!-- element du sous menu limité à 5 lignes dans la maquette -->
	[STORPROC Boutique/Categorie/Menu=1|Men|5|10|Ordre|ASC]
		[IF [!Men::NomLong!]!=]
			<div class="elementMenuPied"><a href="/GamesAvenue/[!Men::Url!]" >- [!Men::NomLong!]</a></div>
		[ELSE]
			<div class="elementMenuPied"><a href="/GamesAvenue/[!Men::Url!]" >- [!Men::Nom!]</a></div>
		[/IF]
	[/STORPROC]
</div>
