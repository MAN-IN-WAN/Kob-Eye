//[IF [!SERVER::REMOTE_ADDR!]=192.168.1.74||[!SERVER::REMOTE_ADDR!]=178.22.145.106]
	//178.22.145.106]
	[IF [!Systeme::User::Login!]!=CommercialAdm]
		[COUNT Systeme/User/[!Systeme::User::Id!]/Denonciation/Obsolete=0|Total]
		[!Requete:=Systeme/User/[!Systeme::User::Id!]/Denonciation/Obsolete=0!]
		[!ADMINVUE:=0!]
	[ELSE]
		[!ADMINVUE:=1!]
		[COUNT ParcImmobilier/Denonciation/Obsolete=0|Total]
		[!Requete:=ParcImmobilier/Denonciation/Obsolete=0!]
		
	[/IF]
	
	[!NbPages:=[!Total:/[!NbParPage!]!]!]
	<div class="Denonciation">
		<h1>Liste de vos dénonciations</h1>
		[IF [!ADMINVUE!]=0]<div class="CreationDenonciation"><a class="BlocButton" href="/Denonciations?Affichage=Saisie"></a></div>[/IF]
		<table class="ListeDenonciations">
		<tr>
			<th>Date</th>
			<th>Contact</th>
			<th>Autres</th>
		</tr>
	
		[STORPROC [!Requete!]|Den|[!LimitStart!]|[!NbParPage!]|tmsCreate|DESC]
			<tr>
				<td>[DATE d/m/Y H:00][!Den::tmsCreate!][/DATE]</td>
				<td>
					[!Den::Prenom!] [!Den::Nom!]<br />
					[IF [!Den::Telephone1!]!=]Téléphone : [!Den::Telephone1!][/IF]<br />
					[IF [!Den::Mail!]!=]Mail : [!Den::Mail!][/IF]
					</td>
				<td>
					[!Den::AutreRenseignement!]
				</td>
			</tr>
			[NORESULT]
				<tr><td colspan="4" class="noresult">Aucune dénonciation </td></tr>
			[/NORESULT]
			[/STORPROC]
		</table>
		////////// Affichage Pagination //////////
		[IF [!NbPages!]>1]
		[!Next:=[!Page!]!]
		[!Next+=1!]
		<div class="Pagination">
			<div class="PaginationBody">
			<a class="PagiFirst" href="/[!Lien!]?[!Filtres!]">&nbsp;</a>
			<a class="PagiPrev" href="/[!Lien!]?[!Filtres!][IF [!Prev!]>1]&amp;Page=[!Prev!][/IF]">&nbsp;</a>
			[STORPROC [!NbPages!]|P]
				[IF [!Pos!]=[!Page!]]<strong>[/IF]
				<a href="/[!Lien!]?[!Filtres!][IF [!Pos!]>1]&amp;Page=[!Pos!][/IF]" [IF [!Pos!]=[!Page!]] class="bleu" [/IF]>[!Pos!]</a>
				[IF [!Pos!]=[!Page!]]</strong>[/IF]
			[/STORPROC]
			<a class="PagiNext" href="/[!Lien!]?[!Filtres!]&amp;Page=[!Next!]">&nbsp;</a>
			<a class="PagiLast" href="/[!Lien!]?[!Filtres!]&amp;Page=[!NbPages!]">&nbsp;</a>
			</div>
		</div>
		[/IF]
	</div>
//[ELSE]
//	[MODULE ParcImmobilier/Denonciation/ListeOld]
//[/IF]