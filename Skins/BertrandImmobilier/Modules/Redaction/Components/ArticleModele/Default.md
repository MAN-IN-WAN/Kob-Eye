<div class="[!NOMDIV!]">
	[IF [!FORCEMODELE!]!=]
		[MODULE Redaction/Modeles/[!FORCEMODELE!]?Chemin=Redaction/Categorie/[!CATEGORIE!]]
	[ELSE]
		
		[STORPROC Redaction/Categorie/[!CATEGORIE!]|Cat|0|1]	
			[IF [!Cat::Modele!]=]
				[MODULE Redaction/Modeles/Default?Chemin=Redaction/Categorie/[!CATEGORIE!]]
			[ELSE]
				[MODULE Redaction/Modeles/[!Cat::Modele!]?Chemin=Redaction/Categorie/[!CATEGORIE!]]
			[/IF]
		[/STORPROC]
	[/IF]
</div>
