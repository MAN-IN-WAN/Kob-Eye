[IF [!Module!]=]
    [!Module:=[!Module::Actuel::Nom!]!]
[/IF]
[IF [!Chemin!]=]
    [!Chemin:=[!Lien!]!]
[/IF]

<span style="color:white">Accueil</span>
<a href="/[!Module!]" style="color:#fff;text-decoration:underline;"> [!Module!]</a>
[INFO [!Chemin!]|Te]
[!Req:=[!Te::Module!]!]

[!GotAlready:=False!]
[!SaveInterface:=!]
[STORPROC [!Te::Historique!]|His|0|100|Id|DESC]
    [!Display:=True!]
    [IF [!His::Type!]=Erreur||[!His::Type!]=]
	[IF [!GotAlready!]!=True]
	    [!Display:=False!]
	[/IF]
    [/IF]
    [IF [!Display!]==True]
	[!GotAlready:=True!]
	[!Req+=/[!His::DataSource!]!]
	[IF [!His::Type!]=&&[!His::Value!]!=&&[!SwitchConfig!]!=True]
	[ELSE]
	    <span class="Separator">&gt;</span>
	    <span style="color:white">Liste</span>
	    <a href="/[!Req!]" style="color:#fff;text-decoration:underline">[!His::DataSource!]</a> 
	[/IF]
	[IF [!SwitchConfig!]=True&&[!His::Value!]=]
	    <span class="Separator">&gt;</span>
	    <span style="color:white">Liste</span>
	    <a href="/[!Req!]" style="color:#fff;text-decoration:underline">Configuration:Options</a> 	    
	[/IF]
	[IF [!His::Type!]=Direct||[!His::Type!]=Interface||[!His::Type!]=Child||[!His::Value!]]
	    [IF [!His::Value!]] 
	    	
	       		[!Req+=/[!His::Value!]!]
			[IF [!His::DataSource!]!=Configuration] // Cas particulier Config
		<span class="Separator">&gt;</span>
		[STORPROC [!Req!]|Nav|0|1]
		    //<span style="color:white;">Infos</span>
		    <a href="/[!Req!]" style="color:#fff;text-decoration:underline;">[!Nav::ObjectType!]:[!Nav::getFirstSearchOrder!]</a> 
		[/STORPROC]
		[/IF]
	    [/IF]
	    //Cas particulier Config
	    [IF [!His::DataSource!]=Configuration]
	    	[!SwitchConfig:=True!]
	    [ELSE]
		[!SwitchConfig:=False!]
	    [/IF]
	    [IF [!His::Type!]=Interface]
		[!Interface:=[!His::Interface!]!]
		[!SaveInterface:=True!]
	    [/IF]    
	[/IF]
	[IF [!SaveInterface!]=True&&[!Pos!]=[!NbResult!]]
	    <span class="Separator">&gt;</span>
	    <span style="color:white;font-weight:bolder;">[!Interface::Interface!]&nbsp;[IF [!Interface::Interface!]!=Modifier] [!Interface::DataSource!] [/IF]</span>&nbsp;
	[/IF]
    [/IF]
[/STORPROC]

