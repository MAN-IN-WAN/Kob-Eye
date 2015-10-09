<a id="Deconnect" href="/Systeme/Deconnexion"></a>

<div id="WelcomeUser">Bonjour [!Systeme::User::Prenom!] [!Systeme::User::Nom!]</div>

<div id="Welcome">Bienvenue dans votre espace prescripteur</div>

[STORPROC Systeme/User/[!Systeme::User::Id!]/Commercial|CCal|0|1]
	[NORESULT]
		[STORPROC ParcImmobilier/Commercial/Referent=1|CCal|0|1][/STORPROC]
	[/NORESULT]
[/STORPROC]

<div id="BlocTopRight">
	<div id="YourContact">Votre contact</div>
	[!CCal::Prenom!] <span style="text-transform: uppercase">[!CCal::Nom!]</span><br />
	[!CCal::Fonction!]<br />
	Tel : [!CCal::Telephone!]<br />
	[!CCal::Ville!]
	<a href="/Contact?C_Sujet=Prescripteur" id="SendMailYourContact"></a>
	
</div>
