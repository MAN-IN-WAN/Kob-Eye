[STORPROC [!Chemin!]|R|0|1][/STORPROC]

[HEADER]
<link rel="canonical" href="[!Domaine!]/[!Lien!]" />
[/HEADER]

[STORPROC Portfolio/Clients/Reference/[!R::Id!]|Cli][/STORPROC]

[TITLE][!R::Titre!] - [!R::Chapo!][/TITLE]

[DESCRIPTION]Référence client [!Cli::Nom!] : [!R::Chapo!] - [!R::Description!][/DESCRIPTION]

//<div id="headerRefs" class="articleHeader" [IF [!Et::CodeCouleur!]]style="background-color:[!Et::CodeCouleur!];"[/IF] >
//	<div class="container"><h1>[!R::Titre!]</h1></div>
//</div>

<div class="FicheReference container">
	<div class="row">
		<div class="col-md-[IF [!Cli::Description!]!=]6[ELSE]12[/IF]">
			//	 	<h1>[!R::Titre!]</h1>
			<h2>[!R::Chapo!]</h2>
			<div class="Description">[!R::Description!]</div>
		</div>
        [IF [!Cli::Description!]!=]
		<div class="col-md-6">
			<h2>Client</h2>
			<h3[!Cli::Nom!]</h3>
			<div class="cliDesc">
				[!Cli::Description!]
			</div>
		</div>
        [/IF]
	</div>
		<div class="row liensite">
			[STORPROC [!Chemin!]/Lien|L]
				<div class="col-md-12">
					<a href="[IF [!L::URL!]~http][!L::URL!][ELSE]http://[!L::URL!][/IF]" title="Voir le site internet [!R::Titre!]" class="WebSite" onclick="window.open(this.href); return false;" [IF [!L::NoFollow!]] rel="nofollow"[/IF]>Voir le site </a></div>
				[NORESULT][/NORESULT]
			[/STORPROC]
		</div>

	<h2>Technologies</h2>
	<div class="row">
		<div class="col-md-12">
			[IF [!R::Php!]=1]
			<abbr title="Personal Home Page">PHP</abbr>
			[/IF]
			[IF [!R::Joomla!]=1]
			Joomla CMS
			[/IF]
			[IF [!R::Kobeye!]=1]
			Plate forme Kob-Eye
			[/IF]
			[IF [!R::Xhtml!]=1]
			<abbr title="eXtensible HyperText Markup Language">Xhtml</abbr>
			[/IF]
			[IF [!R::Html!]=1]
			<abbr title="HyperText Markup Language">Html</abbr>
			[/IF]
			[IF [!R::Css!]=1]
			<abbr title="Cascading Style Sheets">CSS</abbr>
			[/IF]
			[IF [!R::Flash!]=1]
			Flash
			[/IF]
			[IF [!R::Javascript!]=1]
			Javascript
			[/IF]
			[IF [!R::XML!]=1]
			<abbr title="Extensible Markup Language">XML</abbr>
			[/IF]
			[IF [!R::SOAP!]=1]
			<abbr title="Simple Object Access Protocol">SOAP</abbr>
			[/IF]
			[IF [!R::Administration!]=1]
			Site administrable
			[/IF]
		</div>
	</div>
	[IF [!R::Moyens!]!=]
	<h2>Réalisation</h2>

	<div class="moyens">
			[!R::Moyens!]
	</div>
	[/IF]
	<div class="row">
		<div class="col-md-12">
			[STORPROC Portfolio/Reference/[!R::Id!]/Donnee/Type=image|Img]
			<img src="/[!Img::Url!]" alt="[!Img::Titre!]" class="img-responsive"  />
			[IF [!Pos!]=[!NbResult!]]
			[ELSE]
			<br />
			[/IF]
			[NORESULT]
			[IF [!R::Icone!]]
			<img src="/[!R::Icone!]" alt="[!R::Titre!]" class="img-responsive" />
			[/IF]
			[/NORESULT]
			[/STORPROC]
		</div>
	</div>
</div>