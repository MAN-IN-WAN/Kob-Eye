[IF [!SERVER::REMOTE_ADDR!]=185.71.149.9||[!Ab!]=23686]
	[IF [!csv!]=1]
		// on fait un objet sur client car les functions sont dans la class client
		[OBJ Reservation|Client|Cli]
		[!Cli::sendHeader()!]

		// on veut la liste des organisations culturelles et sportives
		[!Requete:=Reservation/Client!]
		[IF [!CodePos!]!=]
			[!Requete+=/CodPos=[!CodPos!]!]
		[/IF]
		[IF [!Ville!]!=]
			[IF [!CodePos!]!=][!Requete+=&!][ELSE][!Requete+=/!][/IF]
			[!Requete+=Ville=[!Ville!]!]
		[/IF]

		[!Cli::addLigne(Export liste des Structures sociales)!]
		[!LigneEntete:=Date création;Type;Nom;Adresse;Téléphone;Fax;Email!]
		[!Cli::addLigne([!LigneEntete!])!]
		[STORPROC [!Requete!]|Req]
			[!AddOk:=0!][!Select:=0!][!Cpt:=0!]
			[STORPROC [!Actives!]|Actv]
				[IF [!Actv!][!AddOk+=1!][/IF]
			[/STORPROC]
			[IF [!AddOk!]<2]
				// il faut vérifier en fonction du filtre saisi actif inactif
				[!DateDebut:=[!Org::renvoietime(01,01)!]!]
				[!DateFin:=[!Org::renvoietime(31,12)!]!]
				[COUNT [!Requete!]/|!Req::Id!]/Reservations/tmsCreate>=[!DateDebut!]&tmsCreate<=[!DateFin!]|NbReqF]
				[IF [!Actv!]=1]
					[IF [!NbReqF!]][!Select:=1!][ELSE][!Select:=0!][/IF]
				[ELSE]
					[IF [!NbReqF!]][!Select:=2!][ELSE][!Select:=1!][/IF]
				[/IF]
			[ELSE]
				[!Select:=1!]
			[/IF]
			[IF [!Select!]=1]
				[!Ligne:=[!Utils::getDate(d-m-Y,[!Req::tmsCreate!])!];!]
				[!Ligne+=[!Req::TypeStructure!];!]
				[!Ligne+=[!Req::Nom!];!]
				[!Ligne+=[!Req::Adresse!]-[!Req::CodPos!] [!Req::Ville!];!]
				[!Ligne+=[!Req::Tel!];!]
				[!Ligne+=[!Req::Fax!];!]
				[!Ligne+=[!Req::Mail!];!]
//				[STORPROC Systeme/Groupe/[!NumeroGroupe!]|Grp|0|1]
//					[STORPROC Systeme/Groupe/[!Grp::Id!]/User|Us]
//						[!Ligne+=[!Grp::Id!]-[!Grp::Titre!] : [!Us::Nom!] - [!Us::Mail!]<br />!]
//					[/STORPROC]
//				[/STORPROC]
				[!Cli::addLigne([!Ligne!])!]
				
			[/IF]
					
		[/STORPROC]
		[!Ligne:=Nombre de structures :;;;!]
		[!Ligne+=[!Cpt!];!]
		[!Cli::addLigne([!Ligne!])!]	
	[ELSE]
		<form action="/[!Lien!].htm" method="post" class="Export" style="margin-bottom:20px;padding:80px; width:980px;">
			<div style="padding:10px ;font-weight:bold;font-size:14px;color:#0070ba;text-decoration:underline;font-variant:small-caps;">Exports des structures</div>
			<div style="overflox:hidden;margin:20px 0;">
				<h2>Choisissez les structures à exporter</h2>
				[STORPROC Systeme/Groupe/2/Groupe|GrpS]
					<input type="checkbox" value="[!GrpS::Id!]" [IF [!Structures!]=[!GrpS::Id!]] selected[/IF] name="Structures[]" >[!GrpS::Titre!]<br />
				[/STORPROC]
			</div>
			<div style="overflox:hidden;margin:20px 0;">
				<h2>Cochez ce que vous voulez</h2>
				<input type="checkbox" value="1" name="Actives[]" >Qui ont réservé dans l'année<br />
				<input type="checkbox" value="0" name="Actives[]" >Qui n'ont pas réservé dans l'année<br />
			</div>

			<div style="overflox:hidden;margin:20px 0;">
				<h2>Filtres</h2>
				<label>Code Postal <input type="text" name="CodePos" ></label>
				<label>Ville <input type="text" name="Ville" ></label>
			</div>

			</div>
				<input type="hidden" name="csv" value="1">
				 Exporter les contacts <button type="submit">OK</button>
			</div>
		</form>
		
		
	[/IF]
[ELSE]
	vous n'avez pas accès à cette fonctionnalité
[/IF]

