//On affiche l explorateur
[STORPROC [![!Prop::query!]:/::!]|Q|0|1][/STORPROC]
[STORPROC [![!Prop::query!]:/::!]|OutVar|1|1][/STORPROC]
[INFO [!Q!]|Test]
[IF [!Prop::module!]][![!Prefixe!]Explorer_Module:=[!Prop::module!]!][/IF]
[BLOC Explorer|EXPLORER [!Test::TypeChild!] - [![!Prefixe!][!Prop::Nom!]_explore!]|[!Prefixe!][!Prop::Nom!]_explore]
	<input type="hidden" name="[!Prefixe!][!Prop::Nom!]_explore" value="[![!Prefixe!][!Prop::Nom!]_explore!]" />
	//Dans le cas d une requete bien definie
	[IF [!Test::Reflexive!]]
		//[IF [!PrefixeVar!]=][!PrefixeVar:=[!Test::Module!]/[!Test::TypeChild!]/!][/IF]
		[MODULE Systeme/Interfaces/Arborescence?Prefixe=[!Prefixe!]&Chemin=[!Q!]&NbChamp=4&TypeEnf=[!Prop::Nom!]&Inter=radio&Var=[!Prefixe!][!Prop::Nom!]&PrefixeVar=[!PrefixeVar!]]
	[ELSE]
		[MODULE Systeme/Interfaces/Liste?Chemin=[!Q!]&Inter=radio&Type=Select&Prefixe=[!Prefixe!]&Var=[!Prefixe!][!Prop::Nom!]&Top=10&RechPrefixe=Explore&OutVar=[!OutVar!]]
	[/IF]
[/BLOC]
	

