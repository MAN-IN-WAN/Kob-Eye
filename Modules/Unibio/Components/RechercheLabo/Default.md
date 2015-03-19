	<div class="EntoureComposant">
		<div class="EnteteComposant EnteteRechercheLabo">
			Nos laboratoires
		</div>
		<div class="ContenuComposant ContenuRechercheLabo">
			Rechercher votre laboratoire
			<form method="get" action="/Nos-Laboratoires">
				<select name="Zone">
					<option value="">- Veuillez sélectionner -</option>
					[STORPROC Unibio/Region|R]
						<option [IF [!Zone!]=[!R::Url!]] selected="selected" [/IF] value="[!R::Url!]">[!R::Nom!]</option>
					[/STORPROC]
				</select>
				<button class="RechercherBtn" type="submit">Rechercher</button>
			</form>
			<a class="TousLesLabos" href="/Nos-Laboratoires">Voir tous les labos</a>
		</div>
	</div>