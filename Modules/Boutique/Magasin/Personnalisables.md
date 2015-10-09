[IF [!Systeme::User::Public!]]
	
[ELSE]
	[!NOMPDF:=Perso!]
	[LIB HTML2PDF|html2pdf]
	[METHOD html2pdf|writeHTML]
		[PARAM]
			<style type="text/css">
				table.page_header  {width:180mm; top:0;bottom:0 ; padding:0;margin:5px; }
				td {word-wrap: break-word; }
			</style>
			<page pageset="old" backtop="10mm" backbottom="10mm" backleft="10mm" backright="10mm" style="font-size: 10px;">
				<table class="page_header" cellspacing="0" cellspadding="0" border="1">
					<tr>
						<th colspan="2" style="border:none;background-color:#ccc;text-align:center;padding:10px;font-size:10px;font-weight:normal;"> Liste produit de type Personnalisables </th>
					</tr>
					<tr>
						<th style="text-align:center;font-size:10px;font-weight:normal;padding:5px;width:40mm;">Produit/Reference</th>
						<th style="text-align:center;font-size:10px;font-weight:normal;padding:5px;width:120mm;">Pack</th>
					</tr>
					[STORPROC Boutique/Produit/TypeProduit=5|P|0|90|tmsEdit|DESC]
						<tr>
							<td style="text-align:left;font-size:10px;font-weight:normal;padding:5px;width:40mm;vertical-aling:top;">
								<strong><span style="color:#ff0000;font-size:14px;">Id: [!P::Id!]--> </span>[!P::Nom!]<br /> <span style="color:#ff0000;">([!P::Id!])</span></strong><br />
								[STORPROC Boutique/Categorie/Produit/[!P::Id!]|C|0|1]
									Categorie : [!lacat:=!]
									[STORPROC Boutique/Categorie/*/Categorie/[!C::Id!]|C1]
										[!lacat+=[!C1::Nom!] - !]
									[/STORPROC]
									[!lacat+=[!C::Nom!]!]
								[/STORPROC]
								[!lacat!]<br />
								
								[STORPROC Boutique/Produit/[!P::Id!]/Reference|R|0|1]
									[!R::Nom!] - [!R::Reference!]<br /> <span style="color:#ff0000;">([!R::Id!])</span><br />
								[/STORPROC]
							</td>
							<td style="text-align:left;font-size:10px;font-weight:normal;padding:5px;width:120mm;">
								[STORPROC Boutique/Produit/[!P::Id!]/ConfigPack|Cpk|0|20|Ordre|ASC]
									[IF [!Pos!]!=1]<br /><br />[/IF][!Cpk::Ordre!] - [!Cpk::Nom!] <span style="color:#ff0000;">(([!Cpk::Id!]) </span>
									[STORPROC Boutique/ConfigPack/[!Cpk::Id!]/Options|opt]
										<br />------->Options   [!opt::Id!] : [!opt::Titre!]
									[/STORPROC]
									[COUNT Boutique/ConfigPack/[!Cpk::Id!]/Reference|Rcpk]
									<br /><strong>Nombre de Reference liées : [!Rcpk!]</strong>
								[/STORPROC]
							</td>
						</tr>
					[/STORPROC]
				</table>
			</page>
		[/PARAM]
		[PARAM][/PARAM]
	[/METHOD]
	[!html2pdf::Output([!NOMPDF!].pdf)!]
	
[/IF]
