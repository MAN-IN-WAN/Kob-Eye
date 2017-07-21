[!Req:=[!REQUETE!]!]
[IF [!REQUETE!]=]
	[!Req:=MiseEnAvant/Categorie/[!CATEGORIE!]/InfoEnAvant/Publier=1!]
	[!Req2:=MiseEnAvant/Categorie/[!CATEGORIE!]/InfoEnAvant!]
	
[/IF]
[STORPROC [!Req!]|Inf|0|1|Ordre|ASC] [/STORPROC]
[!ReqLi:=[!Req2!]/[!Inf::Id!]/Donnee/Type=Lien!]

<div CLASS="[!NOMDIV!]">
	[STORPROC [!Req!]|Inf|||Ordre|ASC]
		[IF [!Inf::Image!]!=]
			<div class="AfficheImage ">
				[IF [!LienUne!]!=0]<a href="[IF [!Li::UrlLien!]~http][ELSE]/[/IF][!Li::UrlLien!]" alt="[!Li::Titre!]">[/IF]
					<img src="[!Domaine!]/[!Inf::Image!]" alt="[!Inf::Titre!]" class="img-responsive" >
				[IF [!LienUne!]!=0]</a>[/IF]
			</div>
		[/IF]
	[/STORPROC]
</div>
