[STORPROC [!Chemin!]|R|0|1][/STORPROC]

[HEADER]
	<link rel="canonical" href="[!Domaine!]/[!Lien!]" />
[/HEADER]

[STORPROC Portfolio/Clients/Reference/[!R::Id!]|Cli][/STORPROC]

[TITLE][!R::Titre!] - [!R::Chapo!][/TITLE]

[DESCRIPTION]Référence client [!Cli::Nom!] : [!R::Chapo!] - [!R::Description!][/DESCRIPTION]

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
				<a href="[IF [!L::URL!]~http][!L::URL!][ELSE]http://[!L::URL!][/IF]" title="Voir le site internet [!R::Titre!]" class="WebSite" onclick="window.open(this.href); return false;" [IF [!L::NoFollow!]] rel="nofollow"[/IF]>Voir le site </a></div>
			[NORESULT][/NORESULT]				
		[/STORPROC]
	</div>
	<div class="row"><div class="col-md-12">
		<h2>Client</h2>
	</div></div>
	<div class="row"><div class="col-md-12">
		<h3[!Cli::Nom!]</h3>
	</div></div>
	<div class="row"><div class="col-md-12">
		[!Cli::Description!]
	</div></div>
	<div class="row"><div class="col-md-12">
		<h2>Technologies</h2>
	</div></div>
	<div class="row"><div class="col-md-12">
		[IF [!R::Php!]=1]
			<acronym title="Personal Home Page">PHP</acronym>
		[/IF]
		[IF [!R::Joomla!]=1]
			&nbsp;Joomla CMS&nbsp;
		[/IF]
		[IF [!R::Kobeye!]=1]
			&nbsp;Plate forme Kob-Eye&nbsp;
		[/IF]
		[IF [!R::Xhtml!]=1]
			&nbsp;<acronym title="eXtensible HyperText Markup Language">Xhtml</acronym>&nbsp;
		[/IF]
		[IF [!R::Html!]=1]
			&nbsp;<acronym title="HyperText Markup Language">Html</acronym>&nbsp;
		[/IF]
		[IF [!R::Css!]=1]
			&nbsp;<acronym title="Cascading Style Sheets">CSS</acronym>&nbsp;
		[/IF]
		[IF [!R::Flash!]=1]
			&nbsp;Flash&nbsp;
		[/IF]
		[IF [!R::Javascript!]=1]
			&nbsp;Javascript&nbsp;
		[/IF]
		[IF [!R::XML!]=1]
			&nbsp;<acronym title="Extensible Markup Language">XML</acronym>&nbsp;
		[/IF]
		[IF [!R::SOAP!]=1]
			&nbsp;<acronym title="Simple Object Access Protocol">SOAP</acronym>&nbsp;
		[/IF]
		[IF [!R::Administration!]=1]
			&nbsp;Site administrable&nbsp;
		[/IF]
	</div></div>
	[IF [!R::Moyens!]!=]
		<div class="row"><div class="col-md-12">
			<h2>R&Eacute;ALISATION</h2>
		</div></div>
		<div class="row"><div class="col-md-12">
			[!R::Moyens!]
		</div></div>
	[/IF]
	<div class="row"><div class="col-md-12">
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
	</div></div>
</div>