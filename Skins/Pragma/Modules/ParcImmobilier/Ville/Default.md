[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|N0|0|1][/STORPROC]
[STORPROC [!I::Historique!]|N1|1|1][/STORPROC]

<div style="overflow:hidden">
[IF [!N0::Type!]=Direct]
	[IF [!N1::DataSource!]=Ville]
		// Ville + Departement
		[STORPROC [!N0::Query!]|V][/STORPROC]
		[!VilleId:=[!V::Id!]!]
		[STORPROC ParcImmobilier/Departement/Ville/[!V::Id!]|D][/STORPROC]
		[!DepId:=[!D::Id!]!]
		<h1>Toutes nos résidences - [!D::Nom!] - [!V::Nom!]</h1>
	[ELSE]
		// Departement seulement
		[STORPROC [!N0::Query!]|D|0|1]
			[!DepId:=[!D::Id!]!]
			<h1>Toutes nos résidences - [!D::Nom!]</h1>
			[NORESULT]
				[!DepId:=0!]
				<h1>Toutes nos résidences</h1>
			[/NORESULT]
		[/STORPROC]
		[!VilleId:=0!]
	[/IF]
[ELSE]
	// Url par défaut
	[IF [!RR_Dep!]!=||[!RR_Ville!]!=||[!RR_Type!]!=||[!RR_Prix!]!=||]
		[!SfxRecherche:=RR_Dep=[!RR_Dep!]&RR_Ville=[!RR_Ville!]&RR_Type=[!RR_Type!]&RR_Prix=[!RR_Prix!]!]
		[!DepId:=[!RR_Dep!]!]
		[!VilleId:=[!RR_Ville!]!]
		<h1>Résultats de la recherche</h1>
	[ELSE]
		[!DepId:=0!]
		[!VilleId:=0!]
		<h1>Toutes nos résidences</h1>
	[/IF]
[/IF]
</div>

<div style="overflow:hidden">
	<div id="MenuResidence" class="arial">
		[MODULE ParcImmobilier/Residence/MenuListe]
	</div>
	<div id="ListeResidence" class="arial">
		<div style="overflow:hidden"> [MODULE ParcImmobilier/Residence/Recherche] </div>
		<div id="ListeResidenceBordered">[MODULE ParcImmobilier/Residence/Liste]</div>
	</div>
</div>