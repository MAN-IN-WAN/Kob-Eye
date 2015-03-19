[COUNT [!Query!]/Article/Publier=1|NbArt]
[IF [!NbArt!]]
	[STORPROC [!Query!]|Cat]
		<div class="RedactionAccueil">
			<div class="TitreCategorie">
				<h1>[!Cat::Titre!]</h1>
			</div>
			[STORPROC [!Query!]/Article|Art|0|1]
				<h2 class="val">[!Art::Titre!]</h2>
				<div style="display:block;overflow:hidden;" id="articlereduit">
					<p>[SUBSTR 300][!Art::Contenu!][/SUBSTR]</p>
					<a class="lirearticle" href="javascript:;" onclick="MasqueBlock();" >Lire cet article</a>
				</div>
				<div style="display:none;overflow:hidden;" id="articlecomplet">
					<p>[!Art::Contenu!]</p>
				</div>
			[/STORPROC]
		</div>
	[/STORPROC]
[/IF]
<div class="BlocsAccueil">
	<div class="BlocHome">
		<div class="BlocTitre TitreMessagerie">
			<h2>Messagerie</h2>
		</div>
		<div class="BlocContenu">
			<p>Connectez-vous à votre messagerie Zimbra en cliquant sur le bouton ci-dessous.</p>
			<p style="text-align:center">
			//	<a href="http://mail.unibio.fr" class="mailunibio" onclick="window.open(this.href);return false;" ></a>
				<a href="http://mail.unibio.fr" class="mailunibio" id="mailunibio"  ></a>
			</p>
		</div>
	</div>
	<div class="BlocHome">
		<div class="BlocTitre TitreAccesInternet">
			<h2>Accès Internet</h2>
		</div>
		<div class="BlocContenu">
			<p>Accédez à Internet en cliquant sur le bouton ci-dessous.</p>
			<p style="text-align:center">
				<br /><a target="_blank" href="http://www.google.fr"  class="internetunibio"></a>
			</p>
		</div>
	</div>
	<div class="BlocHome">
		<div class="BlocTitre TitreReferentielExamins">
			<h2>Référentiel des examens</h2>
		</div>
		<div class="BlocContenu">
			
				<form action="/Espace-Pro/Referentiel" method="get" id="AnalyseSearch">
					<input type="text" id="A_MotCle" name="MotCle" value="" />
					<button type="submit" class="RechercherBtn">Rechercher</button>
				</form>
				<script type="text/javascript">
					window.addEvent('domready', function() {
						FieldDefaultText($('A_MotCle'), "Tapez un mot clé", $('AnalyseSearch'));
					});
				</script>
				<a href="/Espace-Pro" class="Referentiel">Accéder au référentiel complet</a>
			
		</div>
	</div>
	<div class="BlocHome">
		<div class="BlocTitre TitreMesApplications">
			<h2>Mes applications</h2>
		</div>
		<div class="BlocContenuAppli">
			[STORPROC Redaction/Categorie/14/Article/Publier=1|Art|0|2|Id|DESC]
				[STORPROC Redaction/Article/[!Art::Id!]/Lien|Lie|0|1][/STORPROC]
				<a href="[IF [!Lie::Type!]=Interne]/[/IF][!Lie::URL!]" [IF [!Lie::Type!]=Externe] target="_blank"[/IF] style="padding-bottom:0;"><h3>[!Art::Titre!]</h3></a>
				<p>[SUBSTR 100| [...]][!Art::Contenu!][/SUBSTR]</p>
				[STORPROC Redaction/Article/[!Art::Id!]/Image|Img|0|1]
				<div style="text-align:center;" >	<img src="/[!Img::URL!].limit.230x100.jpg" alt="[!Art::Titre!]" title="[!Art::Titre!]" /></div>
				[/STORPROC]
				<a class="UnLien" href="[IF [!Lie::Type!]=Interne]/[/IF][!Lie::URL!]" [IF [!Lie::Type!]=Externe] target="_blank"[/IF] style="padding-right: 10px;padding-bottom:0;">[!Lie::Titre!]</a>
				
			[/STORPROC]
		</div>
		<div class="BlocContenuAppli"><a class="ToutesLeApplis" href="/Mes-Applications" style="padding-top:0; padding-bottom:0;">Voir toutes les applications</a></div>
	</div>
</div>

<script type="text/javascript">
	function MasqueBlock() {
		$('articlecomplet').setStyle('display','block');
		$('articlereduit').setStyle('display','none');
	}
	$('mailunibio').addEvent('click', function(e) {
	    if(!Browser || Browser.name != 'firefox') {
    	    e.preventDefault();
    	    window.open(this.href, "Mail Unibio", "menubar=no, status=no, scrollbars=no, menubar=no, width=900, height=700");
    	}
	});
</script>
