<h1>Les Simulateurs</h1>
<div class="SimulateurComplet">
	<div class="Budget" style="border-right:1px dotted #ccc;">
		<form name="formbien" method="post" action="/[!Lien!]" class="formbien">
			<div class="LigneForm"><h3>SIMULATEUR DU BUDGET</h3></div>
			<div class="LigneForm">Calculez le montant de votre emprunt pour l'achat d'un bien, simplement et sans modalité d'inscription.</div>
			<div class="LigneForm">
				<label>Prix de votre bien</label>
				<button type="button" name="Moins"  onclick="modifValeur(document.formbien.prix_bien,-1000);return false;" >-</button>
				<input type="text" size="8"  name="prix_bien" onChange="javascript:calculBien(document.formbien);" value="[!Prix!]" >
				<button type="button" name="Plus"  onclick="modifValeur(document.formbien.prix_bien,+1000);return false;"  >+</button>
			</div>
			<div class="LigneForm">
				<label>Votre apport personnel</label>
				<button type="button" name="MoinsApp"   onclick="modifValeur(document.formbien.apport_perso,-1000);return false;">-</button>
				<input type="text" size="8"  name="apport_perso" onChange="javascript:calculBien(document.formbien);" value="0" >
				<button type="button" name="PlusApp"  onclick="modifValeur(document.formbien.apport_perso,1000);return false;" >+</button>
			</div>
			<div class="LigneForm">
				<label>Somme &agrave; financer</label>
				<input type="text" style="width:127px;"  size="8" name="somme_finance" onChange="javascript:calculBien(document.formbien);" readonly>
			</div>
			<div class="LigneForm">
				<label>Mensualit&eacute;</label>
				<button type="button" name="MoinsMens" onclick="modifValeur(document.formbien.mensualite,-100);return false;">-</button>
				<input type="text" size="8"  name="mensualite" onChange="javascript:calculBien(document.formbien);" value="0" >
				<button type="button" name="PlusMens"  onclick="modifValeur(document.formbien.mensualite,100);return false;" >+</button>
			</div>
			<div class="LigneForm">
				<label>Dur&eacute;e (ans)</label>
				<button type="button" name="MoinsDur" onclick="modifValeur(document.formbien.duree,-1);return false;">-</button>
				<input type="text" size="8"  name="duree" onChange="javascript:calculBien(document.formbien);" value="15" >
				<button type="button" name="PlusDur"  onclick="modifValeur(document.formbien.duree,1);return false;" >+</button>
			</div>
			<div class="LigneForm">
				<label>Taux</label>
				<button type="button" name="MoinsTx"  onclick="modifValeur(document.formbien.taux,-0.1);return false;">-</button>
				<input type="text" size="8"  name="taux" onChange="javascript:calculBien(document.formbien);" value="3.9" >
				<button type="button" name="PlusTx"  onclick="modifValeur(document.formbien.taux,0.1);return false;" >+</button>
			</div>
			<div class="LigneForm">
				<label>Co&ucirc;t de votre emprunt</label>
				<input type="text"  style="width:127px;"  size="8" name="cout_emprunt" onChange="javascript:calculBien(document.formbien);" readonly>
			</div>
		</form>
	</div>
	<div class="LoiDuflot" style="border:none;" >
		<form id="simulateurForm" action="/[!Lien!]" class="formbien">
			<div class="LigneForm"><h3>BIENTÔT SIMULATEUR LOI DUFLOT</h3></div>

		</form>
	</div>
	
</div>

