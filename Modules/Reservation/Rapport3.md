[IF [!Systeme::User::Public!]!=1||[!Ab!]=23686]
	[!DateDebut:=[!Utils::getTms(1/1/[!Annee!]) 00:00!]!]
	[!DateFin:=[!Utils::getTms(31/12/[!Annee!] 23:59)!]!]

	[!TotalResa:=0!]
	[!TotalPers:=0!]
	[LIB HTML2PDF|html2pdf]
	[METHOD html2pdf|writeHTML]
		[PARAM]
			<page pageset="old" backtop="14mm" backbottom="1mm" backleft="10mm" backright="10mm" style="font-size: 12pt">
				<table class="page_header" cellspacing="0" cellspadding="0" border="1">
					<tr style="width:190mm;font-size:10px;background-color:#ccc;">
						<td colspan="4" style="font-size:14px;font-weight:bold;padding:20xp;text-align:center;">Liste des réservations du [DATE d/m/Y][!DateDebut!][/DATE] au [DATE d/m/Y][!DateFin!][/DATE]</td>
					</tr>
					<tr style="width:190mm;font-size:10px;"><td colspan="4" style="font-size:14px;font-weight:bold;padding:20px;text-align:center;background-cole:#ccc;">Centre Sociaux</td></tr>
					<tr style="width:190mm;font-size:10px;"><th style="text-align:center;font-weight:bold;">Structure</th><th style="text-align:center;font-weight:bold;">Ville</th><th style="text-align:center;font-weight:bold;">Nombre de réservation</th><th style="text-align:center;font-weight:bold;">Nombre de personnes</th></tr>
					[!TotResa:=0!]
					[!TGTotResa:=0!]
					[!TotPers:=0!]
					[!TGTotPers:=0!]
					[STORPROC Reservation/Genre|G]
						[!Genre[!G::Id!]:=0!]
					[/STORPROC]
					// demande pour structures bien precises
					[STORPROC Reservation/Client/Id=149+Id=23+Id=50+Id=70+Id=103|Cl]
						[!NbResa:=0!]						
						[!NbResaArt:=0!]
						[!NbPersT:=0!]
						[!NbPersTArt:=0!]
						// 01/01/15 00:00:00 au 31/12/15 23:59:59
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
						<tr><td style="width:70mm;font-size:12px;">[!Cl::Nom!]</td><td style="width:30mm;font-size:12px;">[!Cl::Ville!]</td><td style="width:35mm;font-size:16px;text-align:right;padding-right:10px;">[!NbResa!] </td><td style="width:35mm;font-size:16px;text-align:right;padding-right:10px;">[!NbPersT!] </td></tr>
						[!TotResa+=[!NbResa!]!]
						[!TotPers+=[!NbPersT!]!]
						[!TGTotResa+=[!NbResa!]!]
						[!TGTotPers+=[!NbPersT!]!]

					[/STORPROC]
					<tr style="width:190mm;font-size:10px;">
						<td colspan="2" style="text-align:right;font-size:14px;font-weight:bold;color:#ff0000;padding:20px;">Total Centre Sociaux</td>	
						<td style="text-align:right;font-weight:bold;padding-right:10px;font-size:16px;"> [!TotResa!]</td>
						<td style="text-align:right;font-weight:bold;padding-right:10px;font-size:16px;">[!TotPers!]</td>
					</tr>
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
					[!TotResa:=0!][!TotPers:=0!]

					<tr style="width:190mm;font-size:10px;"><td colspan="4" style="font-size:14px;font-weight:bold;padding:20xp;text-align:center;background-cole:#ccc;padding:20px;"> Espaces de vie sociale</td></tr>

					<tr style="width:190mm;font-size:10px;"><th style="text-align:center;font-weight:bold;">Structure</th><th style="text-align:center;font-weight:bold;">Ville</th><th style="text-align:center;font-weight:bold;">Nombre de réservation</th><th style="text-align:center;font-weight:bold;">Nombre de personnes</th></tr>
					[STORPROC Reservation/Genre|G]
						[!Genre[!G::Id!]:=0!]
					[/STORPROC]

					[STORPROC Reservation/Client/Id=608+Id=66+Id=662+Id=282+Id=294+Id=302+Id=197+Id=515+Id=41+Id=36|Cl]
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
					<tr style="width:190mm;font-size:10px;"><td colspan="2" style="text-align:right;font-size:14px;font-weight:bold;color:#ff0000;padding:20px;">Total Espaces de vie sociale</td><td style="text-align:right;font-weight:bold;padding-right:10px;font-size:16px;"> [!TotResa!]</td><td style="text-align:right;font-weight:bold;padding-right:10px;font-size:16px;">[!TotPers!]</td></tr>
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
					<tr style="width:190mm;font-size:10px;background-color:#ccc;"><td colspan="2" style="text-align:right;font-size:20px;font-weight:bold;color:#ff0000;padding:20px;">Total GÉNÉRAL</td><td style="text-align:right;font-weight:bold;padding-right:10px;font-size:16px;"> [!TGTotResa!]</td><td style="text-align:right;font-weight:bold;padding-right:10px;font-size:16px;">[!TGTotPers!]</td></tr>

				</table>


			</page>
		[/PARAM]
		[PARAM][/PARAM]
	[/METHOD]

	[!html2pdf::Output(RapportResa2015.pdf)!]

[/IF]
