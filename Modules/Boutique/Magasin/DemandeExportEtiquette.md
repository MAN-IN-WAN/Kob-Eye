[IF [!C_OkEtiq!]]
	[OBJ Boutique|Magasin|Mag]
	[IF [!typeExport!]=P]
		[!Lefichier:=[!C_FichierP!].csv!]
	[ELSE]
		[!Lefichier:=[!C_FichierE!].csv!]
	[/IF]
	[!Mag::sendHeader([!Lefichier!])!]
	[IF [!typeExport!]=P]
		[!Ligne:=CodeClient;Civilité;Prénom;Nom;Adresse;;;;;Code Postal;Ville!]
		[!Mag::addLigne([!Ligne!])!] 
	[ELSE]
		[!Ligne:=RefCommande;Pour;Adresse;CodePostal;Ville;Pays;PointRelais;!]
		[!Mag::addLigne([!Ligne!])!] 
	[/IF]
	[STORPROC [!AEXPORTER!]|CoE]
		[STORPROC Boutique/Commande/[!CoE!]|Co|0|1]
			[IF [!typeExport!]=E]
				[!BL:=[!Co::getBonLivraison!]!][!Cpt:=0!]
				[!Ligne:=[!Co::RefCommande!];!]
				[STORPROC Boutique/Adresse/Commande/[!Co::Id!]|Adr|||Id|DESC]
					[IF [!Adr::Type!]=Livraison][!AdrLv:=[!Adr!]!][/IF]
				[/STORPROC]
				[IF [!BL::AdresseLivraisonAlternative!]]
					[!Ligne+=Pour !]
				[/IF]
				[!Ligne+=[!AdrLv::Civilite!] [!AdrLv::Prenom!] [!AdrLv::Nom!];!]
				[IF [!BL::AdresseLivraisonAlternative!]]
					[!Ligne+=;;;;[!BL::ChoixLivraison!]!]
				[ELSE]
					[!Ligne+=[!AdrLv::Adresse!];!]
					[!Ligne+=[!AdrLv::CodePostal!];!]
					[!Ligne+=[!AdrLv::Ville!];!]
					[!Ligne+=[!AdrLv::Pays!];0;!]
					
				[/IF]
				[!Mag::addLigne([!Ligne!])!] 
			[ELSE]
				[!Ligne:=CodeClient;!]
				[STORPROC Boutique/Adresse/Commande/[!Co::Id!]|Adr|0|1|Id|DESC]
					[IF [!Adr::Type!]=Livraison][!AdrLv:=[!Adr!]!][/IF]
				[/STORPROC]
				[!Ligne+=[!AdrLv::Civilite!];!]
 				[!Ligne+=[!AdrLv::Prenom!];!]
				[!Ligne+=[!AdrLv::Nom!];!]
				[!Ligne+=[!AdrLv::Adresse!];;;;;!]
				[!Ligne+=[!AdrLv::CodePostal!];!]
				[!Ligne+=[!AdrLv::Ville!];!]
				[!Mag::addLigne([!Ligne!])!] 
			[/IF]
		[/STORPROC]
	[/STORPROC]
	Fin Export
[ELSE]
	[TITLE]Admin Kob-Eye | Expedition [/TITLE]
	[MODULE Systeme/Interfaces/FilAriane]
	
	<div id="Container">
		<div id="Arbo">
			[BLOC Panneau]
			[/BLOC]
		</div>
		<div id="Data" style="overflow: auto;">
			[BLOC Panneau]
				<div style="margin:10px;font-size:15px;overflow: auto;">
					<form  action="/[!Lien!].csv" method="post" name="frm">
						<input type="submit" name="C_Valider" value="Exporter" />
						<input type="hidden" name="C_OkEtiq" value="1" />
						PrepaSecure <input type="radio" name="typeExport" value="P"  [IF [!typeExport!]=P] checked="checked"[/IF] /> <input type="text" name="C_FichierP" value="PrepaSecure_[DATE d-m-Y_h-i][!TMS::Now!][/DATE].csv" style="width:190px;" />
						Expeditor Inet <input type="radio" name="typeExport" value="E"  [IF [!typeExport!]=E||[!typeExport!]=] checked="checked"[/IF] /><input type="text" name="C_FichierE" value="Expeditor_[DATE d-m-Y_h-i][!TMS::Now!][/DATE].csv" style="width:190px;" />
						<table border="1" style="width:100%;">
							<tr><td colspan="4" style="text-align:center;font-size:20px;color:#ff0000;">Export des étiquettes pour expéditions</td></tr>
							<tr><td>Reférence</td><td>Client</td><td>Adresse</td><td>Type livraison</td><td>Exporter</td></tr>
							[STORPROC Boutique/Commande/Valide=1&Paye=1&Expedie=0|Co]
								[!BL:=[!Co::getBonLivraison!]!][!Cpt:=0!]
								[IF [!BL!]]
									[IF [!BL::Etiquette!]=]
										[STORPROC Boutique/Adresse/Commande/[!Co::Id!]|Adr|||Id|DESC]
											[IF [!Adr::Type!]=Livraison][!AdrLv:=[!Adr!]!] [!Cpt+=1!][/IF]
											
										[/STORPROC]
										<tr>
											<td>[!Co::RefCommande!]</td>
											<td>
												[IF [!Cpt!]>1]Attention deux adresses de livraison  à vérifier</br>[/IF]
												[IF [!BL::AdresseLivraisonAlternative!]]
													<br />Pour 
												[/IF]
												[!AdrLv::Civilite!] [!AdrLv::Prenom!] [!AdrLv::Nom!]<br /><br />
											</td>
											<td>
												[IF [!BL::AdresseLivraisonAlternative!]]
													<br />[!BL::ChoixLivraison!]<br />
												[ELSE]
													[!AdrLv::Adresse!] <br />
													[!AdrLv::CodePostal!] [!AdrLv::Ville!] [!AdrLv::Pays!]<br />
												[/IF]
											</td>
											<td>[!BL::TypeLivraison!]</td>
											<td>
												<input type="checkbox" name="AEXPORTER[]" value="[!Co::Id!]"  [IF [!AEXPORTER!]=[!Co::Id!]] checked="checked"[/IF] />
											</td>
										</tr>
									[/IF]
								[/IF]
							[/STORPROC]				
	
						</table>
					</form>
				</div>
			[/BLOC]
	</div>
[/IF]


