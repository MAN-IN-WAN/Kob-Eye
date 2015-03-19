[STORPROC Boutique/Magasin|Mag|0|1][/STORPROC]
[!TailleColonne:=20%!]
[!TailleColonneDer:=35%!]

<div class="SiteFond">
	<div class="Contenu">
		<div id="MenuBas">
			<div class="MenuBasColonne" style="width:160px">
				<h4>Nos Produits</h4>
				<ul>
					[STORPROC Boutique/Categorie/Actif=1&&ALaUne=1|Cato|0|10|Ordre|ASC]
						[LIMIT 0|10]
							<li><a href="/La_Boutique/[!Cato::Url!]" title="[IF [!Cato::NomLong!]!=][!Cato::NomLong!][ELSE][!Cato::Nom!][/IF]">[!Cato::NomLong!]</a></li>
						[/LIMIT]
					[/STORPROC]
				</ul>
			</div>
			<div class="MenuBasColonne" style="width:160px">
				<h4>Nos Produits</h4>
				<ul>
					<li><a href="/Informations/Livraison" title="Livraison">Livraison</a></li>
					//<li><a href="/" title="Paiement sécurisé">Paiement sécurisé</a></li>
					//<li><a href="/" "Nos Garanties et engagements">Nos Garanties et engagements</a></li>
					<li><a href="/Informations/_Conditions-generales" title="Conditions Générales de Vente">CGV</a></li>
				</ul>
			</div>
			<div class="MenuBasColonne" style="width:160px">
				<h4>Divers</h4>
				<ul>
					<li><a href="/Contact" title="Contact">Contact</a></li>
					<li><a href="/Informations/_Mentions-legales" title="Mentions Légales">Mentions légales</a></li>
					//<li><a href="/" title="Plan d'accès">Plan d'accès</a></li>
					<li><a href="/Celliers" title="Nos Magasins">Nos magasins</a></li>
				</ul>
			</div>
			<div style="margin-left:510px; padding:5px">
				<h4>qui sommes nous</h4>
				[STORPROC Redaction/Categorie/_Qui-sommes-nous|Cato|0|1|Ordre|ASC]
					<p>[!Cato::Description!]</p>
				[/STORPROC]
			</div>
		</div>
		<div id="MentionsBottom">
			<div id="Paiements"><img src="/Skins/VoieRomaine/Img/paiement-pied.png" alt="Paiements acceptés" title="Paiements acceptés" /></div>
			<div id="Alcool">L'abus d'alcool est dangereux pour la santé, à consommer avec modération.</div>
		</div>
		<div id="AdresseSociete">
			[!Mag::NomLong!]- [!Mag::Adresse!]- [!Mag::CodePostal!] [!Mag::Ville!]
			[IF [!Mag::Tel!]!=]&nbsp;- Tél. [!Mag::Tel!][/IF]
			[IF [!Mag::Fax!]!=]&nbsp;- Fax : [!Mag::Fax!][/IF]
		</div>
		<a id="CopyrightAbtel" href="http://agence-web.abtel.fr" onclick="window.open(this.href);return false">Abtel agence web &copy; 2011</a>
	</div>
</div>