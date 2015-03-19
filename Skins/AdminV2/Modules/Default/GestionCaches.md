[MODULE Systeme/Configuration/Top]
[SWITCH [!Cache!]|=]
    [CASE User]
	[BLOC Info|Régénération du User Cache]
	    Les fichiers supprim&eacute;s sont :
	    [!Told:=False!]
	    [STORPROC Systeme/User|U|0|100000]
		[STORPROC [!CONF::GENERAL::LANGUAGE!]|L]
			[STORPROC Explorateur/_Dossier/Home/[!U::Id!]/_Fichier/.UserCache.[!L::TITLE!]|F]
				[!Told:=True!]
				[!F::Url!] ~ 
				[METHOD F|Delete][/METHOD]
			[/STORPROC]
		[/STORPROC]
	    [/STORPROC]
	    [IF [!Told!]=False]
		Aucun fichier supprim&eacute;
	    [/IF]
	[/BLOC]
    [/CASE]
    [CASE Schema]
	[BLOC Info|Régénération du Schema Cache]
	    Les fichiers supprim&eacute;s sont :
	    [!Told:=False!]
	    [STORPROC [!Systeme::Modules!]|Mod]
		[STORPROC Explorateur/_Dossier/Modules/[!Mod::Nom!]/_Fichier/.Db.cache|F]
		    [!Told:=True!]
		    [!F::Url!] ~ 
		    [METHOD F|Delete][/METHOD]
		    [NORESULT]
		    [/NORESULT]
		[/STORPROC]
	    [/STORPROC]
	    [IF [!Told!]=False]
		Aucun fichier supprim&eacute;
	    [/IF]
	[/BLOC]
    [/CASE]
    [CASE Module]
	[BLOC Info|Régénération du Module Cache]
	    Les fichiers supprim&eacute;s sont :
	    [!Told:=False!]
	    [STORPROC Systeme/User|U|0|100000|Id|ASC|m.Id]
		[STORPROC Explorateur/_Dossier/Home/[!U::Id!]/.cache/_Fichier|F]
		    [!Told:=True!]
		    [!F::Url!] ~ 
		    [METHOD F|Delete][/METHOD]
		    [NORESULT]
		    [/NORESULT]
		[/STORPROC]
	    [/STORPROC]
	    [IF [!Told!]=False]
		Aucun fichier supprim&eacute;
	    [/IF]
	[/BLOC]
    [/CASE]
    [CASE Skin]
	[BLOC Info|Régénération des Skins Cache]
	    Les fichiers supprim&eacute;s sont :
	    [!Told:=False!]
	    [STORPROC Explorateur/_Dossier/Skins/_Dossier|S]
			[STORPROC Explorateur/_Dossier/Skins/[!S::Nom!]/.cache/_Fichier|F]
				[!Told:=True!]
				[!F::Url!] ~ 
				[METHOD F|Delete][/METHOD]
			[/STORPROC]
			[STORPROC Explorateur/_Dossier/Skins/[!S::Nom!]/.cache|D]
		    		[METHOD D|Delete][/METHOD]
			[/STORPROC]
	    [/STORPROC]
	    [IF [!Told!]=False]
		Aucun fichier supprim&eacute;
	    [/IF]
	[/BLOC]
    [/CASE]
[/SWITCH]

    <h1>Cache des utilisateurs</h1>
    <a href="/[!Lien!]?Cache=User">Reg&eacute;n&eacute;rer le cache </a>
    <h1>Cache des modules</h1>
    <a href="/[!Lien!]?Cache=Module">Reg&eacute;n&eacute;rer le cache </a>
    <h1>Cache des sch&eacute;mas </h1>
    <a href="/[!Lien!]?Cache=Schema">Reg&eacute;n&eacute;rer le cache </a>
    <h1>Skins Caches</h1>
    <a href="/[!Lien!]?Cache=Skin">Reg&eacute;n&eacute;rer le cache </a>

[MODULE Systeme/Configuration/Bottom]

