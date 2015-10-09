[STORPROC [!Query!]|Prod|0|1][/STORPROC]
<div class="BlocFichCaracteristique" >
	[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Caracteristique|CAR|||Ordre|ASC]
		[LIMIT 0|100]
			<div class="BlocFichLigneCaract">
				[IF [!CAR::TypeCaracteristique!]!=]<span class="TitreCaract" >[!CAR::TypeCaracteristique!] : </span><br />[/IF]
				<span class="FichValeurCaract">[!CAR::Valeur!]</span>
			</div>
		[/LIMIT]
	[/STORPROC]
</div>
