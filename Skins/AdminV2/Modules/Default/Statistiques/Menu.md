<div style="width: 100%; padding: 0px;" >
	[BLOC Rounded||width:100%;float:left;overflow:hidden;height:auto;]
		[BLOC Rounded|background:#3D3D3D;color:#FFFFFF;|margin-bottom:5px;]
			<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
			<span style="margin-left:5px;">Menu</span>
		[/BLOC]
		<ul>
			[STATS [!Module::Actuel::Nom!]|V1]
			[!Niveau:=0!]
			[INFO [!Lien!]|I]
			[STORPROC [!I::Historique!]|H1|2|1][/STORPROC]
			[STORPROC [!I::Historique!]|H2|3|1][/STORPROC]
			[STORPROC [!V1!]|Tr1]
				<li><a [IF [!H1::Value!]=[!Tr1::Name!]]style="font-weight:bold;"[/IF] href="/[!Module::Actuel::Nom!]/Statistiques?DateDebut=[!DateDebut!]&DateFin=[!DateFin!]">[!Tr1::Name!]</a>
					<ul>
						[STATS [!Module::Actuel::Nom!]/[!Tr1::Name!]|V2]
						[STORPROC [!V2!]|Tr2]
							<li><a [IF [!H2::Value!]=[!Tr2::Name!]]style="font-weight:bold;"[/IF] href="/[!Module::Actuel::Nom!]/Statistiques/[!Tr1::Name!]/[!Tr2::Name!]?DateDebut=[!DateDebut!]&DateFin=[!DateFin!]">[!Tr2::Name!]</a>
							//[MODULE Systeme/Statistiques/Menu?Requete=[!Requete!]/[!Tr::Name!]&Niveau=[!Niveau:+1!]&DateDebut=[!DateDebut!]&DateFin=[!DateFin!]]
							</li>
						[/STORPROC]
					</ul>
				</li>
			[/STORPROC]
		</ul>
	[/BLOC]
</div>

