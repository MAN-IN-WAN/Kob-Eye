/////////////////// Filtres recherche
[!Filtres:=Departement=[!Departement!]&amp;Budget=[!Budget!]&amp;Ville=[!Ville!]&amp;Fiscalite=[!Fiscalite!]!]
[STORPROC [!Type!]|T][!Filtres+=&amp;Type[]=[!T!]!][/STORPROC]
/////////////////// Pagination
[IF [!Page!]=][!Page:=1!][/IF]
[IF [!Affichage!]!=Programmes][!NbParPage:=5!][ELSE][!NbParPage:=3!][/IF]
[!LimitStart:=[![!Page:-1!]:*[!NbParPage!]!]!]
// on est en admin commercial et on met à jour un lot
[!LAGestion:=0!]
[IF [!Systeme::User::Login!]=CommercialAdm]
	[!LAGestion:=1!]
	[IF [!Envoi!]=EnvoiForm]
		[!ActionneurLu:=[!Actionneur!]!] [!ActionnMaj:=[!ActionPrescripteur!]!] [!ActionLotLu:=[!LotValider!]!]
		[IF [!ActionneurLu!]&&[!ActionnMaj!]&&[!ActionLotLu!]]
  			//mise à jour à faire
		 	[STORPROC Systeme/User/[!ActionneurLu!]|PrUser|0|1]
		 		[STORPROC ParcImmobilier/Lot/[!ActionLotLu!]/Action|ActP|0|1|tmsCreate|DESC]
		    		//supprimer action de ce lot
					[!ActP::Delete()!]
			 	[/STORPROC]
			[/STORPROC]
	 		// Créer la nouvelle action
			[!LeStatut:=1!]
			[IF [!ActionnMaj!]=Optionner][!LeStatut:=2!][/IF]
			[IF [!ActionnMaj!]=Reserver][!LeStatut:=3!][/IF]
			[IF [!ActionnMaj!]=Vendu][!LeStatut:=4!][/IF]
			[IF [!LeStatut!]!=1]
		 		[OBJ ParcImmobilier|Action|ActPc]
		 		[METHOD ActPc|Set][PARAM]Type[/PARAM][PARAM][!ActionnMaj!][/PARAM][/METHOD]
				[METHOD ActPc|AddParent][PARAM]ParcImmobilier/Lot/[!ActionLotLu!][/PARAM][/METHOD]
				[METHOD ActPc|AddParent][PARAM]Systeme/User/[!ActionneurLu!][/PARAM][/METHOD]
				[METHOD ActPc|Save][/METHOD]
				
			[/IF]
			// Mettre à jour le lot
			[STORPROC ParcImmobilier/Lot/[!ActionLotLu!]|LotAct|0|1][/STORPROC]
			[METHOD LotAct|Set][PARAM]Statut[/PARAM][PARAM][!LeStatut!][/PARAM][/METHOD]
			[METHOD LotAct|Save][/METHOD]
		[/IF]
	[/IF]
[/IF]

// enlever l'option sur un lot
[IF [!DesAction!]=Optionner&&[!LAction!]]
	// modification du STATUT DU lot 
	[STORPROC ParcImmobilier/Lot/[!LotId!]|StLot|0|1][/STORPROC]
	[METHOD StLot|Set][PARAM]Statut[/PARAM][PARAM]1[/PARAM][/METHOD]
	[METHOD StLot|Save][/METHOD]
	// suppression de l'action concernée 
	[STORPROC ParcImmobilier/Action/[!LAction!]|Act|0|1]
   		[!Act::Delete()!]
	[/STORPROC]

	// envoi du mail de désoption
	[MODULE ParcImmobilier/Mail?Type=Desoptionner&LeLot=[!LotId!]]

