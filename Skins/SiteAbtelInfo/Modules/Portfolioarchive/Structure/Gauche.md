<div style="width:240px;float:left;display:block;position:relative;background-color:#F5F5F5;">
	//[MODULE Portfolio/Structure/Recherche]

	//RECHERCHE DE LA CATEGORIE EN COURS
	[IF [!Chemin!]=][!Chemin:=[!Query!]!][/IF]
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

	//RECHERCHE DE LA REFERENCE EN COURS
	[IF [!I::NbHisto!]>0]
		[STORPROC [!I::Historique!]|Re|0|10]
			[IF [!Re::DataSource!]=Partenaire]
				[!R:=[!Re::Value!]!]
			[/IF]
		[/STORPROC]
	[/IF]
	[IF [!R!]!=][STORPROC Portfolio/Partenaire/[!R!]|Rec|0|1][/STORPROC][/IF]

	//AFFICHAGE
	[STORPROC Portfolio/Categorie/Publier=1|Cat]
	<h3 class="Reference"><a href="/[!Systeme::CurrentMenu::Url!]/Categorie/[!Cat::Url!]" [IF [!Cat::Id!]=[!Cec::Id!]] style="font-weight:bold;color:#939292;"[/IF] >[!Cat::Nom!]</a></h3>
		[IF [!Cat::Id!]=[!Cec::Id!]]
			[STORPROC Portfolio/Categorie/[!Cat::Id!]/Partenaire/Publier=1|Ref|0|100|DateCollaboration|DESC]
				<div class="Projet" style="position:relative;">
					<a href="/[!Systeme::CurrentMenu::Url!]/Categorie/[!Cat::Url!]/Reference/[!Ref::Url!]" title="[!Ref::Titre!]" [IF [!Rec::Id!]=[!Ref::Id!]] style="font-weight:bold;"[/IF]>- [!Ref::Titre!][IF [!Ref::DateSortie!]>[![!TMS::Now!]:-25920000!]] &nbsp; <img src="/Skins/Expressiv/Img/References/BoutonNew.png" alt="nouvelles r&eacute;f&eacute;rences"/>[/IF]</a> 
				</div>
			[/STORPROC]
		[/IF]
	[/STORPROC]<br />
	//[MODULE Portfolio/Clients/Logo]
</div>