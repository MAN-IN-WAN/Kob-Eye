[STORPROC ParcImmobilier/Residence/[!Reference!]|R|0|1]
	[STORPROC ParcImmobilier/Ville/Residence/[!R::Id!]|V|0|1][/STORPROC]
	[STORPROC ParcImmobilier/Residence/[!R::Id!]/Donnee/Type=Perspective|DP|0|1][/STORPROC]
[/STORPROC]
<div class="DetailProgramme">
	<div class="ColonneDocs">
		<h2>Documents du programme</h2>
		[COUNT ParcImmobilier/Residence/[!R::Id!]/Donnee/Type=DocCommerciaux|NbDRes]
		[IF [!R::Doc!]=&&[!R::PlanSitu!]=&&[!R::PlanMasse!]=&&[!R::ContratReservation!]=&&[!R::CompromisVente!]=&&[!NbDRes]=0]
			<div class="Vide">Documents non disponibles</div>
		[ELSE]
			[IF [!R::Doc!]!=]<div class="Plaquette"><a href="/[!R::Doc!]" target="_blank" >Plaquette</a></div>[/IF]
			[IF [!R::PlanSitu!]!=]<div class="Situation"><a href="[!R::PlanSitu!]" target="_blank" >Plan de situation</a></div>[/IF]
			[IF [!R::PlanMasse!]!=]<div class="Masse"><a href="/[!R::PlanMasse!]" target="_blank" >Plan de masse</a></div>[/IF]
			[IF [!R::ContratReservation!]!=&&[!R::CompromisVente!]=]<div class="Reservation"><a href="/[!R::ContratReservation!]" target="_blank" >Contrat générique réservation</a></div>[/IF]
			[IF [!R::CompromisVente!]!=]<div class="Compromis"><a href="/[!R::CompromisVente!]" target="_blank" >Compromis de Vente</a></div>[/IF]
			[STORPROC ParcImmobilier/Residence/[!R::Id!]/Donnee/Type=DocCommerciaux|D]
				<div class="DocCommerciaux"><a href="/[!D::URL!]" target="_blank" >[!D::Titre!]</a></div>
			[/STORPROC]
		[/IF]
	</div>
	<div class="ColonneDetails">
		<div class="InfoPrincipales">
			<div class="Visuel"><img src="[!Domaine!]/[!DP::URL!].mini.210x181.jpg" alt="[!R::Titre!]" title="[!R::Titre!]" /></div>
			<div class="NomResidence">[!R::Titre!]</div>
			<div class="Ville">[SUBSTR 2][!V::CodePostal!][/SUBSTR] - [!V::Nom!]</div>
			<div class="Livraison">Livraison : [!R::DateLivraison!]</div>
			[IF [!L::IconeLoiResidence!]!=||[!L::LoiResidence!]!=]
				<div class="Fiscales">Zones Fiscales</div>
				<div class="LoiResidence">
					[IF [!R::IconeLoiResidence!]!=]
						<img src="/[!R::IconeLoiResidence!]" alt="[!R::Titre!]" title="[!R::Titre!]" /> 
					[/IF]
					[!R::LoiResidence!]
				</div>
			[/IF]

			<div class="Appartement">Lots disponibles</div>

				[STORPROC [!R::getMyTypeLot([!R::Id!],1)!]|TL]
					[!TypeAppart:=!][!S:=!]
					[IF [!TL::NbLots!]>1][!S:=s!][/IF]
					[IF [!TL::TypeLogement!]=T1][!TypeAppart:=appartement[!S!] 1 pce!] [/IF]
					[IF [!TL::TypeLogement!]=T2][!TypeAppart:=appartement[!S!] 2 pces!] [/IF]
					[IF [!TL::TypeLogement!]=T3][!TypeAppart:=appartement[!S!] 3 pces!] [/IF]
					[IF [!TL::TypeLogement!]=T4][!TypeAppart:=appartement[!S!] 4 pces!] [/IF]
					[IF [!TL::TypeLogement!]=T5][!TypeAppart:=appartement[!S!] 5 pces!] [/IF]
					[IF [!TL::TypeLogement!]=Ccial][!TypeAppart:=locaux commerciaux!] [/IF]
					[IF [!TL::TypeLogement!]=Villa][!TypeAppart:=villa[!S!]!] [/IF]
					[IF [!TL::TypeLogement!]=Studio][!TypeAppart:=studio[!S!]!] [/IF]
					[IF [!TL::NbLots!]>0]
						<div class="AppartType">
							<div class="AppartNb"><span class="Nb">[!TL::NbLots!]</span> [!TypeAppart!]</div>
							<div class="TarifAppart">
								[IF [!TL::NbLots!]=1]
									<span class="LeTarif"> 
										[IF [!TL::MaxTarif!]!=&&[!TL::MaxTarif!]!=0] 
											[!TL::MaxTarif!] €
										[ELSE]
											[!TL::TLMaxTarif!] €
										[/IF]
									</span>
								[ELSE]
									[IF [!TL::MaxTarif!]!=||[!TL::MiniTarif!]!=]
										[IF [!TL::MaxTarif!]=[!TL::MiniTarif!]]
											<span class="LibelleApartir"></span>
											<span class="LeTarif"> [IF [!TL::MaxTarif!]!=&&[!TL::MaxTarif!]!=0] [UTIL NUMBERMILLIER][!TL::MaxTarif!][/UTIL] €[/IF] </span>
										[ELSE] 
											[IF [!TL::MaxTarif!]!=[!TL::MiniTarif!]]<span class="LibelleApartir">à partir de </span> <span class="LeTarif">[IF [!TL::MiniTarif!]!=&&[!TL::MiniTarif!]!=0] [UTIL NUMBERMILLIER][!TL::MiniTarif!][/UTIL] €[/IF]</span>[/IF]
										[/IF] 
									[ELSE]
										[IF [!TL::TLMaxTarif!]=[!TL::TLMinTarif!]]
											<span class="LibelleApartir"></span>
											<span class="LeTarif"> [IF [!TL::TLMaxTarif!]!=&&[!TL::TLMaxTarif!]!=0][!TL::TLMaxTarif!] €[/IF] </span>
										[ELSE] 
											[IF [!TL::TLMaxTarif!]!=[!TL::TLMinTarif!]]<span class="LibelleApartir">à partir de </span> <span class="LeTarif">[IF [!TL::TLMinTarif!]!=&&[!TL::TLMinTarif!]!=0] [!TL::TLMinTarif!] €[/IF]</span>[/IF]
										[/IF] 
									[/IF] 
								[/IF] 
							</div>
						</div>
					[/IF]
			[/STORPROC]			
		</div>
		<div class="Pictos">
			[STORPROC ParcImmobilier/PictoResidence/Residence/[!R::Id!]|PR]
				<img src="/[!PR::Picto!]" alt="[!PR::Titre!]" title="[!PR::Titre!]" />
			[/STORPROC]
		</div>
	</div>
