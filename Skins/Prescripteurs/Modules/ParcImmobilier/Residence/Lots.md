[OBJ ParcImmobilier|Residence|ModelR]
[COUNT [!ModelR::getMesLots([!Departement!],[!Ville!],[!Budget!],[!Fiscalite!],[!Type!],[!ResidenceLot!],[!FiltreActions!],0)!]|Total]
[!NbPages:=[!Total:/[!NbParPage!]!]!]
////////// Affichage Liste //////////
[IF [!FiltreActions!]]<h2 style="padding:10px 0;text-align:center;">Vous avez demandé les lots [!FiltreActions!]</h2>[/IF]
[!LeMail:=!]
[IF [!Total!]>0]
    <table class="ListeLots">
        <tr>
            <th class="ListeLotsDescription">Description</th>
            <th class="ListeLotsResidence">Résidence/Ville</th>
            <th class="ListeLotsLivraison">Livraison</th>
            <th class="ListeLotsActabilite">Actabilité</th>
            <th class="ListeLotsPrix">Prix</th>
            <th class="ListeLotsAction">Action</th>
        </tr>
        [!Pair:=0!]
        [STORPROC [!ModelR::getMesLots([!Departement!],[!Ville!],[!Budget!],[!Fiscalite!],[!Type!],[!ResidenceLot!],[!FiltreActions!],0,[!LimitStart!],[!NbParPage!])!]|L]
			[!LeStatutDuLot:=Libre!]
			[IF [!L::StatutLot!]=2][!LeStatutDuLot:=<span class="optionne">Optionné</span>!][/IF]
			[IF [!L::StatutLot!]=3][!LeStatutDuLot:=<span class="reserve">Réservé</span>!][/IF]
			[IF [!L::StatutLot!]=4][!LeStatutDuLot:=<span class="acte">Acté</span>!][/IF]


			[!TypeAppart:=!][!S:=!]
			[IF [!L::NbLots!]>1][!S:=s!][/IF]
			[IF [!L::TypeLogement!]=T1][!TypeAppart:=appartement[!S!] 1 pce!] [/IF]
			[IF [!L::TypeLogement!]=T2][!TypeAppart:=appartement[!S!] 2 pces!] [/IF]
			[IF [!L::TypeLogement!]=T3][!TypeAppart:=appartement[!S!] 3 pces!] [/IF]
			[IF [!L::TypeLogement!]=T4][!TypeAppart:=appartement[!S!] 4 pces!] [/IF]
			[IF [!L::TypeLogement!]=T5][!TypeAppart:=appartement[!S!] 5 pces!] [/IF]
			[IF [!L::TypeLogement!]=Villa][!TypeAppart:=villa[!S!]!] [/IF]
			[IF [!L::TypeLogement!]=Studio][!TypeAppart:=studio[!S!]!] [/IF]

            <tr [IF [!Pair!]=1] class="Pair" [/IF]>
                <td class="ListeLotsDescription">
                    <a href="/[!Lien!]?Lot=[!L::Id!]&amp;[!Filtres!]"><h2>[!TypeAppart!] n°[!L::Identifiant!]</h2></a>
                   [!Utils::getPrice([!L::SurfaceLogement!])!] m² - [!L::Etage!]<br />
		    [IF [!L::IconeLoiResidence!]!=||[!L::LoiResidence!]!=]
			<div class="LoiResidence">
			    [IF [!L::IconeLoiResidence!]!=]
				<img src="/[!L::IconeLoiResidence!]" alt="[!L::Titre!]" title="[!L::Titre!]" /> 
			    [/IF]
			    [!L::LoiResidence!]
			</div>
		    [/IF]
		    <div class="Pictos">
			[STORPROC ParcImmobilier/PictoResidence/Residence/[!L::ResidenceId!]|PR]
				<img src="/[!PR::Picto!]" alt="[!PR::Titre!]" title="[!PR::Titre!]" />
			[/STORPROC]
		    </div>
			<div class="CcialLotTableListe">[!L::AccrocheCCial!]</div>
                </td>
                <td class="ListeLotsResidence">
                    <strong>[!L::Residence!]</strong><br /><br />
                    [!L::CodePostal!]<br />[!L::Ville!]
                </td>
                <td class="ListeLotsLivraison">
                    [!L::DateLivraison!]
                </td>
                <td class="ListeLotsActabilite">
                    [!L::Actabilite!]
                </td>
                <td class="ListeLotsPrix">
                       [UTIL NUMBERMILLIER][!L::Tarif!][/UTIL]  € <br /> <div class="StatutLot">[!LeStatutDuLot!]</div>
	                 // [NORESULT]Pas de tarif[/NORESULT]
			
                </td>


                <td class="ListeLotsAction">
                	<table class="SousOption">
                	<tr>
                		<td>
		                    [STORPROC ParcImmobilier/Lot/[!L::IdLot!]/Action|Act|0|1|tmsCreate|DESC]
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
			                        	 	<br /><a class="BtnDesOptionnerLot Ajax" href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?ResidenceLot=[!R::Id!]&OngletLot=LotDesc&amp;[!Filtres!]&amp;DesAction=Optionner&amp;LotId=[!L::IdLot!]&amp;LAction=[!Act::Id!]&amp;[!Filtres!]"></a>
			                        	  	<a class="BtnReserverLot Ajax" href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?ResidenceLot=[!R::Id!]&OngletLot=LotDesc&amp;[!Filtres!]&amp;Action=Reserver&amp;LotId=[!L::IdLot!]&amp;[!Filtres!]"></a>
			                             [ELSE]
			                             	Une option a été émise sur ce lot jusqu'au [DATE d/m/Y H:00][!Tms!][/DATE].
			                             [/IF]	
			                         [/IF]
			                      </div>
				                  [NORESULT]
				                  	   <a class="BtnReserverLot Ajax" href="/[!Lien!]?Action=Reserver&amp;LotId=[!L::IdLot!]&amp;[!Filtres!]"></a>
				                       <a class="BtnOptionnerLot Ajax" href="/[!Lien!]?Action=Optionner&amp;LotId=[!L::IdLot!]&amp;[!Filtres!]"></a>
				                  [/NORESULT]
	                     	[/STORPROC]                		
                		</td>
                	</tr>
                	<tr  class="Bottom">
                		<td > <a class="DetailLot" href="/[!Lien!]?Lot=[!L::Id!]&amp;[!Filtres!]">Détail du lot</a></td>
                	</tr>
                	</table>
	            </td>
            </tr>
            [IF [!Pair!]=0][!Pair:=1!][ELSE][!Pair:=0!][/IF]
        [/STORPROC]
    </table>
