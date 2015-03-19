// Paramètres composant
[!DefaultFontSize:=30!]

// Residence

[STORPROC ParcImmobilier/Residence/AmbianceReferente=1|R|0|1|tmsEdit|DESC][/STORPROC]
[STORPROC ParcImmobilier/Ville/Residence/[!R::Id!]|V][/STORPROC]
[STORPROC ParcImmobilier/Departement/Ville/[!V::Id!]|D][/STORPROC]
[!DebutHref:=!]
[!FinHref:=!]
[IF [!R::Lien!]!=]
	[!DebutHref:=<a href="/[!Systeme::getMenu(ParcImmobilier)!]/Departement/[!D::Lien!]/Ville/[!V::Lien!]/Residence/[!R::Lien!]">!]
	[!FinHref:=</a>!]
[/IF]

[!Legende:=[!R::Titre!]!]

[COUNT ParcImmobilier/Residence/[!R::Id!]/Donnee/Type=Perspective|NbImg]

[IF [!NbImg!]>0]

	// HTML
	<div id="Anim"  style="height:[!ImgHeight!]px; width:[!CadreWidth!]px;">
		<div id="AnimCadre" class="AnimCadreAccueil"  style="height:[!ImgHeight!]px; width:[!CadreWidth!]px; overflow:hidden;">
			[STORPROC ParcImmobilier/Residence/[!R::Id!]/Donnee/Type=Perspective|I|||Titre|ASC]
				<script type="text/javascript">
					[IF [!Key!]=0][!Legende:=[!I::Titre!]!][/IF]
					window.addEvent('domready', function() { legendesAnim[[!Key!]] = "[!I::Titre!]" });
				</script>
				<img src="/[!I::URL!].mini.[!ImgWidth!]x[!ImgHeight!].jpg" alt="[!Utils::noHtml([!Legende!])!]" />
			[/STORPROC]
		</div>
		<div id="AnimMasque" style="background:url('/[!R::MasqueHTMLAccueil!]') no-repeat"></div>
		<div id="AnimLegende">[!DebutHref!][!Legende!][!FinHref!]</div>
		<div id="AnimPages"></div>
		<div id="AnimEtiquette"></div>
		[IF [!R::BBC!]=1]
			<div id="LogoBBC"></div>
		[/IF]
	</div>


[/IF]