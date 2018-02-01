[IF [!Systeme::User::Public!]]
	<div style="display:block;width:100%;" >
		<div id="Entete" style="display:block;margin:20px auto auto auto;width:980px;">
			<a href="/" title="Revenir à la page d'accueil">
				<img src="/Skins/Intranet/Img/bando-mail.jpg" width="980" height="80" 
				alt="export" title="export" />
			</a>
		</div>
		<div id="Contenu" style="display:block;margin:20px auto auto auto;width:980px;">
			[MODULE Systeme/Login?Redirect=/Newsletter/Export]
		</div>
	</div>
[ELSE]
	[IF [!start!]!=]
		// Production CSV
		[OBJ Newsletter|Contact|Ct]
		[!Ct::sendHeader()!]
	 	[!TotGene:=0!]
	 	[!FILTRE:=!]
	 	[IF [!Groupe!]!=0]
	 		[!FILTRE:=/[!Groupe!]!]
	 	[/IF]
	 	[!Titre:=Export du [!Utils::getDate(d.m.Y,[!start!])!]!]
	 	[!Titre+= au [!Utils::getDate(d.m.Y,[!stop!])!]!]
	 	[!Ct::addTitre([!Titre!])!] 
		[STORPROC Newsletter/GroupeEnvoi[!FILTRE!]|Gr]
			[IF [!Gr::Id!]!=17]
				[STORPROC Newsletter/GroupeEnvoi/[!Gr::Id!]/Contact/tmsCreate>=[!start!]&tmsCreate<=[!stop!]|C|||Campagne|DESC]
					[!Ct::addContact([!C!],[!Gr::Titre!])!] 
				[/STORPROC]
				[COUNT Newsletter/GroupeEnvoi/[!Gr::Id!]/Contact/tmsCreate>=[!start!]&tmsCreate<=[!stop!]|Tot] 
				[!Ct::addTotal([!Tot!],[!Gr::Titre!])!]
				[!TotGene+=[!Tot!]!]
			[/IF]
		[/STORPROC]
		
		[IF [!FILTRE]=]
			// Gestion des demandes de rappels		
			[STORPROC Newsletter/GroupeEnvoi/17|Gr]
				[COUNT Newsletter/GroupeEnvoi/17/Contact/*/Reception/tmsCreate>=[!start!]&tmsCreate<=[!stop!]|Tot]
				[!Ct::addRappel([!Tot!],[!Gr::Titre!])!]
				[!TotGene+=[!Tot!]!]
			[/STORPROC] 
		[/IF]
		[!Ct::addTotal([!TotGene!])!]
		Fin Export
	[ELSE]
		<form action="/[!Lien!].htm" method="post" class="Export" style="margin-bottom:20px;padding:80px 0 0 0; width:980px;">
			<div style="margin:10px 0;"><a href="/Systeme/Deconnexion">Se déconnecter</a></div>
			De <select name="start">
				[!Annee:=2010!]
				[STORPROC 5|An]
					[!Annee+=1!]
					[STORPROC 12|Mois]
						[!Startselect:=[!Utils::getTms(1/[!Mois:+1!]/[!Annee!])!]!]
						<option value="[!Startselect!]" [IF [!start:]=[!Startselect!]] selected[/IF]>[UTIL MONTH][!Mois:+1!][/UTIL] [!Annee!]</option>
					[/STORPROC]
				[/STORPROC]
			</select>
			à <select name="stop">
				[!Annee:=2010!]
				[STORPROC 5|An]
					[!Annee+=1!]
					[STORPROC 12|Mois]
						[!Stopselect:=[!Utils::getTms(1/[!Mois:+2!]/[!Annee!])!]!]
						<option value="[!Stopselect!]" [IF [!stop:]=[!Stopselect!]] selected[/IF] >[UTIL MONTH][!Mois:+1!][/UTIL] [!Annee!]</option>
					[/STORPROC]
				[/STORPROC]
			</select> (inclus)
			Choix du groupe <select name="Groupe">
				<option value="0" [IF [!Groupe:]=0] selected[/IF] >Tous</option>
				[STORPROC Newsletter/GroupeEnvoi/Id>0|Grp]
					<option value="[!Grp::Id!]" [IF [!Groupe:]=[!Grp::Id!]"] selected[/IF] >[!Grp::Titre!]</option>
				[/STORPROC]
			</select>
			<button type="submit">OK</button>
		</form>
	[/IF]
[/IF]

		
		
