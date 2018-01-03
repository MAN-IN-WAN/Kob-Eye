[IF [!Chemin!]]
[ELSE]
	[!Chemin:=[!Query!]!]
[/IF]
[STORPROC [!Chemin!]/Article/Publier=1|Art]
	[IF [!Art::Contenu!]!=]
		<div class="row" ><div class="col-md-12 col-sm-12 col-xs-12">
			<div class="articleMep"><div class="contenuMEP">
				[IF [!Art::AfficheTitre!]]<h2>[!Art::Titre!]</h2>[/IF]
				[IF [!Art::Chapo!]!=]<h3>[!Art::Chapo!]</h3>[/IF]
			</div></div>
		</div></div>
	[ELSE]
		<div class="row" ><div class="col-md-12 col-sm-12 col-xs-12">
			<div class="articleMep">
				[STORPROC MiseEnPage/Article/[!Art::Id!]/Contenu/Publier=1|Cont]
					<div class="contenuMEP">
						[STORPROC MiseEnPage/Contenu/[!Cont::Id!]/Colonne|Col|||Ordre|ASC]
							<div class="colonneMEP" style="width:[!Col::Ratio!]%;">			
								[STORPROC MiseEnPage/Colonne/[!Col::Id!]/Image|ContColImg]
									<div class="imgMEPContainer">
										<img src="/[!ContColImg::URL!]" alt="[!ContColImg::Alt!]" title="[!ContColImg::Title!]" class="img-responsive">
									</div>
									[NORESULT]
										[STORPROC MiseEnPage/Colonne/[!Col::Id!]/Texte|ContColText]
											<div class="txtMEPContainer">
												[IF [!Art::AfficheTitre!]]<h2>[!Art::Titre!]</h2>[/IF]
												[IF [!Art::Chapo!]!=]<h3>[!Art::Chapo!]</h3>[/IF]
												<p>[!ContColText::Contenu!]</p>
											</div>
										[/STORPROC]
									[/NORESULT]
								[/STORPROC]
							</div>
						[/STORPROC]
					</div>
				[/STORPROC]
			</div>
		</div></div>
	[/IF]

			
[/STORPROC]