[ELSE]
    <p>Votre recherche n'a retourné aucun résultat.</p>
[/IF]

////////// Affichage Pagination //////////
[!PagNbNum:=3!]
[IF [!NbPages!]>1]
	[IF [!NbPages!]>[!Math::Floor([!NbPages!])!]]
		//On arrondit au chiffre superieur le nombre total de page
		[!NbPages:=[![!Math::Floor([!NbPages!])!]:+1!]!]
	[/IF]
    [!Next:=[!Page!]!]
    [!Next+=1!]
    [!Prev:=[!Page!]!]
    [!Prev-=1!]
 	[IF [!Page!]=][!Page:=1!][/IF]
	 <div class="Pagination">
        <div class="PaginationBody">
            <a class="PagiFirst" href="/[!Lien!]?[!Filtres!]&amp;Affichage=[!Affichage!]">&nbsp;</a>
            <a class="PagiPrev" href="/[!Lien!]?[!Filtres!][IF [!Prev!]>1]&amp;Page=[!Prev!][/IF]&amp;Affichage=[!Affichage!]">&nbsp;</a>
	        
	        //Liste des Numeros de pages
			[!Decal:=[!PagNbNum:/3!]!]
			[!Depart:=[!Page:-[!Decal:+1!]!]!]
			//Affichage de la premiere

			[IF [!Depart!]>0]
				<a href="/[!Lien!]?[!Filtres!]" title="Aller &agrave; la page 1 de la liste des lots">1</a> ...
			[/IF]
			[STORPROC [!PagNbNum!]|Pag]
				[!Cur:=[!Pos:+[!Depart!]!]!]
				[IF [!Cur!]!=[!Page!]&&[!Cur!]>0&&[!Cur!]<[!NbPages!]]
					<a href="/[!Lien!]?[!Filtres!][IF [!Cur!]>1]&amp;Page=[!Cur!][/IF]&amp;Affichage=[!Affichage!]"  title="Aller &agrave; la page [!Cur!] de la liste des lots">[!Cur!]</a>
				[ELSE]
					[IF [!Page!]=[!NbPages!]][ELSE][IF [!Cur!]=[!Page!]]<span class="current">[!Page!]</span>[/IF][/IF]
				[/IF]
			[/STORPROC]
			//Affichage de la derniere
			[IF [!Depart!]<=[!NbPages:-[!Decal:+1!]!]]
				... [IF [!Page!]=[!NbPages!]]<span class="current">[!NbPages!]</span>[ELSE]<a href="/[!Lien!]?[!Filtres!]&amp;Page=[!NbPages!]&amp;Affichage=[!Affichage!]" title="Aller &agrave; la fin de la liste des lots">[!NbPages!]</a>[/IF]
			[/IF]
              <a class="PagiNext" href="/[!Lien!]?[!Filtres!]&amp;Page=[IF[!Next!]>[!NbPages!]][!Pos!][ELSE][!Next!][/IF]&amp;Affichage=[!Affichage!]">&nbsp;</a>
            <a class="PagiLast" href="/[!Lien!]?[!Filtres!]&amp;Page=[!NbPages!]&amp;Affichage=[!Affichage!]">&nbsp;</a>
        </div>
    </div>
[/IF]
            
            // ancienne methode
            //[STORPROC [!NbPages!]|P]
              //  [IF [!Pos!]=[!Page!]]<strong>[/IF]
                //<a href="/[!Lien!]?[!Filtres!][IF [!Pos!]>1]&amp;Page=[!Pos!][/IF]" [IF [!Pos!]=[!Page!]] class="bleu" [/IF]>[!Pos!]</a>
                //[IF [!Pos!]=[!Page!]]</strong>[/IF]
            //[/STORPROC]
            
            
            
  
<script type="text/javascript">
    // Traitement des actions en AJAX
    $$('a.Ajax').each(function(lien) {
       lien.addEvent('click', function(e) {
           e.stop();
          // alert(e.name);
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
