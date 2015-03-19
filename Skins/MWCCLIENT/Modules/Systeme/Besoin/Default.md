<form id="CpBesoin" method="post" action="/[!Lien!]" style="width:659px;margin:auto;">
	[IF [!SendForm!]||[!SendFormInfos!]]
			<div class="LigneForm">
			<label class="libelle">Chardonnay Blanc</label>
		</div>
		<div class="LigneForm">
			<label>Quantité disponible (hl)</label>
			<input type="text" name="C_Qte" value="[!C_Qte!]"/>
		</div>
		<div class="LigneForm">
			<label>Tarif (€/hl)</label>
			<input type="text" name="C_Tarif" value="[!C_Tarif!]"/>
		</div>
		<div class="Buttons">
			<button type="submit">Valider</button>
			<input type="hidden" name="SendFormInfos" value="1" />
		</div>
	[ELSE]
		<div class="ButtonsDebut">
			<button type="submit">Faire votre demande</button>
			<input type="hidden" name="SendForm" value="1" />
		</div>
	[/IF]

</form>

[IF [!SendFormInfos!]]
	<div class="blocMessage" style="width:659px;margin:auto;">
		<h3>Votre Demande : Chardonnay Blanc a bien été prise en compte.</h3>
	</div>
[/IF]
