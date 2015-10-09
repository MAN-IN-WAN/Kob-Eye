<div id="RechercheResidence" class="RechercheListeResidence">

	// ! \\ Les "labels" des options sont utilisés en JS

	<form action="/[!Systeme::getMenu(ParcImmobilier)!]" method="get">
		<button type="submit"></button>
		<select name="RR_Dep" id="RechercheResidenceDepartement">
			<option title="" value="">Région sud France</option>
			[STORPROC ParcImmobilier/Departement|D]
				[STORPROC ParcImmobilier/Departement/[!D::Id!]/Ville/Residence/Logement=1&&Reference=0|V|0|100|Nom|ASC|distinct(m.Id),m.*]
					<option title="[!D::Id!]" value="[!D::Id!]" [IF [!RR_Dep!]=[!D::Id!]||[!DepId!]=[!D::Id!]] selected="selected" [/IF]>[!D::Nom!]</option>
					[LIMIT 0|100][/LIMIT]
				[/STORPROC]
			[/STORPROC]
		</select>
		<select name="RR_Ville" id="RechercheResidenceVille">
			<option title="" value="">Ville</option>
			[STORPROC ParcImmobilier/Departement/*/Ville/Residence/Logement=1&&Reference=0|V|0|100|Nom|ASC|distinct(m.Id),m.*,j0.Id as DepId]
				<option value="[!V::Id!]" title="[!V::DepId!]" [IF [!RR_Ville!]=[!V::Id!]||[!VilleId!]=[!V::Id!]] selected="selected" [/IF]>[!V::Nom!]</option>
			[/STORPROC]
		</select>
		<select name="RR_Type">
			<option value="">Type de logement</option>
//			<option value="Studio" [IF [!RR_Type!]=Studio] selected="selected" [/IF]>Studio</option>
			<option value="T1" [IF [!RR_Type!]=T1] selected="selected" [/IF]>Appartement 1 pièce</option>
			<option value="T2" [IF [!RR_Type!]=T2] selected="selected" [/IF]>Appartement 2 pièces</option>
			<option value="T3" [IF [!RR_Type!]=T3] selected="selected" [/IF]>Appartement 3 pièces</option>
			<option value="T4" [IF [!RR_Type!]=T4] selected="selected" [/IF]>Appartement 4 pièces</option>
			<option value="T5" [IF [!RR_Type!]=T5] selected="selected" [/IF]>Appartement 5 pièces et +</option>
//			<option value="T6" [IF [!RR_Type!]=T6] selected="selected" [/IF]>Appartement 6 pièces</option>
//			<option value="Villa" [IF [!RR_Type!]=Villa] selected="selected" [/IF]>Villa</option>
		</select>
		<select name="RR_Prix">
			<option value="">Prix</option>
			<option value="T1" [IF [!RR_Prix!]=T1] selected="selected" [/IF]>&lt; 120.000 euros</option>
			<option value="T2" [IF [!RR_Prix!]=T2] selected="selected" [/IF]>121.000 - 160.000 euros</option>
			<option value="T3" [IF [!RR_Prix!]=T3] selected="selected" [/IF]>161.000 - 190.000 euros</option>
			<option value="T4" [IF [!RR_Prix!]=T4] selected="selected" [/IF]>191.000 - 260.000 euros</option>
			<option value="T5" [IF [!RR_Prix!]=T5] selected="selected" [/IF]>261.000 - 350.000 euros</option>
			<option value="T6" [IF [!RR_Prix!]=T6] selected="selected" [/IF]>&gt; 350.000 euros</option>
		</select>
	</form>
</div>