<ul class="lvl1">
	<li class="lvl1 [IF [!DepId!]=0] current [/IF] first">
		<a class="lvl1" href="/[!Systeme::getMenu(ParcImmobilier)!]">Toutes nos résidences</a>
		<ul class="lvl2"><li style="display:none"></li></ul>
	</li>
	[STORPROC ParcImmobilier/Departement|D]
		[STORPROC ParcImmobilier/Departement/[!D::Id!]/Ville/Residence/Logement=1&&Reference=0|V|0|100|Nom|ASC|distinct(m.Id),m.*]
			<li class="lvl1 [IF [!DepId!]=[!D::Id!]] current [/IF]">
				<a class="lvl1" href="/[!Systeme::getMenu(ParcImmobilier)!]/Departement/[!D::Lien!]">[!D::Nom!]</a>
				<ul class="lvl2">
					[LIMIT 0|100]
						<li class="lvl2 [IF [!VilleId!]=[!V::Id!]] current [/IF]">
							<a class="lvl2" href="/[!Systeme::getMenu(ParcImmobilier)!]/Departement/[!D::Lien!]/Ville/[!V::Lien!]">[!V::Nom!]</a>
						</li>
					[/LIMIT]
				</ul>
			</li>
		[/STORPROC]
	[/STORPROC]
</ul>