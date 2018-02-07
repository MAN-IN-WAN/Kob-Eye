// Fiche Pdf d'un produit
[!LienFinal:=!]
[INFO [!Lien!]|I]
[STORPROC [!I::Historique!]|H]
	[IF [!H::Value!]~Pdf][!LienFinal+= !][ELSE][!LienFinal+=[!H::Value!]/!][/IF]
[/STORPROC]

[STORPROC [!Query!]|P|0|1]
	[STORPROC Catalogue/Categorie/Produit/[!P::Id!]|Cat|0|1][/STORPROC]
[/STORPROC]

[LIB HTML2PDF|html2pdf]
[METHOD html2pdf|writeHTML]
	[PARAM]
		<style type="text/css">
			body {font-family:verdana;font-size:12px; }
			table.page_header { width:170mm; border: none; }
    			table.page_footer { width:170mm; border: none; border-top: solid 1mm #536281; }
			ul.bb_ul {overflow:auto;margin:0;padding:0;list-style-type:none;}
			li.bb_li {
				display:block;
				margin-bottom:5px;
				padding-left:12px;
				list-style:circle;
			}
			.bb_bold { font-weight:bold;}
		</style>
		<page  pageset="old" backtop="14mm" backbottom="10mm" backleft="5mm" backright="5mm" >
			<table class="page_header" >
				<tr style="width:170mm;padding-top:5px;margin:5px">
					<td style="text-align:center;vertical-align:top;">
						<img src="Skins/Public/Img/bando-mail.jpg.limit.600x300.jpg"  />
					</td>
				</tr>
			</table>			
			<page_footer>	
				<table class="page_footer">
					<tr>
						<td style="width:180mm;text-align:right;color:#536281;">Axenergie Gaz Service</td>
					</tr>
				</table>
			</page_footer>
			<table class="page_header" >
				<tr style="width:170mm;padding-top:5px;margin:5px;border-bottom:1px dotted #536281;height:390px;vertical-align:top;">
					<td style="width:80mm;text-align:left">
						[IF [!P::Image!]!=]<img src="[!P::Image!].limit.256x385.jpg"  />[/IF]
					</td>
					<td style="width:80mm;text-align:left;padding-top:40px;">
						<div style="color:#af0410;font-weight:bold;font-variant:small-caps;font-size:14px;height:20px;">[!Cat::Nom!]</div>
						[IF [!P::Fabricant!]!=]
							[STORPROC Catalogue/Fabricant/[!P::Fabricant!]|Fab|0|1][/STORPROC]
							<div style="color:#000;font-weight:bold;font-variant:small-caps;font-size:14px;height:20px;">[!Fab::Nom!]</div>
						[/IF]
						[IF [!P::Titre!]!=]<div style="color:#536281;font-weight:bold;font-size:14px;height:20px;">[!P::Titre!]</div>[/IF]
						<hr style="color:#536281;">						
						[IF [!P::Dimensions!]!=]<div style="color:#000;font-size:14px;padding-top:10px;">
							<span style="color:#000;font-size:14px;font-weight:bold;"> Dimensions : </span>[!P::Dimensions!]
						</div>[/IF]

						[IF [!P::SolMurale!]!=]<div style="color:#000;font-size:14px;padding-top:10px;" >
							<span style="color:#000;font-size:14px;font-weight:bold;"> Pose : </span>[!P::SolMurale!]
						</div>[/IF]
						[IF [!P::Service!]!=]<div style="color:#000;font-size:14px;padding-top:10px;" >
							<span style="color:#000;font-size:14px;font-weight:bold;"> Service : </span>[!P::Service!]
						</div>[/IF]
						[IF [!P::Evacuation!]!=]<div style="color:#000;font-size:14px;padding-top:10px;" >
							<span style="color:#000;font-size:14px;font-weight:bold;"> Evacuation : </span> 
								[SWITCH [!P::Evacuation!]|=]
									[CASE CF]
										- Conduit Fumée
									[/CASE]
									[CASE FF]
										- Flux forcé
									[/CASE]
									[CASE VMC]
										- VMC
									[/CASE]
								[/SWITCH]
						</div>[/IF]
						[IF [!P::Puissance!]!=]<div style="color:#000;font-size:14px;padding-top:10px;" >
							<span style="color:#000;font-size:14px;font-weight:bold;"> Puissance : </span>[!P::Puissance!]
						</div>[/IF]
						[IF [!P::Sanitaire!]!=]<div style="color:#000;font-size:14px;padding-top:10px;" >
							<span style="color:#000;font-size:14px;font-weight:bold;"> Type sanitaire : </span>[!P::Sanitaire!]
						</div>[/IF]
						[IF [!P::DebitSanitaire!]!=]<div style="color:#000;font-size:14px;padding-top:10px;" >
							<span style="color:#000;font-size:14px;font-weight:bold;"> Débit sanitaire : </span>[!P::DebitSanitaire!]
						</div>[/IF]
						[STORPROC Catalogue/Fabricant/[!P::Fabricant!]|Fab|0|1]
							[IF [!Fab::Logo!]!=]<div style="text-align:right;padding-top:30px;">
								<img src="[!Fab::Logo!]"  />
							</div>[/IF]
						[/STORPROC]
					</td>
				</tr>
			</table>
			<table class="page_header" >
				<tr style="width:170mm;padding-top:5px;margin:5px">
					<td style="width:170mm;text-align:left"  >
						[IF [!P::Description!]!=]
							<div style="color:#536281;font-weight:bold;text-transform:uppercase;font-size:14px;">Description</div>
							<p style="color:#000;font-size:14px;text-align:justify;">
								[!P::Description!]
							</p>
						[/IF]
						[IF [!P::Avantages!]!=]
							<div style="color:#536281;font-weight:bold;text-transform:uppercase;font-size:14px;">Avantages produits</div>
							<p style="color:#000;font-size:14px;text-align:justify;">[!P::Avantages!]</p>
						[/IF]
					</td> 
				</tr>
			</table>

		</page>	
	[/PARAM]
	[PARAM][/PARAM]
[/METHOD]

[!html2pdf::Output(Home/Pdf/[!P::Url!]_[!P::tmsEdit!].pdf,FI)!]
//[!html2pdf::Output!]

