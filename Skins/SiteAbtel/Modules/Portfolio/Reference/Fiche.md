[STORPROC [!Chemin!]|R|0|1][/STORPROC]
[HEADER]
	<link rel="canonical" href="[!Domaine!]/[!Lien!]" />
[/HEADER]
[STORPROC Portfolio/Clients/Reference/[!R::Id!]|Cli][/STORPROC]//[!DEBUG::Cli!]
[TITLE][!R::Titre!] - [!R::Chapo!][/TITLE]
[DESCRIPTION]Référence client [!Cli::Nom!] : [!R::Chapo!] - [!R::Description!][/DESCRIPTION]
<div style="overflow:hidden;">
	[MODULE Portfolio/Structure/Gauche?Chemin=[!Chemin!]]
	<div id="Milieu" style="margin-left:250px;">
		<div id="Data">
			<div class="FicheClient">
				<h1 style="border-bottom:1px solid #f29400;">[!R::Chapo!]</h1>
				<div class="Description">[!R::Description!]</div>
			</div>
			<div class="FicheClient" style="background-color:#ffffff;padding:15px !important;padding:0;min-height:500px;">
				[STORPROC [!Chemin!]/Lien|L]
					<div style="margin:0;padding:0;float:right;">
						<a href="[IF [!L::URL!]~http][!L::URL!][ELSE]http://[!L::URL!][/IF]" title="Voir le site internet [!R::Titre!]" class="WebSite" onclick="window.open(this.href); return false;" [IF [!L::NoFollow!]] rel="nofollow"[/IF]>Voir le site</a>
					</div>
					[NORESULT][/NORESULT]				
				[/STORPROC]
				<h2>Client</h2>
				<div>[!Cli::Nom!] - [!R::Chapo!]</div>
				<hr style="color:#1e1e1e;background:#1e1e1e;height:1px;border:0;margin-bottom:10px;"/>
				<h2>Technologies</h2>
				<div>
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
				</div>
				<hr style="color:#1e1e1e;background:#1e1e1e;height:1px;border:0;margin-bottom:10px;"/>
				[IF [!R::Moyens!]!=]<h2>R&Eacute;ALISATION</h2>
				<div>[!R::Moyens!]</div>
				<hr style="color:#1e1e1e;background:#1e1e1e;height:1px;border:0;margin-bottom:10px;"/>
				[/IF]
				[STORPROC Portfolio/Reference/[!R::Id!]/Donnee/Type=image|Img]
					<img src="/[!Img::Url!]" alt="[!Img::Titre!]" style="border:0;border:none;margin:0;padding:10px 0 10px 0;"/>
					[IF [!Pos!]=[!NbResult!]]
					[ELSE]
						<br />
					[/IF]
				[/STORPROC]
			</div>
		</div>
	</div>
</div>

