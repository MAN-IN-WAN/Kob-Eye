
<div class="col-md-4">
	<div class="MoyensPaiements">Moyens de paiement</div>
	<div class="Coordonnees">
		<div class="Titre">Besoin d'aide ? Contactez-nous</div>
		<div class="DeuxColonnes">
			<div class="Gauche">
				<div class="LeContact">Par Tél du lundi au<br />samedi de 9h à 19h</div>
				<div class="Tel">[STORPROC Boutique/Magasin/1|Mag][!Mag::Tel!][/STORPROC]</div>
			</div>
			<div class="Droite">
				<div class="LeContact">Par  e-mail<br />(7j/7 - 24h/24)</div>
				<div class="BtnContact"><a href="/Contact" class="btn btn-kirigami btn-small">Contact</a></div>
			</div>
		</div>
	</div>
</div>
<div class="col-md-4">
	<div class="InscriptionNewsletter">
		[MODULE Newsletter/Inscription]
	</div>
	<div class="Abonnes">
		[STORPROC Redaction/Categorie/6/Article|Art|0|1|Ordre|ASC]
			[STORPROC Redaction/Article/[!Art::Id!]/Lien|Lie|0|1][/STORPROC]
			<div class="Titre">[!Art::Titre!]</div>
			<div class="Article">[!Art::Contenu!]</div>
			<div class="Lien"><a href="/[!Lie::URL!]" [IF [!Lie::Type!]=Externe]target="_blank"[/IF]>[!Lie::Titre!]</a></div>
		[/STORPROC]
	</div>
</div>
<div class="col-md-4">
	<div class="MenuBas" >
		<div class="Titre">Accès direct</div>
		[STORPROC [!Systeme::Menus!]/MenuBas=1&Affiche=1|M]
			<ul >
				[LIMIT 0|100]
					<li >
						<a href="/[!M::Url!]">[!M::Titre!]</a>
					</li>	
				[/LIMIT]
			</ul>
		[/STORPROC]
	</div>
</div>

