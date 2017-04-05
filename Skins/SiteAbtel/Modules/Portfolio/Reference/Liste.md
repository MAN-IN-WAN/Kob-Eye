[INFO [!Chemin!]|I]
[IF [!I::NbHisto!]>0]
	[STORPROC [!I::Historique!]|Cec|0|1]
		[IF [!Cec::DataSource!]=Categorie]
			[!C:=[!Cec::Value!]!]
		[/IF]
	[/STORPROC]
[ELSE]
	[!C:=5!]
[/IF]
[IF [!C!]!=][STORPROC Portfolio/Categorie/[!C!]|Cec|0|1][/STORPROC][/IF]
<div id="headerRefs">
	<div class="container">
		<h1>[!Systeme::CurrentMenu::Titre!]</h1>
	</div>
</div>
<div class="container">
	[MODULE Portfolio/Structure/Gauche?Chemin=[!Chemin!]]
	<div id="Milieu" style="margin-left:250px;">
		<div id="Data" style="border-top:0px solid #827152;padding-top:1px;">
			[STORPROC [!Chemin!]/Publier=1|Ref|0|100|DateSortie|DESC]
				<div class="[IF [!Math::Round([!Pos:/3!])!]=[!Pos:/3!]]LeftLast[ELSE]Left[/IF]">
					<div class="inner">
						<a href="/Les-References/Categorie/[!Cec::Url!]/Reference/[!Ref::Url!]" title="Voir le d&eacute;tail de [!Ref::Titre!]">
							[IF [!Ref::Icone!]]
								<img src="/[!Ref::Icone!]" alt="[!Ref::Titre!]" />
							[ELSE]
								<img src="/Skins/Expressiv/Img/RefDefault.jpg" alt="[!Ref::Titre!]"/>
							[/IF]
						</a>
						<div class="InfoRef">
							<span class="DateSortie" style="float:right;">
								[DATE m.Y][!Ref::DateSortie!][/DATE]
							</span>
							<h2 style="padding:0;font-style:normal;color:#939292;text-transform:none;">[!Ref::Titre!]</h2>
						</div>
						<p style="width:230px;">[!Ref::Chapo!]</p>
					</div>
				</div>
			[/STORPROC]
		</div>
	</div>
</div>
