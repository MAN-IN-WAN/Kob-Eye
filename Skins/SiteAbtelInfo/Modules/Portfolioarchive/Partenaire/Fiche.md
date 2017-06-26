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
	<div class="row">
		[STORPROC [!Chemin!]/Lien|L]
			<div class="col-md-12">
				<a href="[IF [!R::SiteWeb!]~http][!R::SiteWeb!][ELSE]http://[!R::SiteWeb!][/IF]" title="Voir le site internet [!R::Titre!]" class="WebSite" onclick="window.open(this.href); return false;" >Voir le site </a>
			</div>
		[/STORPROC]
	</div>
	<div class="row"><div class="col-md-12">
		<h3[!R::Titre!]</h3>
	</div></div>
	<div class="row"><div class="col-md-12">
		[!R::Description!]
	</div></div>
	<div class="row"><div class="col-md-12">
		[IF [!R::Icone!]]
			<img src="/[!R::Icone!]" alt="[!R::Titre!]" class="img-responsive" />
		[/IF]
	</div></div>
</div>