[IF [!Systeme::User::Login!]!=CommercialAdm]
	[COUNT Systeme/User/[!Systeme::User::Id!]/Denonciation|Total]
	[!Requete:=Systeme/User/[!Systeme::User::Id!]/Denonciation!]
	[!ADMINVUE:=0!]
[ELSE]
	[!ADMINVUE:=1!]
	[COUNT ParcImmobilier/Denonciation|Total]
	[!Requete:=ParcImmobilier/Denonciation!]
	
[/IF]

[!NbPages:=[!Total:/[!NbParPage!]!]!]
<div class="Denonciation">
	<h1>Liste de vos dénonciations</h1>
	[IF [!ADMINVUE!]=0]<div class="CreationDenonciation"><a class="BlocButton" href="/Denonciations?Affichage=Saisie"></a></div>[/IF]
//	[IF [!ADMINVUE!]=1]<div class="ExportDenonciation"><a class="BlocButton" href="/Denonciations/Export" target="_blank">Exporter les dénonciations</a></div>[/IF]
	<table class="ListeDenonciations">
	    <tr>
	        <th>Date</th>
	        <th>Contact</th>
	        <th>Détails</th>
	        <th>Autres</th>
	    </tr>
	 
	    [STORPROC [!Requete!]|Den|[!LimitStart!]|[!NbParPage!]|tmsCreate|DESC]
	            <tr>
		            <td>[DATE d/m/Y H:00][!Den::tmsCreate!][/DATE]</td>
		            <td>
		               	[!Den::Civilite!] [!Den::Prenom!] [!Den::Nom!]<br />
		            	[!Den::Adresse1!] <br />
		            	[!Den::Adresse2!] <br />
		            	[!Den::CodePostal!] [!Den::Ville!]<br />
				        Téléphones : [!Den::Telephone1!] [IF [!Den::Telephone2!]!=] - [!Den::Telephone2!][/IF][IF [!Den::Telephone3!]!=] - [!Den::Telephone3!][/IF]<br />
				        [IF [!Den::Fax!]!=]Fax : [!Den::Fax!] [/IF][IF [!Den::Mail!]!=]Mail : [!Den::Mail!][/IF]
			        </td>
		            <td>
		            	Types recherchés : [!Den::TypeLot!] <br />
		            	Surface : [!Den::Surface!] <br />
		            	Investissement envisagé : [!Den::Budget!] <br />
				        Situation : [!Den::VilleRecherche!] [!Den::Quartier!] [!Den::Residence!]<br />
				        Motifs : [!Den::Motifs!] <br />
				        Date Livraison souhaitée : [!Den::Livraison!] <br />
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