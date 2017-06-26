[STORPROC [!Chemin!]|R|0|1][/STORPROC]

[HEADER]
	<link rel="canonical" href="[!Domaine!]/[!Lien!]" />
[/HEADER]

[TITLE][!R::Titre!] - [!R::Chapo!][/TITLE]

[DESCRIPTION][!R::Chapo!] - [!R::Description!][/DESCRIPTION]

<div id="headerRefs" class="articleHeader" [IF [!Et::CodeCouleur!]]style="background-color:[!Et::CodeCouleur!];"[/IF] >
	<div class="container"><h1>[!R::Titre!]</h1></div>
</div>

<div class="FicheReference container">
	<div class="row"><div class="col-md-12">
	 	<h1>[!R::Titre!]</h1>	
		<h2>[!R::Chapo!]</h2>
		<div class="Description">[!R::Description!]</div>
	</div></div>

	[IF [!R::Icone!]]
		<div class="row"><div class="col-md-12">
			<img src="/[!R::Icone!]" alt="[!R::Titre!]" class="img-responsive" />
		</div></div>
	[/IF]
</div>