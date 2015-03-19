[INFO [!Query!]|Inf]
[STORPROC [!Inf::Historique!]|H|0|1]
	[!Niv0:=[!H::Value!]!]
[/STORPROC]
[STORPROC [!Query!]|Cat|0|1][/STORPROC]
[MODULE Systeme/Structure/PhotoEntete]
[MODULE Systeme/Structure/Droite?Cata=[!Niv0!]]
<div id="Milieu" style="padding-top:0;">
	<div id="BlocAccueil">
		<h1>[!Cat::Nom!]</h1>
		<p>[!Cat::Description!]</p>
	</div>
	<div id="Services">
		[STORPROC Redaction/Categorie/[!Cat::Id!]/Article/Publier=1|Art|0|4|Ordre|ASC]
			<div class="Prestations" style="[IF[!Math::Floor([!Pos:/2!])!]==[!Pos:/2!]]border-left:1px dashed #B2B2B2;[/IF];">
				<div style="overflow:hidden;display:block;position:relative;clear:both;">
					[STORPROC Redaction/Article/[!Art::Id!]/Image|ArtImg]
						<img src="/[!ArtImg::URL!]" alt="[!ArtImg::Titre!]" style="margin-left:10px;width:40px;float:left;"/>
					[/STORPROC]
					<h2>[!Art::Titre!]</h2>
					<span>[!Art::Chapo!]</span>
				</div>
				<p>[!Art::Contenu!]</p>
				<div style="overflow:auto;width:100%;">
				[STORPROC Redaction/Article/[!Art::Id!]/Lien|AccLien]
					[IF [!AccLien::Icone!]!=]
						<img src="/[!AccLien::Icone!]" alt="[!AccLien::Titre!]" class="LienIcone" style="width:30px;"/>
					[/IF]
					<a href="/[!AccLien::URL!]" title="[!Art::Titre!] : [!AccLien::Titre!]" class="PrestaLien">[!AccLien::Titre!]</a>
				[/STORPROC]
				</div>
			</div>
		[/STORPROC]
	</div>
</div>
<div class="Clear"></div>