[/IF]
[IF [!Action!]!=]
	/////////////////// On a demandé une réservation ou une option
	
	// il faut vérfier que le lot est toujours disponible pour l'action qu'on veut entreprendre
	// on doit vérifier qu'entre temps une réservation ou un option n'a pas été posée par un autre prescripteur
	
	[OBJ ParcImmobilier|Action|Act]
	[METHOD Act|Set][PARAM]Type[/PARAM][PARAM][!Action!][/PARAM][/METHOD]
	[METHOD Act|AddParent][PARAM]ParcImmobilier/Lot/[!LotId!][/PARAM][/METHOD]
	[METHOD Act|AddParent][PARAM]Systeme/User/[!Systeme::User::Id!][/PARAM][/METHOD]
	[METHOD Act|Save][/METHOD]
	// mettre à jour le lot
	[STORPROC ParcImmobilier/Lot/[!LotId!]|UpdLot|0|1][/STORPROC]
	[!LeStatut:=[!UpdLot::Statut!]!]
	[IF [!Action!]=Optionner][!LeStatut:=2!][/IF]
	[IF [!Action!]=Reserver][!LeStatut:=3!][/IF]
	[IF [!LeStatut!]=[!UpdLot::Statut!]]
		Erreur ce lot a été [!Action!] entre temps !<br /><br />
		<a href="/[!Lien!]?[!Filtres!]">Revenir à la liste</a>
		// envoi du mail de réservation
		[MODULE ParcImmobilier/Mail?Type=Erreur[!Action!]&LeLot=[!LotId!]]
	[ELSE]
		[METHOD UpdLot|Set]
			[PARAM]Statut[/PARAM][PARAM][!LeStatut!][/PARAM]
		[/METHOD]
		[METHOD UpdLot|Save][/METHOD]
		Votre demande pour <span style="text-transform:lowercase">[!Action!]</span> ce lot a bien été prise en compte.<br /><br />
		<a href="/[!Lien!]?[!Filtres!]">Revenir à la liste</a>
		// envoi du mail de réservation
		[MODULE ParcImmobilier/Mail?Type=[!Action!]&LeLot=[!LotId!]]
	[/IF]
[ELSE]
	/////////////////// Affichage standard
	[IF [!Reference!]]
		<h1>Détail du programme</h1>
		<div> [MODULE ParcImmobilier/Residence/DetailProgramme]</div>
		<div class="RetourListe"><a href="[!SERVER::HTTP_REFERER!]" >Retour</a></div>
	[ELSE]
		[IF [!Lot!]]
			<h1>Détail du lot</h1>
			<div class="borderb">[MODULE ParcImmobilier/Residence/DetailLot?OngletLot=LotDesc]</div>
			<div class="RetourListe"><a href="[!SERVER::HTTP_REFERER!]" >Retour</a></div>
		[ELSE]
			[IF [!ResidenceLot!]][!Filtres+=&amp;ResidenceLot=[!ResidenceLot!]!][/IF]
			[IF [!FiltreActions!]] [!Filtres+=&amp;FiltreActions=[!FiltreActions!]!][/IF]
		//	[IF [!Affichage!]=Programmes||[!Affichage!]=]
		//		[IF [!FiltreActions!]!=Departement=&Budget=&Ville=&Fiscalite=&&[!ResidenceLot!]!=][!Affichage:=Lots!][/IF]
		//	[/IF]
	
			<h1>Résultats de votre recherche</h1>
			<div class="Tabs">
				<div class="Tab [IF [!Affichage!]=Lots||[!LAGestion!]=1]TabActive[/IF]">
					<a href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?Affichage=Lots&amp;[!Filtres!]">Affichage par lots</a>
				</div>
				[IF [!Systeme::User::Login!]!=CommercialAdm]
					<div class="Tab [IF [!Affichage!]=Programmes||[!Affichage!]=]TabActive[/IF]">
						<a href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?Affichage=Programmes&amp;[!Filtres!]">
							Affichage par programmes
						</a>
					</div>
				[/IF]
				<div class="Tab">
					<a href="[!Domaine!]/[!Lien!]/PdfGrillePrix?Affichage=Imp&amp;[!Filtres!]" target="_blank" rel="link">
						Imprimer la grille des prix
					</a>
				</div>
			</div>
			[IF [!Systeme::User::Login!]!=CommercialAdm]
				[IF [!Affichage!]=Programmes||[!Affichage!]=]
					[MODULE ParcImmobilier/Residence/Programmes]
				[ELSE]
					[MODULE ParcImmobilier/Residence/Lots]
				[/IF]
			[ELSE]
				//[!Complete:=!]
				//[IF [!Filtres!]!=][!Complete:=?ResidenceLot=[!R::Id!][!Filtres!]!][/IF]
				[MODULE ParcImmobilier/Residence/LotsAdmin[!Complete!]]
			[/IF]
		[/IF]
	[/IF]
[/IF]