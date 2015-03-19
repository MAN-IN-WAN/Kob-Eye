[STORPROC [!Query!]|Let|0|1][/STORPROC]
[LIB HTML2PDF|html2pdf]
[METHOD html2pdf|writeHTML]
	[PARAM]
		<style type="text/css">
			table.page_header { width:190mm;border: none; color:#000000;}
			table.page_footer { width:190mm;border: none; border-top: solid 1mm #000000; color:#000000;}
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
		<page  pageset="old" backtop="14mm" backbottom="10mm" backleft="5mm" backright="5mm" style="font-size: 12pt;">
			<table class="page_header" >
				<tr >
					<td style="width:150mm;text-align:left;">
						<img src="[!CONF::MODULE::SYSTEME::SITE!]/Skins/[!Systeme::Skin!]/Img/bando-newsletter.jpg" style="border:none;padding:10px;"/>					
					</td>
				</tr>
			</table>
			<page_footer>	
				<table class="page_footer">
					<tr >
						<td style="text-align:left;width:150mm;">Pour voir cette page en ligne rdv sur [!Domaine!]/[!Query!].htm</td>
						
					</tr>
				</table>
			</page_footer>
			<table class="page_header" >
				[IF [!Let::Intro!]]
					<tr style="padding-top:5px;" >
						<td style="padding:5px 5px 10px 5px;width:170mm;text-align:left;" valign="top">
							<font face="Arial" size="2">[!Let::Intro!]</font>
						</td>
					</tr>
				[/IF]
			</table>
		
			<table class="page_header" >
				[STORPROC [!Query!]/Article|Art|0|100|Ordre|ASC]
					<tr>
						<td>
							[COUNT Newsletter/Article/[!Art::Id!]/Fichier|P]
							[IF [!Art::Image!]||[!P!]]
								[COUNT [!Query!]/Article|A]
								[IF [!Math::Floor([!Pos:/2!])!]==[!Pos:/2!]]
									<div style="float:right;width:200px;margin-left:10px;">
								[ELSE]
									<div style="float:left;width:200px;margin-right:10px;">
								[/IF]
								[IF [!Art::Image!]!=]
									<img src="[!CONF::MODULE::SYSTEME::SITE!]/[!Art::Image!]" alt="[!Art::Titre!]" style="border:0;width:200px;margin:0 10px 5px 0;"/>
								[/IF]
								[STORPROC Newsletter/Article/[!Art::Id!]/Fichier|Pict]
									<img src="[!CONF::MODULE::SYSTEME::SITE!]/[!Pict::Fichier!]" alt="[!Pict::Titre!]" style="border:0;margin:0 10px 5px 0;"/>
								[/STORPROC]
								</div>
							[/IF]
						</td>
						<td>
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
						<td style="padding:5px 5px 10px 5px;" align="left" valign="top">
							<div style="border-top:3px solid #F03B18;margin-top:5px;text-align:justify;margin:0 10px 0 10px;">
								<hr style="color:#F03B18;background:#F03B18;height:1px;border:0;margin-top:2px;margin-bottom:20px;"/>
								<font face="Arial" size="2">[!Let::Conclu!]</font>
							</div>
						</td>
					</tr>
				</table>
			[/IF]
		</page>	
	[/PARAM]
	[PARAM][/PARAM]
[/METHOD]

[!html2pdf::Output!]


 