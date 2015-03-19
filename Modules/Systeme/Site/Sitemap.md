[MODULE Systeme/Configuration/Top]
[BLOC Panneau]


[OBJ Systeme|Page|Sitemap]
[STORPROC [!Query!]|Site|0|1]

	<h1>RECUPERATION DE L'UTILISATEUR</h1>
	[STORPROC Systeme/User/Site/[!Site::Id!]|U|0|1][/STORPROC]
	<h2 style="color:green">[!U::Login!]</h2>
	<h1>GENERATION DES PAGES</h1>
	<ul>
	[STORPROC [!U::getMenus()!]|P|0|10000000]
	    [!P::Save()!]
	    <li style="color:blue">-[!P::Module!] / [!P::ObjectType!] / [!P::getFirstSearchOrder()!] ( [!P::Id!] )        [ OK ]
	    <ul>
	    [STORPROC [!P::Menus!]|P2|0|10000000]
		[!P2::Save()!]
		<li style="color:green">+--[!P2::Module!] / [!P2::ObjectType!] / [!P2::getFirstSearchOrder()!] ( [!P2::Id!] )        [ OK ]</li>
	    [/STORPROC]
	     </ul></li>
	[/STORPROC]
	</ul>
	
[/STORPROC]

[/BLOC]
[MODULE Systeme/Configuration/Bottom]