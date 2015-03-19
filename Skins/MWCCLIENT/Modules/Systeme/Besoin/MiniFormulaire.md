[IF [!SendForm!]!=]
		<div class="blocMessage">
			<h3>Demande envoyée.</h3>
		</div>
[/IF]


<form id="CpBesoin" method="post" action="/[!Lien!]">
	<div class="LigneForm">
		<label>Chardonnay Blanc</label>
	</div>
	<div class="LigneForm">
		<label>Quantité disponible</label>
		<input type="text" name="C_Qte" value="[!C_Qte!]"/>
	</div>
	<div class="LigneForm">
		<label>Tarif</label>
		<input type="text" name="C_Tarif" value="[!C_Tarif!]"/>
	</div>
	<div class="Buttons">
		<button type="submit">Valider</button>
		<input type="hidden" name="SendForm" value="1" />
	</div>
</form>

