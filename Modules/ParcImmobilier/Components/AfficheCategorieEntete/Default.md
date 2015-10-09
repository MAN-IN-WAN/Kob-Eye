
	<head>
		<style type="text/css">
			div#Anim {
				height: 322px;
				overflow: hidden;
				position: relative;   width: 1024px;
			}
			div.FondAnim {
				
				display:block;
			}
			
			
			div#AnimApercu {
				position: absolute;
				top: 0;
				left: 0;
				height:322px;
				width: 1024px;
			}
			div#AnimListFond {
				background: rgba(255, 255, 255, 0.71);
				height: 198px;
				left: 14px;
				position: absolute;
				top: 68px;
				width: 180px;
				z-index: 100;
			}
			
			div#AnimList {
				left: 14px;
				position: absolute;z-index:200;
				top: 83px;
				width: 220px;
				background: url('[!Domaine!]/Skins/[!Systeme::Skin!]/Img/Bando/fleche-accueil.png') no-repeat;
	
	
			}
			div#AnimList ul {
				list-style:none; margin: 0 ;padding:0;
			}
			div#AnimList li.AnimActive {
				border-top: 1px dotted white;
				border-bottom: 1px dotted white;
			}
			
			div.hidden {
				display: none;
			}
			
			div#AnimList li.AnimTxtGauche {
				height: 55px;padding-left: 10px;
				line-height:45px;
				width: 180px;
				border-bottom: 1px dotted #393939;
			}
			
			div#AnimList li.AnimTxtGauche a {
				color: #333333;text-transform:uppercase;
				text-decoration: none;
				font-size: 17px;
				width: 180px;
				vertical-align: middle;
				display: inline-block;
				line-height: normal;
			}
		</style>
	
	</head>
	<div id="Anim">
		<div id="FondAnimMilieu">
			<div id="AnimApercu">
				[STORPROC ParcImmobilier/CategorieHeader/1/Header/Publier=1|H|0|3]
					<div class="AnimApercuImg">
						[IF [!H::FondAnimation!]!=]
							[!ImgFond:=[!H::BandeauNew!]!]
						[/IF]
	
						[IF [!H::Lien!]~http||[!H::Lien!]~www]
							<a href="[!H::Lien!]"  target="_blank" ><img src="/[!H::Bandeau!]" alt="" /></a>
						[ELSE]
							<a href="[IF [!H::Lien!]~/][ELSE]/[/IF][!H::Lien!]" target="_blank" ><img src="/[!H::Bandeau!]" alt="" /></a>
						[/IF]
					</div>				
				[/STORPROC]
			</div>
			<div id="AnimListFond"></div>
			<div id="AnimList">
				<ul>
					[STORPROC ParcImmobilier/CategorieHeader/1/Header/Publier=1|H|0|3]
						<li class="AnimTxtGauche" [IF  [!Pos!]=3] style=" border-bottom:none;"[/IF]>
							[IF [!H::Lien!]~http||[!H::Lien!]~www]
								<a href="[!H::Lien!]" [IF [!Pos!]=1]style="color:#ffffff"[/IF]>[!H::TexteGauche!]</a>
							[ELSE]
								<a href="[IF [!H::Lien!]~/][ELSE]/[/IF][!H::Lien!]" [IF [!Pos!]=1]style="color:#ffffff"[/IF]>[!H::TexteGauche!]</a>
							[/IF]
		
	
	
						</li>
					[/STORPROC]
				</ul>
			</div>
		</div>
	</div>
