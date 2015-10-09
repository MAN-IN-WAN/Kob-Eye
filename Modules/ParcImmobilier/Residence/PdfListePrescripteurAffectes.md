[IF [!Systeme::User::Public!]]
	[REDIRECT]/[/REDIRECT]
[ELSE]
	[STORPROC [!Query!]|RS|0|1]
		[LIB HTML2PDF|html2pdf]
		[METHOD html2pdf|writeHTML]
			[PARAM]
				<page pageset="old" backtop="5mm" backbottom="5mm" backleft="5mm" backright="5mm" style="font-size: 12pt">
					<table border="1" cellspacing="0" cellspadding="0" style="width:100%;">
						<tr>
							<th colspan="3" style="border:none;background-color:#ccc;text-align:center;padding:10px;font-size:10px;font-weight:normal;"> Liste Prescripteur liés à Résidence [!Rs::Nom!]</th>
						</tr>
						<tr>
							<th style="text-align:center;font-size:10px;font-weight:normal;padding:5px;">Prescripteur</th>
							<th style="text-align:center;font-size:10px;font-weight:normal;padding:5px;">Commercial</th>
							<th style="text-align:center;font-size:10px;font-weight:normal;padding:5px;">Ville</th>
						</tr>
						[STORPROC Systeme/User/Residence/[!RS::Id!]|Prs]
							[STORPROC Systeme/User/[!Prs::Id!]/Commercial|Ccal|0|1][/STORPROC]
							<tr>	
								<td style="text-align:center;font-size:10px;font-weight:normal;padding:5px;">[!Prs::Nom!]</td>
								<td style="text-align:center;font-size:10px;font-weight:normal;padding:5px;"> [!Ccal::Nom!]</td>
								<td style="text-align:center;font-size:10px;font-weight:normal;padding:5px;">[!Ccal::Ville!]</td>
							</tr>
						[/STORPROC]
					</table>
				</page>
			[/PARAM]
			[PARAM][/PARAM]
		[/METHOD]
		[!html2pdf::Output(Home/Pdf/PragmaResi.pdf)!]
	[/STORPROC]
[/IF]