</div>

<div class="LotDetailOnglet">
        <div class="Tabs" style="margin-bottom:0;">
            <div class="Tab [IF [!OngletLot!]=LotDesc||[!OngletLot!]=]TabActive[/IF]">
                <a href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?Reference=[!R::Id!]&amp;OngletLot=LotDesc&amp;[!Filtres!]">
                   Description
                </a>
            </div>
            <div class="Tab [IF [!OngletLot!]=LotLots]TabActive[/IF]">
                <a href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?Reference=[!R::Id!]&amp;OngletLot=LotLots&amp;[!Filtres!]">
                    Détails des lots
                </a>
            </div>
            <div class="Tab">
	        	<a href="[!Domaine!]/[!Lien!]/PdfGrillePrix?ResidenceLot=[!R::Id!]&amp;OngletLot=LotLots&amp;[!Filtres!]" target="_blank" rel="link">
	                  Imprimer la grille des prix
	            </a>
	        </div>

        </div>

</div>
<div class="ContenuOnglet">
	[IF [!OngletLot!]=LotDesc||[!OngletLot!]=]
		<div class="Contenu">[!R::Descriptif!]</div>
	[ELSE]
		<div>
			[COUNT [!R::getMesLots([!Departement!],[!Ville!],[!Budget!],[!Fiscalite!],[!Type!],[!R::Id!],[!FiltreActions!],0)!]|Total]
			[!NbPages:=[!Total:/[!NbParPage!]!]!]
			////////// Affichage Liste //////////
			[!LeMail:=!]
			[IF [!Total!]>0]
			    <table class="ListeLotsPgm">
			        <tr>
			            <th class="ListeLotsDescription" style="border-left:none;">Description</th>
			            <th class="ListeLotsResidence">Ville</th>
			            <th class="ListeLotsActabilite">Actabilité</th>
			            <th class="ListeLotsPrix">Prix</th>
			            <th class="ListeLotsAction">Action</th>
			        </tr>
	               [!Pair:=0!]
			        [STORPROC [!R::getMesLots([!Departement!],[!Ville!],[!Budget!],[!Fiscalite!],[!Type!],[!R::Id!],[!FiltreActions!],0,[!LimitStart!],[!NbParPage!])!]|L]

						[!TypeAppart:=!][!S:=!]
						[IF [!L::NbLots!]>1][!S:=s!][/IF]
						[IF [!L::TypeLogement!]=T1][!TypeAppart:=appartement[!S!] 1 pce!] [/IF]
						[IF [!L::TypeLogement!]=T2][!TypeAppart:=appartement[!S!] 2 pces!] [/IF]
						[IF [!L::TypeLogement!]=T3][!TypeAppart:=appartement[!S!] 3 pces!] [/IF]
						[IF [!L::TypeLogement!]=T4][!TypeAppart:=appartement[!S!] 4 pces!] [/IF]
						[IF [!L::TypeLogement!]=T5][!TypeAppart:=appartement[!S!] 5 pces!] [/IF]
						[IF [!L::TypeLogement!]=Villa][!TypeAppart:=villa[!S!]!] [/IF]
						[IF [!L::TypeLogement!]=Studio][!TypeAppart:=studio[!S!]!] [/IF]
	
						[!LeStatutDuLot:=Libre!]
						[IF [!L::StatutLot!]=2][!LeStatutDuLot:=<span class="optionne">Optionné</span>!][/IF]
						[IF [!L::StatutLot!]=3][!LeStatutDuLot:=<span class="reserve">Réservé</span>!][/IF]
						[IF [!L::StatutLot!]=4][!LeStatutDuLot:=<span class="acte">Acté</span>!][/IF]
			            <tr [IF [!Pair!]=1] class="Pair" [/IF]>
			                <td class="ListeLotsDescription" [IF [!Pos!]=[!NbResult!]]style="border-bottom:none;"[/IF]>
                    			<a href="/[!Lien!]?Lot=[!L::Id!]&amp;[!Filtres!]"><h2>[!TypeAppart!] n°[!L::Identifiant!]</h2></a>[!Utils::getPrice([!L::SurfaceLogement!])!] m² - [!L::Etage!]<br />
			                </td>
				            <td class="ListeLotsResidence" [IF [!Pos!]=[!NbResult!]]style="border-bottom:none;"[/IF]>
			                	[!L::CodePostal!]<br />[!L::Ville!]
				            </td>
				            <td class="ListeLotsActabilite" [IF [!Pos!]=[!NbResult!]]style="border-bottom:none;"[/IF]>
				                [!L::Actabilite!]
				             </td>
				             <td class="ListeLotsPrix" [IF [!Pos!]=[!NbResult!]]style="border-bottom:none;"[/IF]>
				                    [!L::Tarif!] € <br /> <div class="StatutLot">[!LeStatutDuLot!]</div>
					                // [NORESULT]Pas de tarif[/NORESULT]
				             </td>
				             <td class="ListeLotsAction" style="text-align:center;">
				               	<table class="SousOption">
			            	    	<tr>
            			    			<td>
            			    				[STORPROC ParcImmobilier/Lot/[!L::Id!]/Action|Act|0|1|tmsCreate|DESC]
						                     	 [STORPROC Systeme/User/Action/[!Act::Id!]|Prs][/STORPROC]
						 	                     <div class="ConfirmationMsg">
							                        [IF [!Act::Type!]=Reserver]
							                        	<span class="colorrouge">
															[IF [!Prs::Id!]=[!Systeme::User::Id!]]
															 		Vous avez réservé ce lot le  [DATE d/m/Y H:00][!Act::tmsCreate!][/DATE].
															 	[ELSE]
															 		Ce lot est réservé depuis le [DATE d/m/Y H:00][!Act::tmsCreate!][/DATE].
															 	[/IF]											
															</span>
							               	       		<br />
												    [/IF]
							                        [IF [!Act::Type!]=Optionner]
							                            [!Tms:=[!Act::tmsCreate!]!]
							                            //[!Tms+=172800!]
										    [!Tms+=259200!]

							                            [IF [!Prs::Id!]=[!Systeme::User::Id!]]
							                             	Vous avez optionné ce lot jusqu'au [DATE d/m/Y H:00][!Tms!][/DATE].
							                        	 	<br /><a class="BtnDesOptionnerLot Ajax" href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?Reference=[!R::Id!]&OngletLot=LotDesc&amp;[!Filtres!]&amp;DesAction=Optionner&amp;LotId=[!L::Id!]&amp;LAction=[!Act::Id!]&amp;[!Filtres!]"></a>
							                        	  	<a class="BtnReserverLot Ajax" href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?Reference=[!R::Id!]&OngletLot=LotDesc&amp;[!Filtres!]&amp;Action=Reserver&amp;LotId=[!L::Id!]&amp;[!Filtres!]" ></a>
							                             [ELSE]
							                             	Une option a été émise sur ce lot jusqu'au [DATE d/m/Y H:00][!Tms!][/DATE].
							                             [/IF]	
							                         [/IF]
							                      </div>
								                  [NORESULT]
								                  	   <a class="BtnReserverLot Ajax"  href="/[!Lien!]?Action=Reserver&amp;LotId=[!L::Id!]&amp;[!Filtres!]"></a>
								                       <a class="BtnOptionnerLot Ajax" href="/[!Lien!]?Action=Optionner&amp;LotId=[!L::Id!]&amp;[!Filtres!]"></a>
								                  [/NORESULT]
						                     [/STORPROC]
            			    			</td>
            			    		</tr>
					               	<tr class="Bottom">
				                		<td >
				                			<a class="DetailLotProgramme" href="/[!Lien!]?Lot=[!L::Id!]&amp;[!Filtres!]">Détail du lot</a>
										</td>
				                	</tr>
				                	</table>
				              </td>
				          </tr>
				          [IF [!Pair!]=0][!Pair:=1!][ELSE][!Pair:=0!][/IF]
				      [/STORPROC]
				</table>
			[/IF]
		</div>
	[/IF]
	
