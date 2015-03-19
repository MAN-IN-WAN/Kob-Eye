[INFO [!Lien!]|I]
[!LaCat:=!]
[STORPROC [!I::Historique!]|H][/STORPROC]
[IF [!H::Value!]]
	[STORPROC Boutique/Categorie/[!H::Value!]|Cat|0|1]
		[!LaCat:=[!Cat::Url!]!]
	[/STORPROC]
[/IF]
<div class="EntoureComposant">
	<div id="Recherche">
		<form action="/Rechercher" method="get" >
			<div class="LigneForm" >
				[IF [!SEARCH!]]<div style="display:inline-block;float:left;"><input id="RechercheMotCle" name="RechercheMotCle" value="[!RechercheMotCle!]" /></div>[/IF]
				[IF [!TRI!]]
					<div style="display:inline-block;float:left"><select name="RechercheTri" onchange="this.form.submit()">
						<option value="">- Tri -</option>
						<option value="Alphabetique" [IF [!RechercheTri!]=Alphabetique] selected [/IF] >Alphabétique</option>
						<option value="PrixASC" [IF [!RechercheTri!]=PrixASC] selected [/IF]>Prix croissant</option>
						<option value="PrixDESC" [IF [!RechercheTri!]=PrixDESC] selected [/IF]>Prix décroissant</option>
						<option value="News" [IF [!RechercheTri!]=News] selected [/IF]>Nouveautés en premier</option>
						<option value="PlusVisites" [IF [!RechercheTri!]=PlusVisites] selected [/IF]>Produit les plus visités</option>
					</select></div>
				[/IF]
				[IF [!FILTRE!]]
					<div style="display:inline-block;float:left"><select name="RechercheFiltre" onchange="this.form.submit()">
						<option value="">- Filtre -</option>
						<option value="Promotions" [IF [!RechercheFiltre!]=Promotions] selected [/IF]>Uniquement les promotions</option>
						<option value="Coeur" [IF [!RechercheFiltre!]=Coeur] selected [/IF]>Uniquement les coup de coeur</option>
						<option value="IdKdo" [IF [!RechercheFiltre!]=IdKdo] selected [/IF]>Uniquement les idées cadeaux</option>
					</select></div>
				[/IF]
				[IF  [!H::Value!]&&[!Cat::Url!]]<input type="hidden"  value="[!LaCat!]" name="Categorie" />[/IF]
				<input type="submit" id="submitRecherche" value="OK" />
				<input type="hidden" name="TitreListe" value="Résultats de votre recherche" />
	
			</div>
		</form>
	</div>
</div>

// Surcouche JS
<script type="text/javascript">
	window.addEvent('domready', function() {
		FieldDefaultText( $('RechercheMotCle'), 'Rechercher...' );
		$('RechercheMotCle').setStyle('font-style', 'normal');
	});
</script>