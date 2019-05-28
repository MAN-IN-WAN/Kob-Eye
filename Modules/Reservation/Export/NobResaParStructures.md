[IF [!Systeme::User::Public!]!=1||[!Ab!]=23686]
	[IF [!csv!]=1]
		[OBJ Reservation|Client|Cli]
		[!Cli::sendHeader()!]
		[!Cli::addLigne(;Export Somme des Réservations par Structures sociales du 01/01/2015 au 31/12/2015;)!]
		[!Cli::addLigne(Id;Nom;Type;Ville;Utilisateur;Réservations)!]

		[STORPROC Reservation/Client|C]
			[!Ligne:=!]
			[!Ligne+=[!C::Id!];!]
			[!Ligne+=[!C::Nom!];!]
			[!Ligne+=[!C::TypeStructure!];!]
			[!Ligne+=[!C::Ville!];!]
			[!Ligne+=[!C::NumeroGroupe!] - !]
			[STORPROC Systeme/Group/[!C::NumeroGroupe!]|Grp|0|1]
				[!Ligne+=[!Grp::Titre!] : !]
				[STORPROC Systeme/Group/[!Grp::Id!]/User|Us]
					[!Ligne+= [!Us::Nom!] - [!Us::Mail!]<br>!]
				[/STORPROC]
			[/STORPROC]
			[!CPT:=0!]
			[STORPROC Reservation/Client/[!C::Id!]/Reservations|RR]
				[STORPROC Reservation/Evenement/Reservations/[!RR::Id!]|Ev]
					// 01/01/15 00:00:00 au 31/12/15 23:59:59
					[IF [!Ev::DateDebut!]>=1420066800&&[!Ev::DateFin!]<=1451602799]
						[!CPT+=[!RR::NbPlace!]!]
					[/IF]
				[/STORPROC]
				[!CPT+=[!NbRRP!]!]
			[/STORPROC]
			[!Ligne+=;[!CPT!]!]
			[!Cli::addLigne([!Ligne!])!]
		[/STORPROC]
	[ELSE]
		<form action="/[!Lien!].htm" method="post" class="Export" style="margin-bottom:20px;padding:80px; width:980px;">
			<div style="padding:10px ;font-weight:bold;font-size:14px;color:#0070ba;text-decoration:underline;font-variant:small-caps;">Exports des sommes de réservations de l'année par structures</div>
			</div>
				<input type="hidden" name="csv" value="1">
				<input type="hidden" name="Ab" value="23686">
				 Exporter les contacts <button type="submit">OK</button>
			</div>
		</form>
		
		
	[/IF]
[ELSE]

		Vous n'avez pas accès à cette fonction   
		//[REDIRECT][/REDIRECT] 

[/IF]