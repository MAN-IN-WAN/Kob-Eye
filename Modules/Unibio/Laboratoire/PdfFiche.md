[LIB HTML2PDF|html2pdf]
[METHOD html2pdf|writeHTML]
	[PARAM]
		<page  pageset="old" backtop="14mm" backbottom="10mm" backleft="5mm" backright="5mm" style="font-size: 12pt;">
			[STORPROC [!Query!]|Lab|0|1]
				<table>
					<tr><td colspan="2" style="font-size:16px;font-family:arial;color:#66a7bc;margin-bottom:20px;text-decoration:underline;">Fiche détaillée du laboratoire</td></tr>

					<tr>
						<td>
							<table>
								<tr>
									<td style="display:block;font-size:16px;font-family:arial;color:#66a7bc;">[!Lab::Nom!]</td>
								</tr>
								<tr>
									<td style="display:block;font-size:12px;font-family:arial;color:#474747;">
										[STORPROC [!Query!]/Professionel|Pro]
											[!Pro::Nom!] [!Pro::Prenom!] - [!Pro::Profession!]<br />
										[/STORPROC]
									</td>
								</tr>
								[IF [!Lab::Photo!]]<tr>
									<td><img src="[!Domaine!]/[!Lab::Photo!].limit.180x161.jpg" /></td>
								</tr>[/IF]
							</table>
						</td>
						<td>
							<table>
								<tr>
									<td style="display:block;font-size:12px;font-family:arial;color:#474747;"><strong>ADRESSE : </strong>[!Lab::Adresse!]</td>
								</tr>
								<tr>
									<td style="display:block;font-size:12px;font-family:arial;color:#474747;"><strong>Tél : [!Lab::Tel!]</strong> - Fax : [!Lab::Fax!]</td>
								</tr>
								<tr>
									<td style="display:block;font-size:12px;font-family:arial;color:#474747;"><strong>Tél : [!Lab::Tel!]</strong> - Fax : [!Lab::Fax!]</td>
								</tr>
	
								<tr>
									<td style="display:block;font-size:12px;font-family:arial;color:#474747;">[!Lab::Horaires!]</td>
								</tr>
	
								<tr>
									<td style="display:block;font-size:12px;font-family:arial;color:#474747;"><p>[!Lab::Description!]</p></td>
								</tr>
							</table>
						</td>
					</tr>
				
	
				[/STORPROC]
			</table>
			<page_footer >
				<table class="page_footer">
					<tr>
						<td style="width:180mm;text-align:left;">Cette impression est faite à partir du site <a href="[!Domaine!]" >[!Domaine!]</a></td>
					</tr>
				</table>
			</page_footer>

    		</page>	
	[/PARAM]
	[PARAM][/PARAM]
[/METHOD]

[!html2pdf::Output!]


 