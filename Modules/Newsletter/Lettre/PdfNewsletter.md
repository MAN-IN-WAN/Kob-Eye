[STORPROC [!Query!]|Let|0|1][/STORPROC]
[LIB HTML2PDF|html2pdf]
[METHOD html2pdf|writeHTML]
	[PARAM]
		<style type="text/css">
			table.interne {width:120mm;border: none; color:#000000;}
			.table.page_header { width:190mm;border: none; color:#000000;}
			.table.page_footer { width:190mm;border: none;color:#000000;}
			.bb_bold{font-weight:bold;}
			.bb_italic {font-style:italic;color:#3f3d3e;display:block;padding-top:0;margin-top:0;}
			.bb_italic .bb_bold {font-size:12px;}
			.bb_underline {text-decoration:underline;}
			.bb_strike {text-decoration:line-through;}
			.bb_quote {margin:20px;border:1px dotted black;}
			.bb_spoiler {margin:20px;border:1px dotted black;color:white;background-color:white;}
			.bb_uppercase {text-transform:uppercase;}
			.bb_lowercase {text-transform:lowercase;}
			.bb_a_url {text-decoration:none;color:#000000;}
			.bb_a_url:hover{text-decoration:underline;}
			li a.bb_a_url{text-decoration:none;color:#000000;}
			ul.bb_ul {overflow:auto;margin:0;padding:0;list-style-type:none;}
			li.bb_li {display:block;margin-bottom:5px;padding-left:12px;background:url(/home/olrap/www/Class/Lib/ImgHTML2PDF/puce-pdf.png) no-repeat 0 4px;}
		
		</style>
		<page  pageset="old" backtop="14mm" backbottom="10mm" backleft="5mm" backright="5mm" style="font-size: 12pt;width:190mm;">
			<table class="page_header" >
				<tr >
					<td style="width:140mm;text-align:left;" colspan="2">
						<img src="[!CONF::MODULE::SYSTEME::SITE!]/Skins/[!Systeme::Skin!]/Img/bando-newsletter.jpg" style="border:none;padding:10px;"/>					
					</td>
				</tr>
			</table>

			<page_footer>	
				<table class="page_footer">
					<tr>
						<td colspan="2" style="width:100%;"> <hr style="color:#F03B18;background:#F03B18;height:1px;border:0;margin-top:2px;margin-bottom:20px;"/>
						</td>
					</tr>
					<tr colspan="2">
						<td style="text-align:left;width:180mm;color:#F03B18;">Pour voir cette page en ligne rdv sur [!Domaine!]/[!Query!].htm</td>
					</tr>
				</table>
			</page_footer>

			[IF [!Let::Intro!]]
				<table class="page_header" >
					<tr>
						[STORPROC [!Query!]/Fichier/Positionnement=Intro|PictL|0|1|Ordre|ASC]
							<td style="width:20mm;">
								<img src="[!CONF::MODULE::SYSTEME::SITE!]/[!PictL::Fichier!]" alt="[!PictL::Titre!]" style="border:0;margin:0 10px 5px 0;"/>
							</td>
							<td style="padding:5px;width:140mm;text-align:justify;" valign="top">					[!Let::Intro!]
							</td>
							[NORESULT]
								<td colspan="2" style="padding:5px;width:150mm;">[!Let::Intro!]</td>
							[/NORESULT]
						[/STORPROC]
					</tr>
				</table>
			[/IF]
			<table class="page_header" >
				[STORPROC [!Query!]/Article|Art|0|100|Ordre|ASC]
					<tr>
						<td colspan="2" style="padding:5px;width:150mm;"> <hr style="color:#F03B18;background:#F03B18;height:1px;border:0;margin-top:2px;margin-bottom:20px;"/>
						</td>
					</tr>
					<tr>
						<td style="width:20mm;">
							[IF [!Art::Image!]!=]
								<img src="[!CONF::MODULE::SYSTEME::SITE!]/[!Art::Image!]" alt="[!Art::Titre!]" style="border:0;width:20mm;margin:0 10px 5px 0;"/>
							[/IF]
							[STORPROC Newsletter/Article/[!Art::Id!]/Fichier|Pict]
								<img src="[!CONF::MODULE::SYSTEME::SITE!]/[!Pict::Fichier!]" alt="[!Pict::Titre!]" style="border:0;margin:0 10px 5px 0;"/>
							[/STORPROC]
						</td>
						<td style="padding:5px;width:140mm;text-align:justify;" valign="top">
							<h3 style="font-family:Arial;font-weight:bold;font-style:normal;color:#F03B18;margin-bottom:10px;">[!Art::Titre!]</h3>
							<font face="Arial" size="2" color="#000000">
								[IF [!Arti::Chapo!]][!Art::Chapo!]<br /><br />[/IF]
								[!Art::Contenu!]<br /><br />
							</font>
						</td>
					</tr>
				[/STORPROC]
			</table>
			[IF [!Let::Conclu!]]
				<table class="page_header" >
					<tr>
						[STORPROC [!Query!]/Fichier/Positionnement=Conclusion|PictL|0|1|Ordre|ASC]
							<td style="width:20mm;">
								<img src="[!CONF::MODULE::SYSTEME::SITE!]/[!PictL::Fichier!]" alt="[!PictL::Titre!]" style="border:0;margin:0 10px 5px 0;"/>
							</td>
							<td style="padding:5px;width:140mm;text-align:justify;" valign="top">					[!Let::Conclu!]
							</td>
							[NORESULT]
								<td colspan="2" style="padding:5px;width:150mm;">[!Let::Conclu!]</td>
							[/NORESULT]
						[/STORPROC]
					</tr>
				</table>
			[/IF]
		</page>	
	[/PARAM]
	[PARAM][/PARAM]
[/METHOD]

[!html2pdf::Output!]


 