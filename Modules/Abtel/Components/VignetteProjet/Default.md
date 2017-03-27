// Affichage des vignettes projet
[!Req:=Abtel/Entite/*!]
[IF [!ENTITE!]!=]
	[!Req:=Abtel/Entite/[!ENTITE!]!]
[/IF]
<div class="[!NOMDIV!] VignetteProjet row">
	[STORPROC [!Req!]/Projet/Publier=1|P|0|4|tmsCreate|DESC]	
		<div class="pull-left" style="[IF [!P::BackImage!]!=]background:url('[!P::BackImage!]') no-repeat 0 0;[/IF]">
			[IF [!P::URL!]!=]<a href="[!E::URL!]" alt="[!E::Nom!]" target="_blank">[/IF]
			<h2>[!P::Nom!]</h2>
			[IF [!P::URL!]!=]</a>[/IF]
		</div>

	[/STORPROC]
</div>
