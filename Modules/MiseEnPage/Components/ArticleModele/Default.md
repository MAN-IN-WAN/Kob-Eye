<div class="[!NOMDIV!]">
	[IF [!FORCEMODELE!]!=]
		[MODULE MiseEnPage/Modeles/[!FORCEMODELE!]?Chemin=MiseEnPage/Categorie/[!CATEGORIE!]]
	[ELSE]
		
		[STORPROC MiseEnPage/Categorie/[!CATEGORIE!]|Cat|0|1]	
			[IF [!Cat::Modele!]=]
				[MODULE MiseEnPage/Modeles/Default?Chemin=MiseEnPage/Categorie/[!CATEGORIE!]]
			[ELSE]
				[MODULE MiseEnPage/Modeles/[!Cat::Modele!]?Chemin=MiseEnPage/Categorie/[!CATEGORIE!]]
			[/IF]
		[/STORPROC]
	[/IF]
</div>
