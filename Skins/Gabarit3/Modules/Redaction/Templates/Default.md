[MODULE Systeme/Structure/Droite]
[STORPROC [!Query!]|Cat]
	[TITLE][!Cat::Nom!][IF [!Cat::Chapo!]!=] - [!Cat::Chapo!][/IF][/TITLE]
	[DESCRIPTION][SUBSTR 100][!Cat::Description!][/SUBSTR][/DESCRIPTION]
	[INFO [!Query!]|Inf]
	[STORPROC [!Inf::Historique!]|H|0|1]
		[!Niv0:=[!H::Value!]!]
	[/STORPROC]
	<div id="Milieu">
		<div id="Data">
			<div class="TitreCat">
				<div class="TextDefault">
					<h1>[!Cat::Nom!]</h1>
					<p class="Description">[!Cat::Description!]</p>
				</div>
			</div>

			[STORPROC Redaction/Categorie/[!Cat::Id!]/Article/Publier=1|Art|0|20|Ordre|ASC]
				[!Art::Titre!]<br/>
				[!Art::Chapo!]<br/>
				[!Art::Contenu!]<br/>
			[/STORPROC]
		</div>
	</div>
	<div class="Clear"></div>
[/STORPROC]