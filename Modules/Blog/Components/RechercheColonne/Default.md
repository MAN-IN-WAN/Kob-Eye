<div class="[!NOMDIV!]" style="padding-bottom:[!PADDINGBOTTOM!]px">
	<div class="EnteteNavigation">
		[!TITRE!]
	</div>
	<div class="ContenuComposantNavigation">	
		<div id="Recherche">
			<form action="/[!Systeme::getMenu(Blog/Recherche)!]" method="get" >
				<div class="LigneForm">
					<input id="RechercheMotCle" name="RechercheMotCle" value="[!RechercheMotCle!]" />
					<input type="submit" id="submitRecherche" value="OK" />
					<input type="hidden" name="TitreListe" value="Résultats de votre recherche" />
		
				</div>
			</form>
		</div>
	</div>
</div>

// Surcouche JS
<script type="text/javascript">
	window.addEvent('domready', function() {
		FieldDefaultText( $('RechercheMotCle'), 'Rechercher...' );
	});
</script>