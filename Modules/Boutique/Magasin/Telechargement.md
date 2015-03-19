[IF [!Systeme::User::Public!]]
				
[ELSE]
	[!Error:=0!][!Erreur:=!][!Liste:=!][!Nono:=0!]
	[TITLE]Admin Kob-Eye | Mise à disposition téléchargements[/TITLE]
	[MODULE Systeme/Interfaces/FilAriane]
	[STORPROC [!Query!]|Mag][/STORPROC]
	
	<div id="Container">
		<div id="Arbo">
			[BLOC Panneau][/BLOC]
		</div>
		<div id="Data">
			[BLOC Panneau]
				[BLOC Panneau|background:white;position:relative;overflow:auto;height:500px;padding:5px;margin-bottom:20px;]
					<div class="BigTitle" style="font-weight:bold;;-moz-border-radius:5px 5px 0px 0px;clear:both;">Propriétés</div>		
						<table  border="1">
							<form action="" method="post" >
								<tr  >
									<td colspan="5" style="text-align:center;">
										Mise à disposition téléchargement<br/>
										choisissez l'abonnement <br/>
										Les clients abonnés à ce service auront accès au dosssier téléchargement (si leur abonnement n'a pas expiré)
									</td>
								</tr>
								<tr  >
									<td colspan="2" ><label>Nom du répertoire</label></td>
									<td colspan="3" >
										<select name="NomRep" style="width:300px;">
											<option value="" selected="selected">Choisir un répertoire</option>
											[STORPROC Explorateur/_Dossier/Home/_Dossier/Telechargement/_Dossier|Dos]
												[STORPROC Explorateur/_Dossier/Home/_Dossier/Telechargement/[!Dos::Id!]/_Dossier|DosF]
													<option value="_Dossier/Home/_Dossier/Telechargement/_Dossier[!Dos::Id!]/_Dossier/[!DosF::Id!]">Téléchargment : [!Dos::Id!] - [!DosF::Id!]</option>
												[/STORPROC]
											[/STORPROC]
										</select>
									</td>
								</tr>
								<tr  >
									<td colspan="2" ><label>Choisir l'abonnement</label></td>
									<td colspan="3" >
										<select name="Abonnez" style="width:300px;">
											<option value="" selected="selected">Choisir un abonnement</option>
											[STORPROC Boutique/Produit/NatureProduit=2&Actif=1|Se]
												<option value="[!Se::Id!]">Abonnement : [!Se::Nom!]</option>
											[/STORPROC]
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="5" style="text-align:center;">
										<button type="submit">Créer les enregistrements téléchargements</button><br/>
										Si vous cliquez sur ok, cet outil créer un enregistrement téléchargement par client concerné, en renseignant le nom du répertoire auquel il a accès.
									</td>
								</tr>
								[IF [!okabo!]=1]
									<tr  >
										<td colspan="5" style="text-align:center;">
											// mis à dispo demandé
											[IF [!NomRep!]=]
												[!Erreur:=Le répertoire est obligatoire<br />!]
												[!Error:=1!]		
											[/IF]
											[IF [!Abonnez!]=]
												[!Erreur+=Choisir l abonnement concerné!]
												[!Error:=1!]		
											[/IF]
											[IF [!Error!]=0]
												// Mise à dispo pour tous les clients
												Création telechargement pour clients : <br />
												[STORPROC Boutique/Service/Produit=[!Abonnez!]&DateDebut<=[!TMS::Now!]&DateFin>=[!TMS::Now!]|Serv|0|400]
													[STORPROC Boutique/Client/Service/[!Serv::Id!]|ServiCli|0|1]
														[!ServiCli::Nom!] - [!NomRep!] / [!ServiCli::Id!] - [!Serv::Nom!]<br />

//[OBJ Boutique|Telechargement|Tlg]
//[METHOD Tlg|Set][PARAM]Nom[/PARAM][PARAM][!ServiCli::Nom!][/PARAM][/METHOD]
//[METHOD Tlg|Set][PARAM]Url[/PARAM][PARAM][!NomRep!][/PARAM][/METHOD]
//[METHOD Tlg|AddParent][PARAM]Boutique/Client/[!ServiCli::Id!] [/PARAM][/METHOD]
//[METHOD Tlg|Save][PARAM]1[/PARAM][/METHOD]
													[/STORPROC]
												[/STORPROC]
											[/IF]
											[IF [!Error!]=1] <h3>[!Erreur!]</h3>[/IF]	
										</td>
									</tr>
								[/IF]
								<input type="hidden" value="1" name="okabo" >
							</form>
						</table>
					</div>
				[/BLOC]
			[/BLOC]
		</div>
	</div>
[/IF]