</div>
////////// Affichage Pagination //////////
[IF [!NbPages!]>1]
	[IF [!NbPages!]>[!Math::Floor([!NbPages!])!]]
		//On arrondit au chiffre superieur le nombre total de page
		[!NbPages:=[![!Math::Floor([!NbPages!])!]:+1!]!]
	[/IF]

    [!Next:=[!Page!]!]
    [!Next+=1!]
    <div class="Pagination">
        <div class="PaginationBody">
            <a class="PagiFirst" href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?Reference=[!R::Id!]&amp;OngletLot=LotLots&amp;[!Filtres!]">&nbsp;</a>
            <a class="PagiPrev" href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?Reference=[!R::Id!]&amp;OngletLot=LotLots&amp;[!Filtres!][IF [!Prev!]>1]&amp;Page=[!Prev!][/IF]">&nbsp;</a>
            [STORPROC [!NbPages!]|P]
                [IF [!Pos!]=[!Page!]]<strong>[/IF]
                <a href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?Reference=[!R::Id!]&amp;OngletLot=LotLots&amp;[!Filtres!][IF [!Pos!]>1]&amp;Page=[!Pos!][/IF]" [IF [!Pos!]=[!Page!]] class="bleu" [/IF]>[!Pos!]</a>
                [IF [!Pos!]=[!Page!]]</strong>[/IF]
            [/STORPROC]
 //           <a class="PagiNext" href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?Reference=[!R::Id!]&amp;OngletLot=LotLots&amp;[!Filtres!]&amp;Page=[!Next!]">&nbsp;</a>
               <a class="PagiNext" href="/[!Lien!]?[!Filtres!]&amp;Page=[IF[!Next!]>[!NbPages!]][!Pos!][ELSE][!Next!][/IF]&amp;Affichage=[!Affichage!]">&nbsp;</a>
           <a class="PagiLast" href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?Reference=[!R::Id!]&amp;OngletLot=LotLots&amp;[!Filtres!]&amp;Page=[!NbPages!]">&nbsp;</a>
        </div>
    </div>
[/IF]


<script type="text/javascript">
    // Traitement des actions en AJAX
    $$('a.Ajax').each(function(lien) {
       lien.addEvent('click', function(e) {
           e.stop();
           // Demande de confirmation
           if(confirm("Confirmez vous votre demande  ?")) {
               // Affichage loader
               lien.addClass('Loading');
               new Request({
                   url: lien.get('href'),
                   onComplete: function() {
                       // Texte à afficher
			document.location.reload();
                   }
               }).send();
           }
       });
    });
</script>
