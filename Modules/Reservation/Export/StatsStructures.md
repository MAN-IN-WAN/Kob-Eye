[IF [!SERVER::REMOTE_ADDR!]=185.71.149.9||[!Ab!]=23686]
	[IF [!exportstats!]]
		[!DateDebut:=[!Utils::getTms(1/1/[!start!]) 00:00!]!]
		[!DateFin:=[!Utils::getTms(31/12/[!start!] 23:59)!]!]
		[!Filtre:=!][!Resa:=!]
		[STORPROC [!Structures!]|S]
			[IF [!Filtre!]!=][!Filtre+=+!][/IF]
			[!Filtre+=Id=[!S!]!]
			[STORPROC Reservation/Client/Id=[!S!]|Cl]
				[!Resa+=[!Cl::Nom!]!]
			[/STORPROC]
		[/STORPROC]
		[!TotalResa:=0!]
		[!TotalPers:=0!]
		[LIB HTML2PDF|html2pdf]
		[METHOD html2pdf|writeHTML]
			[PARAM]
				<page pageset="old" backtop="14mm" backbottom="1mm" backleft="10mm" backright="10mm" style="font-size: 12pt">
					<table class="page_header" cellspacing="0" cellspadding="0" border="1">
						<tr style="width:190mm;font-size:10px;background-color:#ccc;">
							<td colspan="4" style="font-size:14px;font-weight:bold;padding:20xp;text-align:center;">
								Liste des réservations du [DATE d/m/Y][!DateDebut!][/DATE] au [DATE d/m/Y][!DateFin!][/DATE]
							</td>
						</tr>
						[!TotResa:=0!]
						[!TGTotResa:=0!]
						[!TotPers:=0!]
						[!TGTotPers:=0!]
						// demande pour structures bien precises
						[!TotResa:=0!][!TotPers:=0!]
	
						<tr style="width:190mm;font-size:10px;"><th style="text-align:center;font-weight:bold;">Structure</th><th style="text-align:center;font-weight:bold;">Ville</th><th style="text-align:center;font-weight:bold;">Nombre de réservation</th><th style="text-align:center;font-weight:bold;">Nombre de personnes</th></tr>
						[STORPROC Reservation/Genre|G]
							[!Genre[!G::Id!]:=0!]
						[/STORPROC]
	
						[STORPROC Reservation/Client/[!Filtre!]|Cl|||Nom|ASC]
							[!NbResa:=0!]						
							[!NbResaArt:=0!]
							[!NbPersT:=0!]
							[!NbPersTArt:=0!]
							[STORPROC Reservation/Client/[!Cl::Id!]/Reservations/tmsCreate>=[!DateDebut!]&&tmsCreate<=[!DateFin!]|Res]
								[!NbResa+=1!]
								[!NbPers:=0!]
								[COUNT Reservation/Reservations/[!Res::Id!]/Personne|NbPers]
								[!NbPersT+=[!NbPers!]!]
								[STORPROC Reservation/Evenement/Reservations/[!Res::Id!]|Ev]
									[STORPROC Reservation/Spectacle/Evenement/[!Ev::Id!]|Sp|0|1]
										[STORPROC Reservation/Genre/Nom=[!Sp::Genre!]|G|0|1|Id|DESC]
											[!Genre[!G::Id!]+=[!NbPers!]!]
										[/STORPROC]
									[/STORPROC]
								[/STORPROC]
							[/STORPROC]
							<tr><td style="width:70mm;font-size:12px;">[!Cl::Nom!]</td><td style="width:30mm;font-size:12px;">[!Cl::Ville!]</td><td style="width:35mm;font-size:16px;text-align:right;padding-right:10px;">[!NbResa!]</td><td style="width:35mm;font-size:16px;text-align:right;padding-right:10px;">[!NbPersT!]</td></tr>
							[!TotResa+=[!NbResa!]!]
							[!TGTotResa+=[!NbResa!]!]
							[!TotPers+=[!NbPersT!]!]
							[!TGTotPers+=[!NbPersT!]!]
						[/STORPROC]
						<tr style="width:190mm;font-size:10px;background-color:#ccc;"><td colspan="2" style="text-align:right;font-size:20px;font-weight:bold;color:#ff0000;padding:20px;">Total GÉNÉRAL</td><td style="text-align:right;font-weight:bold;padding-right:10px;font-size:16px;"> [!TGTotResa!]</td><td style="text-align:right;font-weight:bold;padding-right:10px;font-size:16px;">[!TGTotPers!]</td></tr>

						<tr style="width:190mm;font-size:10px;">
							<td colspan="4" style="text-align:center;font-size:12px;font-weight:bold;color:#000;padding:5px;">Répartition par genre</td>
						</tr>
						[STORPROC Reservation/Genre|G|||Nom|ASC]
							[IF [!Genre[!G::Id!]!]!=0]
								<tr style="width:190mm;font-size:10px;">
									<td colspan="3" style="text-align:right;font-size:10px;font-weight:bold;color:#000;padding:2px;">
									[!G::Nom!]
									</td>
									<td style="text-align:right;font-size:10px;font-weight:bold;color:#000;padding:2px;">
										[!Tot:=[!Genre[!G::Id!]!]!]
										[!Tot/=[!TotPers!]!]
										[!Tot*=100!]
										[!Math::Price([!Tot!])!]% ([!Genre[!G::Id!]!])
									</td>
								</tr>
							[/IF]
						[/STORPROC]
	
					</table>
	
	
				</page>
			[/PARAM]
			[PARAM][/PARAM]
		[/METHOD]
		[!html2pdf::Output(Home/Pdf/RapportResa[!start!].pdf,F)!]

		<a href="http://www.cultureetsportsolidaires34.fr/Home/Pdf/RapportResa[!start!].pdf" style="padding:150px;font-weight:bold;color:red;" target="_blank" rel="link">
			Télécharger le pdf RapportResa[!start!] 
		</a>
		
		
	[ELSE]
		<div id="Container" style="overflow:auto;">
			<h2>Statistiques des structures sociales</h2>
			<div style="margin:0;font-size:15px;overflow: auto;">
			//	<form name="stats" action="">
					<input type="hidden" value="1" name="exportstats" >
					<input type="hidden" value="[!Ab!]" name="Ab" >
					<table><tr>
						<td>
							<label for="start">Année</label><input id="start" type="text" value="[!start!]" name="start" style="display: inline;">
							<button type="submit">OK</button>	
						</td>
					</tr>
					<tr>
						<td>
							<table><tr>
								[!Cpt:=4!]
								[STORPROC Reservation/Client|Cl|||Nom|ASC]
									[IF [!Cpt!]=0]</tr><tr>[!Cpt:=4!][/IF]
									<td><input type="checkbox" value="[!Cl::Id!]" name="Structures[]" ></td><td style="font-size:7px;">[!Cl::Nom!]</td>
									[!Cpt-=1!]
								[/STORPROC]
							</tr></table>

						</td>
					</tr></table>

				//</form>
			</div>
		</div>
	[/IF]
	
[ELSE]
	vous n'avez pas accès à cette fonctionnalité
[/IF]	
	
	
	
	
