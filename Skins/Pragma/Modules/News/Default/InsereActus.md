[STORPROC News/Categorie/Nouvelle/[!Ne::Id!]|Cat|0|1][/STORPROC]

<div class="BlocNouvelle "  >
	<div class="LigneTitre">
		<div class="TitreNouvelle"><a href="/Actus/[!Cat::Url!]/Nouvelle/[!Ne::Url!]" title="[!Ne::Titre!]" ><h4>[!Ne::Titre!]</h4></a></div>
		[IF [!NbNe!]>1]<div class="Buttons nodisplay " style="display:none;">
			[IF [!Ne::Contenu!]!=||[!Ne::ContenuIframe!]!=]<button name="Ouvre_[!Ne::Id!]" class="OuvreNouvelle"></button><button  name="Ferme_[!Ne::Id!]" class="FermeNouvelle"></button>[/IF]
		</div>[/IF]
	</div>
	<div class="ContenuNouvelle [IF [!NeSel::Id!]=[!Ne::Id!]]NouvelleEncours[/IF]" >
		<div class="NouvelleEtImg">
			[IF [!Ne::Image!]!=]
				<div style="float:left;display:block;position:relative;width:180px;">
					<a href="/[!Ne::Image!]" class="mb" rel="Img[[!Ne::Id!]]" title="[SUBSTR 30][!Ne::Titre!][/SUBSTR]" style="background:none;"><img src="/[!Ne::Image!].limit.170x170.jpg" alt="[!Ne::Titre!]" /></a>
				</div>
			[/IF]
			<div class="contenuNe" style="float:left;display:block;position:relative; [IF [!Ne::Image!]!=]width: 600px;[ELSE]width:auto;[/IF] text-align:justify;">
				[IF [!Ne::Contenu!]!=][!Ne::Contenu!][/IF]
				[IF [!Ne::ContenuIframe!]!=][!Ne::ContenuIframe!][/IF]
				[COUNT News/Nouvelle/[!Ne::Id!]/Donnee/Type=Image|NbImg]
				[IF [!NbImg!]]
					<div class="NewsImage" >
						[STORPROC News/Nouvelle/[!Ne::Id!]/Donnee/Type=Image|Do]
							<a href="/[!Do::Fichier!].limit.800x600.jpg" class="mb" rel="Img[[!Ne::Id!]]" style="background:none;" title="[SUBSTR 30][!Utils::NOHTML([!Do::Titre!])!][/SUBSTR]">
								<img src="/[!Do::Fichier!].mini.70x70.jpg" width="70" height="70" alt="[!Do::Titre!]" title="[!Do::Titre!]"/>
							</a>
						[/STORPROC]
					</div>
				[/IF]
			</div>
			<div class="NewsLienResidence" style="float:right;display:block;position:relative;">
				[STORPROC ParcImmobilier/Residence/Nouvelle/[!Ne::Id!]|Rs|0|1]
					[STORPROC ParcImmobilier/Ville/Residence/[!Rs::Id!]|V][/STORPROC]
					[STORPROC ParcImmobilier/Departement/Ville/[!V::Id!]|D][/STORPROC]
						<a class="VoirResidence" href="/[!Systeme::getMenu(ParcImmobilier)!]/Departement/[!D::Lien!]/Ville/[!V::Lien!]/Residence/[!Rs::Lien!]" >Voir la résidence</a>
						<a class="Contact" href="/Contact?C_Sujet=[!Rs::Titre!]" >Contact</a>
						<a class="EnvoyerAmi" href="/Envoyer-Ami?C_Adresse=[!Domaine!]%2FParcImmobilier%2FDepartement%2F[!D::Lien!]%2FVille%2F[!V::Lien!]%2FResidence%2F[!Rs::Lien!]" >Envoyer à un ami</a>
				[/STORPROC]

			</div>
		</div>	
	</div>
	[COUNT News/Nouvelle/[!Ne::Id!]/Donnee/Type=Fichier|NbFic]
	[IF [!NbFic!]]
		<div class="NewsResidenceDonnee">
			[STORPROC News/Nouvelle/[!Ne::Id!]/Donnee/Type=Fichier|Do]
				<a class="[IF [!Do::Css!]!=][!Do::Css!][ELSE]accueilliennews[/IF]" href="/[!Do::Fichier!]" rel="link" >[!Do::Titre!]</a>
			[/STORPROC]
		</div>
	[/IF]
	[COUNT News/Nouvelle/[!Ne::Id!]/Donnee/Type=Lien|NbDo]
	[IF [!NbDo!]]
		<div class="NewsResidenceDonnee">
			[STORPROC News/Nouvelle/[!Ne::Id!]/Donnee/Type=Lien|Do]
				<a class="[IF [!Do::Css!]!=][!Do::Css!][ELSE]accueilliennews[/IF]" href="/[!Do::Url!]" >[!Do::Titre!]</a>
			[/STORPROC]
		</div>
	[/IF]
	[COUNT News/Nouvelle/[!Ne::Id!]/Donnee/Type=LienExterne|NbDoE]
	[IF [!NbDoE!]]
		<div class="NewsResidenceDonnee">
			[STORPROC News/Nouvelle/[!Ne::Id!]/Donnee/Type=LienExterne|Do]
				<a class="[IF [!Do::Css!]!=][!Do::Css!][ELSE]accueilliennews[/IF]" href="[!Do::Url!]" target="_blank" >[!Do::Titre!]</a>
			[/STORPROC]
		</div>
	[/IF]

</div>