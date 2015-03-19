// Paramètres composant
[!DefaultFontSize:=30!]

// Residence

[!Legende:=[!R::Titre!]!]
//[!LaRequete!]
[COUNT [!LaRequete!]/Image|NbImg]
[IF [!NbImg!]>0]
	[STORPROC [!LaRequete!]/Lien|L|0|1|tmsEdit|DESC][/STORPROC]
	[IF [!L::URL!]!=]
		[IF [!L::URL!]~http][!Lelien:=!][ELSE][!Lelien:=/!][/IF]
		[!Lelien+=[!L::URL!]!]
		[!DebutHref:=<a href="[!Lelien!]">!]
	[ELSE]
		[!DebutHref:=<a href="/[!Systeme::getMenu(ParcImmobilier)!]">!]
	[/IF]
	[!FinHref:=</a>!]
	[!LaRequete+=/Image!]
[ELSE]

	[STORPROC ParcImmobilier/Residence/AmbianceReferente=1|R|0|1|tmsEdit|DESC][/STORPROC]
	[STORPROC ParcImmobilier/Ville/Residence/[!R::Id!]|V][/STORPROC]
	[STORPROC ParcImmobilier/Departement/Ville/[!V::Id!]|D][/STORPROC]
	[!DebutHref:=!]
	[!FinHref:=!]
	[IF [!R::Lien!]!=]
		[!DebutHref:=<a href="/[!Systeme::getMenu(ParcImmobilier)!]/Departement/[!D::Lien!]/Ville/[!V::Lien!]/Residence/[!R::Lien!]">!]
		[!FinHref:=</a>!]
	[/IF]

	[COUNT ParcImmobilier/Residence/[!R::Id!]/Donnee/Type=Perspective|NbImg]
	[!LaRequete:=ParcImmobilier/Residence/[!R::Id!]/Donnee/Type=Perspective!]
[/IF]

[IF [!NbImg!]>0]

	// HTML
	<div id="Anim"  style="height:[!ImgHeight!]px; width:[!CadreWidth!]px;">
			<div id="AnimCadre" class="AnimCadreAccueil"  style="height:[!ImgHeight!]px; width:[!CadreWidth!]px; overflow:hidden;">
				[STORPROC [!LaRequete!]|I|||Titre|ASC]
					<script type="text/javascript">
						[IF [!Key!]=0][!Legende:=[!I::Titre!]!][/IF]
						window.addEvent('domready', function() { legendesAnim[[!Key!]] = "[!I::Titre!]" });
					</script>
					<img src="/[!I::URL!].mini.[!ImgWidth!]x[!ImgHeight!].jpg" alt="[!Utils::noHtml([!Legende!])!]" />
				[/STORPROC]
			</div>
			<div id="AnimMasque" [IF [!LaRequete!]~Parc]style="background:url('/[!R::MasqueHTMLAccueil!]') no-repeat"[/IF]></div>
			<div id="AnimLegende">[!DebutHref!][!Legende!][!FinHref!]</div>
			<div id="AnimPages"></div>
			<div id="AnimEtiquette"></div>
			[IF [!LaRequete!]~Parc][IF [!R::BBC!]=1]
				<div id="LogoBBC"></div>
			[/IF][/IF]
	</div>


[/IF]