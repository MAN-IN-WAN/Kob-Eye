[MODULE Systeme/Interfaces/FilAriane]

<div id="Container">
    [MODULE Systeme/Interfaces/BarreModule]
    //[MODULE Systeme/Interfaces/BarreAction?Modifier=1]
    //Liste des blocs icones
    [BLOC Panneau|top:50px;||overflow:auto;]
	<h1>Mise &agrave; jour</h1>
	<ul>
	[STORPROC [!CONF::MODULE!]|Mod]
		<li>    [!Key!] :
			[IF [!Module::[!Mod::NAME!]::Db::Check!]=1]
				La mise &agrave; jour  du module [!Mod::NAME!] s'est bien pass&eacute;e
			[ELSE]
				Une erreur a eu lieu lors de la mise du module [!Mod::NAME!] &agrave; jour.
			[/IF]
		</li>
	[/STORPROC]
	</ul>

    [/BLOC]
</div>
