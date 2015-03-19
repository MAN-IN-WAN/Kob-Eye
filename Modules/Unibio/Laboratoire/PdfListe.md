[IF [!Domaine!]~unibio.fr]
	[!FiltrePublic:=1!]
[/IF]
// pour filtrer en fonction de l'entité à afficher Unibio ou Biomed
[!FiltreEntite:=/Entite=Unibio!]
[IF [!Domaine!]~biomed34.fr][!FiltreEntite:=/Entite=Biomed!][/IF]
[IF [!Domaine!]~intranet][!FiltreEntite:=!][/IF]
[!Requete:=!]
[IF [!Zone!]]
	[!Requete+=Unibio/Region/[!Zone!]/Laboratoire[!FiltreEntite!]!]
[ELSE]
	[IF [!FiltrePublic!]]
		[!Requete+=Unibio/Region/Public=1/Laboratoire[!FiltreEntite!]!]
	[ELSE]
		[!Requete+=Unibio/Region/*/Laboratoire[!FiltreEntite!]!]
	[/IF]
[/IF]
[LIB HTML2PDF|html2pdf]
[METHOD html2pdf|writeHTML]
	[PARAM]
	<page  pageset="old" backtop="14mm" backbottom="10mm" backleft="5mm" backright="5mm" style="font-size: 12pt;">
		<h2>
			[IF [!Zone!]=]
				Les laboratoires
			[ELSE]
				[STORPROC Unibio/Region/[!Zone!]|Z]
					Les laboratoires de [!Z::Nom!]
				[/STORPROC]
			[/IF]
		</h2>
		<table>
			<tr><th>Laboratoire</th><th>Informations</th></tr> 
			[STORPROC [!Requete!]|Lab|||Nom|ASC]
				<tr>
					<td style="display:block;font-size:16px;font-family:arial;color:#66a7bc;">
						[!Lab::Nom!]<br />
						[STORPROC Unibio/Laboratoire/[!Lab::Id!]/Professionel|Pro]
							<span style="color:#000;font-size:10px;font-family:arial;"><br />[!Pro::Nom!] [!Pro::Prenom!] - [!Pro::Profession!]</span>
						[/STORPROC]
					</td>
					<td style="display:block;font-size:12px;font-family:arial;color:#474747;">
						[IF [!Lab::Photo!]!=]
							<table><tr>
								<td><img src="[!Domaine!]/[!Lab::Photo!]" width="90" height:="75" /></td>
								<td>
									<strong>ADRESSE : </strong>[!Lab::Adresse!]<br />
									<strong>Tél : [!Lab::Tel!]</strong> - Fax : [!Lab::Fax!]
								</td>
							</tr></table>
						[ELSE]
							<strong>ADRESSE : </strong>[!Lab::Adresse!]<br />
							<strong>Tél : [!Lab::Tel!]</strong> - Fax : [!Lab::Fax!]
						[/IF]
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


 
