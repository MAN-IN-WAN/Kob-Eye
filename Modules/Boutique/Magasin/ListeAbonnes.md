[IF [!Systeme::User::Public!]]
	
[ELSE]
[!Filtre:=!][!NOMPDF:=MagService!]
	[STORPROC [!Query!]|Mag][/STORPROC]
	[LIB HTML2PDF|html2pdf]
	[METHOD html2pdf|writeHTML]
		[PARAM]
			<style type="text/css">
				table.page_header  {width:200mm; top:0;bottom:0 ; padding:0;margin:0; }
	
			</style>
			<page pageset="old" backtop="14mm" backbottom="1mm" backleft="10mm" backright="10mm" style="font-size: 10pt">
				<table class="page_header" cellspacing="0" cellspadding="0" border="1">
					<tr  >
						<td colspan="5" style="text-align:center;">[!NOMPDF!] de [!Mag::Nom!]</td>
					</tr>
					[STORPROC Boutique/Magasin/[!Mag::Id!]/Service|Srv|0|2000|Nom,DateDebut|ASC]
						[IF [!LeService!]!=[!Srv::Nom!]||[!LaDateDebut!]!=[!Srv::DateDebut!]||[!LaDateFin!]!=[!Srv::DateFin!]]
							[COUNT Boutique/Magasin/[!Mag::Id!]/Service/Nom=[!Srv::Nom!]&DateDebut=[!Srv::DateDebut!]&DateFin=[!Srv::DateFin!]|NbAbo]
							<tr><td colspan="5" style="text-align:center;font-size:14px;background-color:#313131;color:#fff;">[!Srv::Nom!] du [!Utils::getDate(d/m/Y,[!Srv::DateDebut!])!] au [!Utils::getDate(d/m/Y,[!Srv::DateFin!])!] ([!NbAbo!] abonnements)</td></tr>
							[!LeService:=[!Srv::Nom!]!][!LaDateDebut:=[!Srv::DateDebut!]!][!LaDateFin:=[!Srv::DateFin!]!]
							<tr style="text-align:center;font-size:14px;background-color:#ccc;">
								<td style="text-align:center;">Client</td>
								<td style="text-align:center;">Début</td>
								<td style="text-align:center;">Fin</td>
								<td style="text-align:center;">Taux de Remise</td>
								<td style="text-align:center;">Type</td>
							</tr>
						[/IF]
						[STORPROC Boutique/Client/Service/[!Srv::Id!]|Cli|0|1][/STORPROC]
						[!LieCd:=Importé!]
						[STORPROC Boutique/Service/[!Srv::Id!]/LigneCommande|LC|0|1] [!LieCd:=Commandé!][/STORPROC]
						<tr >
							<td style="text-align:center;">[!Cli::Nom!] -[!Cli::Mail!]</td>
							<td style="text-align:center;">[!Utils::getDate(d/m/Y,[!Srv::DateDebut!])!]</td>
							<td style="text-align:center;">[!Utils::getDate(d/m/Y,[!Srv::DateFin!])!]</td>
							<td style="text-align:center;">[!Srv::Remise!]</td>
							<td style="text-align:center;">[!LieCd!]</td>
						</tr>
						
					[/STORPROC]
				</table>
			</page>
		[/PARAM]
		[PARAM][/PARAM]
	[/METHOD]
	[!html2pdf::Output([!NOMPDF!].pdf)!]
	
[/IF]