<div class="GSAGences">
	[IF [!searchAgence!]!=]
		[COUNT Catalogue/Agence/Ville/[!searchAgence!]|NbAg]
		[STORPROC Catalogue/Agence/Ville/[!searchAgence!]|Ag]
			<div class="UneAgence">
				<div class="InfosAgence">
					[IF [!Ag::Photo!]!=]
						<div class="PhotoAgence">
							<img src="/[!Ag::Photo!]" title="[!Ag::Nom!]" alt="[!Ag::Nom!]" />
						</div>
					[/IF]
					<div class="CorpsAgence">
						<div class="NomAgence">
							[IF [!NbAg!]>1]<h2>Agence : [!Ag::Nom!]</h2>[ELSE]<h1>Agence : [!Ag::Nom!]</h1>[/IF]
						</div>
						<div class="NomAgence">
							[IF [!Ag::Email!]!=]<a href="/Contact?email=[!Ag::Email!]">Pour nous contacter cliquer ici</a>[/IF]
						</div>
						<div class="AdresseAgence">
							<div class="AdresseAgenceContenu">
								[!Ag::Adresse!]
							</div>
							<div class="AdresseAgenceContenu">
								[!Ag::CodePostal!] [!Ag::Ville!]
							</div>
							<div class="TelAgence">
								Tél : [!Ag::Telephone!]
							</div>
						</div>
					</div>
					<div class="AdresseAgenceContenuGG">
						[!Ag::GG!]
					</div>
				</div>
			</div>
			[NORESULT]
				[STORPROC Geographie/Ville/[!searchAgence!]|Vi]
					[STORPROC Geographie/Departement/Ville/[!Vi::Id!]|Dp][/STORPROC]
					[COUNT Catalogue/Agence/Departement/[!Dp::Id!]|NbAg]
					[STORPROC Catalogue/Agence/Departement/[!Dp::Id!]|AgD]
						<div class="UneAgence">
							<div class="InfosAgence">
								[IF [!AgD::Photo!]!=]
									<div class="PhotoAgence">
										<img src="/[!AgD::Photo!]" title="[!AgD::Nom!]" alt="[!AgD::Nom!]" />
									</div>
								[/IF]
								<div class="CorpsAgence">
									<div class="NomAgence">
										[IF [!NbAg!]>1]<h2>Agence : [!AgD::Nom!]</h2>[ELSE]<h1>Agence : [!AgD::Nom!]</h1>[/IF]
									</div>
									<div class="NomAgence">
										[IF [!AgD::Email!]!=]<a href="/Contact?email=[!AgD::Email!]">Pour nous contacter cliquer ici</a>[/IF]
									</div>
									<div class="AdresseAgence">
										<div class="AdresseAgenceContenu">
											[!AgD::Adresse!]
										</div>
										<div class="AdresseAgenceContenu">
											[!AgD::CodePostal!] [!AgD::Ville!]
										</div>
										<div class="TelAgence">
											Tél : [!AgD::Telephone!]
										</div>
									</div>
								</div>
								<div class="AdresseAgenceContenuGG">
									[!AgD::GG!]
								</div>
							</div>		
						</div>				
					[/STORPROC]
				[/STORPROC]
			[/NORESULT]
		[/STORPROC]
	[ELSE]
		// on a demande une agence
		[STORPROC [!Query!]|Ag]
			<div class="UneAgence">
				<div class="InfosAgence">
					[IF [!Ag::Photo!]!=]
						<div class="PhotoAgence">
							<img src="/[!Ag::Photo!]" title="[!Ag::Nom!]" alt="[!Ag::Nom!]" />
						</div>
					[/IF]
					<div class="CorpsAgence">
						<div class="NomAgence">
							[IF [!NbAg!]>1]<h2>Agence : [!Ag::Nom!]</h2>[ELSE]<h1>Agence : [!Ag::Nom!]</h1>[/IF]
						</div>
						<div class="NomAgence">
							[IF [!Ag::Email!]!=]<a href="/Contact?email=[!Ag::Email!]">Pour nous contacter cliquer ici</a>[/IF]
						</div>
						<div class="AdresseAgence">
							<div class="AdresseAgenceContenu">
								[!Ag::Adresse!]
							</div>
							<div class="AdresseAgenceContenu">
								[!Ag::CodePostal!] [!Ag::Ville!]
							</div>
							<div class="TelAgence">
								Tél : [!Ag::Telephone!]
							</div>
						</div>
					</div>
					<div class="AdresseAgenceContenuGG">
						[!Ag::GG!]
					</div>
				</div>
			</div>
		[/STORPROC]

	
	[/IF]
</div>
[!searchAgence:=!]