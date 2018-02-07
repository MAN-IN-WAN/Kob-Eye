[STORPROC [!Chemin!]|R|0|1][/STORPROC]
[STORPROC Portfolio/Clients/Reference/[!R::Id!]|Cli][/STORPROC]//[!DEBUG::Cli!]
<div style="overflow:hidden;">
	[MODULE Portfolio/Structure/Gauche]
	<div id="Milieu" style="margin-left:250px;">
		<div id="Data" style="border-top:1px solid #1e1e1e;">
			<div id="FicheClient">
				<h1>[!R::Chapo!]</h1>
				<div class="Description">[!R::Description!]</div>
				<div style="overflow:hidden;margin:0;padding:0;">
					<a href="[IF [!R::SiteWeb!]~http][!R::SiteWeb!][ELSE]http://[!R::SiteWeb!][/IF]" title="Voir le site internet [!R::Titre!]" class="WebSite" onclick="window.open(this.href); return false;">Voir le site</a>
					<div style="float:left;margin:0;padding:0;">
						<span class="Deco1">&nbsp;</span><h2>Client</h2>
						<div>[!Cli::Nom!]</div>
					</div>
				</div>
				<hr style="color:#827152;background:#1e1e1e;height:1px;border:0;margin-bottom:10px;"/>
				<h2>Technologies</h2>
				<div>[!R::Technologies!]</div>
				<hr style="color:#827152;background:#1e1e1e;height:1px;border:0;margin-bottom:10px;"/>
				<h2>Réalisation</h2>
				<div>[!R::Moyens!]</div>
				<hr style="color:#1e1e1e;background:#1e1e1e;height:1px;border:0;margin-bottom:10px;"/>
				[STORPROC Portfolio/Reference/[!R::Id!]/Donnee/Type=image|Img]
					<img src="/[!Img::Url!]" alt="[!Img::Titre!]" style="border:0;border:none;margin:0;"/>
					[IF [!Pos!]=[!NbResult!]]
					[ELSE]
						<hr style="color:#1e1e1e;background:#1e1e1e;height:1px;border:0;margin:0;padding:0;"/>
					[/IF]
				[/STORPROC]
			</div>
		</div>
	</div>
</div>
