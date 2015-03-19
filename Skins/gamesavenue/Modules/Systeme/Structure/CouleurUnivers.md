// Recup couleur des titre en fonction de l'univers
[!MenuDemande:=!]
[INFO [!Lien!]|I]
[STORPROC [!I::Historique!]|H|1|1]
	[!MenuDemande:=[!H::Value!]!]
[/STORPROC]
[!ColorTitre:=#ff0000!]
[!EtoileNotation:=/Skins/gamesavenue/Images/etoile_notation.png!]
[!PuceImage:=!]
[!TraitColor:=#ff0000!]
[IF [!MenuDemande!]!=]
	[STORPROC Boutique/Categorie/Url=[!MenuDemande!]|MSel|0|1|Ordre|ASC]
		[STORPROC Boutique/Univers/Categorie/[!MSel::Id!]|USel|0|1][/STORPROC]
		[!TraitColor:=[!USel::TraitColor!]!]
		[!ColorTitre:=[!USel::TexteColor!]!]
		[!EtoileNotation:=[!USel::EtoileNotation!]!]
		[!PuceImage:=[!USel::PuceImage!]!]
		
	[/STORPROC]
[/IF]	
[HEADER]
<style>
	.blocambiance_puce a {
		background:url(/[!PuceImage!]) no-repeat 0px 3px;
		
	}
	.blocambiance_puce div a {
		background:none;
		
	}
	.blocambiance_color{
		color:[!ColorTitre!]
		
	}
	.blocambiance_border_bottom {
		border-bottom:1px solid [!ColorTitre!];
	}

	.blocambiance_border_right {
		border-right:1px solid [!ColorTitre!];
	}


	.blocambiance_etoile {
		background:url(/[!EtoileNotation!]) no-repeat 40% 30%;
		
	}
	hr {color:[!TraitColor!];margin:10px}
</style>
[/HEADER]