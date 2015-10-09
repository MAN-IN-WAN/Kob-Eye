[MODULE Systeme/Configuration/Top]
[BLOC Panneau]


[OBJ Systeme|Page|Sitemap]
[STORPROC [!Query!]|Site|0|1]

	
	<h1>GENERATION DES MOTS CLEFS</h1>
	<ul>
	[STORPROC [!Systeme::Modules!]|Mod]
	    <li style="color:red">-- [!Mod::Nom!]
		<ul>
		[STORPROC [!Module::[!Key!]::Db::ObjectClass!]/browseable=1|O]
		    <li style="color:blue">----- [!O::titre!]
		    <ul>
		    [STORPROC [!Mod::Nom!]/[!O::titre!]/*|P|0|100000]
			[!P::Save()!]
			<li style="color:green">------[!P::Module!] / [!P::ObjectType!] / [!P::getFirstSearchOrder()!] ( [!P::Id!] )        [ OK ]</li>
		    [/STORPROC]
		    </ul></li>
		[/STORPROC]
		</ul>
	</li>
	[/STORPROC]
	</ul>
[/STORPROC]

[/BLOC]
[MODULE Systeme/Configuration/Bottom]