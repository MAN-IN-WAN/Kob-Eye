[TITLE]Admin Kob-Eye | Voir Simulation[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
<div id="Container">
	<form action="" method="post" name="rech[!Test::TypeChild!]" class="FormRech">
		<div id="Arbo">
			[BLOC Panneau]
			[/BLOC]
		</div>
		<div id="Data" style="display:block;">
			[BLOC Panneau]
				[STORPROC [!Query!]|Dev|0|1]
					[STORPROC Catalogue/Simulateur/Devis/[!Dev::Id!]|Sim|0|1]
						<div class="Simulateur">
							<div class="SimulateurDescriptif">
								<h1>Récapitulatif de votre [!Sim::Titre!]</h1>
							</div>
							<div class="RecapEtap">
								<p >Nom :<strong> [!Dev::Nom!]</strong></p>
								<p>Email :<strong> [!Dev::Email!]</strong></p>
								<p>Adresse : <strong>[!Dev::Adresse!] - [!Dev::Ville!]</strong></p>
								<p>Rdv le : <strong>[DATE d/m/y h:m][!Dev::DateRdv!][/DATE]</strong></p>
							</div>

							[STORPROC Catalogue/Simulateur/[!Sim::Id!]/Etape/Publier=1|Etp|||Ordre|ASC]
								<div class="RecapEtap">
									[!ChxRep:=0!]
									// gestion des produits qui correspondent à plusieurs niveau de confort
									[!FiltreNiveau:=!]
									[STORPROC Catalogue/Etape/[!Etp::Id!]/Question/Publier=1|Qst|||Ordre|ASC]
										// lecture Question
										<br />[!Qst::Titre!] :
										[STORPROC Catalogue/Etape/[!Etp::Id!]/Question/[!Qst::Id!]/Choix|Chx]
											// lecture Choix de réponse
											[STORPROC Catalogue/Devis/[!Dev::Id!]/Reponse/Etape=[!Etp::Id!]&Question=[!Qst::Id!]&Reponse=[!Chx::Id!]|Rep]
												<span>[!Chx::LibelleReponse!]</span>
												
											[/STORPROC]
										[/STORPROC]
									[/STORPROC]
								</div>
							[/STORPROC]		
						[/STORPROC]
					</div>
				[/STORPROC]
				<div class="SimulateurDescriptif">Produits proposés</div>
				[STORPROC Catalogue/Devis/[!Dev::Id!]/Produit|Pr]
					<p >Produit [!Pos!] :<strong> [!Pr::Fabricant!] -[!Pr::Titre!] -[!Pr::Reference!] </strong></p>
				[/STORPROC]



			[/BLOC]
		</div>
	</form>
</div>
