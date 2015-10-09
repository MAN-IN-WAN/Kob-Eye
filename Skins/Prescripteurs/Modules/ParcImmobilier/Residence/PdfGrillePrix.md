[OBJ ParcImmobilier|Residence|ModelR]
[LIB HTML2PDF|html2pdf]
[METHOD html2pdf|writeHTML]
	[PARAM]
		<page pageset="old" backtop="14mm" backbottom="10mm" backleft="5mm" backright="5mm" >
   			<table >
				<tr style="width:170mm;">
					<td  style="text-align:center;color:#0070BA;font-size:16px;text-transform:uppercase;text-decoration:underline;width:170mm;" >Grilles des prix [!FiltreActions!]</td>
				</tr>
			</table>
			<page_footer>	
				<table style="font-family:arial;font-size:12px; color:#0070BA;border-top:1px solid #0070BA;width:170mm;">
					<tr style="width:170mm;">
						<td style="width:140mm;text-align:left;">Pragma-immobilier pour les partenaires</td>
						<td style="width:25mm;text-align:right">&copy;Abtel 2012</td>
					</tr>
				</table>
			</page_footer>
			[!ResidenceLue:=!]
		    [STORPROC [!ModelR::getMesLots([!Departement!],[!Ville!],[!Budget!],[!Fiscalite!],[!Type!],[!ResidenceLot!],[!FiltreActions!],0,0,300000)!]|L]
		    	[IF [!L::StatutLot!]=1]
		       		[IF [!ResidenceLue!]!=[!L::ResidenceId!]]
			       		[STORPROC ParcImmobilier/Residence/[!L::ResidenceId!]|R][/STORPROC]
		       			[STORPROC ParcImmobilier/Ville/Residence/[!R::Id!]|V|0|1][/STORPROC]
		       			[IF [!ResidenceLue!]!=]</table>[!JaiOuver:=0!][/IF]
						[!ResidenceLue:=[!L::ResidenceId!]!]
						<table style="font-family:arial;font-size:12px; color:#000;height:10mm;vertical-align:middle;">
							<tr style="width:170mm;padding-top:5px;margin:5px 0 10px 0;">
								<th style="color:#0070BA;font-size:14px;font-variant:small-caps;">
									<br /><br /><br />Residence : [!R::Titre!] - [!V::Nom!] - [SUBSTR 2][!V::CodePostal!][/SUBSTR]<br />
								</th>
							</tr>
						</table>
						<table style="font-family:arial;font-size:12px; color:#000;border-collapse:collapse;"  >
							[!JaiOuver:=1!]
					        <tr style="width:170mm;" >
					            <th style="border-top:solid 1px #0070BA;border-bottom:solid 1px #0070BA;border-left:solid 1px #0070BA;border-right:solid 1px #0070BA;width:70mm;text-align:center;padding:10px;">Lot</th>
					            <th style="border-top:solid 1px #0070BA;border-bottom:solid 1px #0070BA;border-right:solid 1px #0070BA;width:70mm;text-align:center;padding:10px;">Description</th>
					            <th style="border-top:solid 1px #0070BA;border-bottom:solid 1px #0070BA;border-right:solid 1px #0070BA;width:20mm;text-align:center;padding:10px;">Prix</th>
					        </tr>
					[/IF]
					[!TypeAppart:=!][!S:=!]
					[IF [!L::NbLots!]>1][!S:=s!][/IF]
					[IF [!L::TypeLogement!]=T1][!TypeAppart:=appartement[!S!] 1 pce!] [/IF]
					[IF [!L::TypeLogement!]=T2][!TypeAppart:=appartement[!S!] 2 pces!] [/IF]
					[IF [!L::TypeLogement!]=T3][!TypeAppart:=appartement[!S!] 3 pces!] [/IF]
					[IF [!L::TypeLogement!]=T4][!TypeAppart:=appartement[!S!] 4 pces!] [/IF]
					[IF [!L::TypeLogement!]=T5][!TypeAppart:=appartement[!S!] 5 pces!] [/IF]
					[IF [!L::TypeLogement!]=Villa][!TypeAppart:=villa[!S!]!] [/IF]
					[IF [!L::TypeLogement!]=Studio][!TypeAppart:=studio[!S!]!] [/IF]
	
			        <tr>
			            <td style="border-top:solid 1px #0070BA;border-bottom:solid 1px #0070BA;border-left:solid 1px #0070BA;border-right:solid 1px #0070BA;width:70mm;text-align:left;padding:10px;">[!TypeAppart!] n°[!L::Identifiant!]</td>
			            <td style="border-top:solid 1px #0070BA;border-bottom:solid 1px #0070BA;border-left:solid 1px #0070BA;border-right:solid 1px #0070BA;width:70mm;text-align:left;padding:10px;"> [!Utils::getPrice([!L::SurfaceLogement!])!] m² - [!L::Etage!]</td>
			            <td style="border-top:solid 1px #0070BA;border-bottom:solid 1px #0070BA;border-left:solid 1px #0070BA;border-right:solid 1px #0070BA;width:20mm;text-align:right;padding:10px;">[!L::Tarif!] €</td>
			        </tr>
			    [/IF]
				[IF [!Pos!]=[!NbResult!]&&[!JaiOuver!]=1]</table>[/IF]
		
			[/STORPROC]
		</page>	
	[/PARAM]
	[PARAM][/PARAM]
[/METHOD]

[!html2pdf::Output!]

