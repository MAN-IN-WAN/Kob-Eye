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
		<div id="Data" style="border-top:1px solid #827152;">
			<div class="FicheClient">
				<span class="Deco">&nbsp;</span><h1>[!R::Chapo!]</h1>
				<div class="Description">[!R::Description!]</div>
			</div>
			<div class="FicheClient" style="background-color:#ffffff;padding:15px !important;padding:0;min-height:500px;">
				[STORPROC [!Chemin!]/[!R::Id!]/Lien|L]
					<div style="margin:0;padding:0;float:right;">
						<a href="[IF [!L::URL!]~http][!L::URL!][ELSE]http://[!L::URL!][/IF]" title="Voir le site internet [!R::Titre!]" class="WebSite" onclick="window.open(this.href); return false;">Voir le site</a>
					</div>
					[NORESULT][/NORESULT]				
				[/STORPROC]
				<span class="Deco1">&nbsp;</span><h2>Client</h2>
				<div>[!Cli::Nom!]</div>
				<hr style="color:#827152;background:#827152;height:1px;border:0;margin-bottom:10px;"/>
				<span class="Deco1">&nbsp;</span><h2>Technologies</h2>
				<div>[!R::Technologies!]</div>
				<hr style="color:#827152;background:#827152;height:1px;border:0;margin-bottom:10px;"/>
				<span class="Deco1">&nbsp;</span><h2>Réalisation</h2>
				<div>[!R::Moyens!]</div>
				<hr style="color:#827152;background:#827152;height:1px;border:0;margin-bottom:10px;"/>
				[STORPROC Portfolio/Reference/[!R::Id!]/Donnee/Type=image|Img]
					<img src="/[!Img::Url!]" alt="[!Img::Titre!]" style="border:0;border:none;margin:0;"/>
					[IF [!Pos!]=[!NbResult!]]
					[ELSE]
						<hr style="color:#827152;background:#827152;height:1px;border:0;margin:0;padding:0;"/>
					[/IF]
				[/STORPROC]
			</div>
		</div>
	</div>
</div>

