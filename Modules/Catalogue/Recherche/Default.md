<div class="RechercheProduit">

	<form action="/[!Lien!]" method="get" class="CatalogueRecherche">
		<div class="ChampRecherche">
			<input id="RechercheMotCle" name="RechercheMotCle" value="[!RechercheMotCle!]" />
			<input type="submit" id="submitRecherche" value="OK" class="Ok" />
		</div>
		<div class="ChampRecherche">
			<select name="RechercheTri" onchange="this.form.submit()">
				<option value="" selected="selected" >- Tri -</option>
				<option value="Alphabetique" [IF [!RechercheTri!]=Alphabetique] selected [/IF] >Alphabétique</option>
				<option value="PrixASC" [IF [!RechercheTri!]=PrixASC] selected [/IF]>Prix croissant</option>
				<option value="PrixDESC" [IF [!RechercheTri!]=PrixDESC] selected [/IF]>Prix décroissant</option>
				<option value="News" [IF [!RechercheTri!]=News] selected [/IF]>Nouveautés en premier</option>
			</select>
		</div>
		<div class="ChampRecherche">
			<select name="RechercheFiltre" onchange="this.form.submit()">
				<option value="" selected="selected" >- Fabricants -</option>
				[COUNT [!Query!]/Produit|NbProd]
				[IF [!NbProd!]]
					[!Requete:=[!Query!]/Produit!]
				[ELSE]
					[!Requete:=[!Query!]/Categorie/*/Produit!]
				[/IF]
				[STORPROC [!Requete!]|FabP|||Fabricant|ASC|DISTINCT(m.Fabricant)]
					[STORPROC Catalogue/Fabricant/[!FabP::Fabricant!]|Fab|||Nom|ASC]
						<option value="[!Fab::Url!]" [IF [!RechercheFiltre!]=[!Fab::Url!]] selected="selected" [/IF] >[!Fab::Nom!]</option>
					[/STORPROC]
				[/STORPROC]
			</select>
		</div>
	</form>
</div>
// Surcouche JS
<script type="text/javascript">
	window.addEvent('domready', function() {
		FieldDefaultText( $('RechercheMotCle'), 'Rechercher...' );
		$('RechercheMotCle').setStyle('font-style', 'normal');
	});
</script>