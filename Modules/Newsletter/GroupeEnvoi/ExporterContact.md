[STORPROC [!Query!]|Objet|0|1]
	[IF [!Action!]=Exporter]
		[INI memory_limit]80M[/INI]
		[INI max_execution_time]3600[/INI]	
		[!Recherche:=[!Query!]/Contact!]
                 [!SEP:=\r\n!]
		[COUNT [!Recherche!]|Nb]
		[!NbPass:=[!Nb:/1000!]!]
		[!NbPass+=1!]
                //affichage de l'entete
                //[STORPROC [!T::getElementsByAttribute(export,,1)!]|P][!P::name!][IF [!Pos!]<[!NbResult!]];[/IF][/STORPROC]
                [!Systeme::Clean()!]
                [!Systeme::setFileName([!Utils::Canonic([!Objet::Titre!])!])!]
                First Name; Last Name; E-mail Address; Home Address; Mobile Phone[STORPROC [!NbPass!]|n][STORPROC [!Recherche!]|C|[!n:*1000!]|1000]
                [!C::Prenom!];[!C::Nom!];[!C::Email!];[!C::Adresse!] [!C::CodePostal!] [!C::Ville!];[!C::Telephone!][/STORPROC][/STORPROC]
	[ELSE]
				<h1>Exportation d'un fichier</h1>

					<a rel="link" class="KEBouton" href="/[!Lien!].csv?Action=Exporter">
                                                        Télécharger le fichier</a>
	[/IF]
[/STORPROC]
