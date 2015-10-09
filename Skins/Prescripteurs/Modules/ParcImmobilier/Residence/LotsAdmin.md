[OBJ ParcImmobilier|Residence|ModelR]
[!LAGestion:=1!]
[COUNT [!ModelR::getMesLots([!Departement!],[!Ville!],[!Budget!],[!Fiscalite!],[!Type!],[!ResidenceLot!],[!FiltreActions!],[!LAGestion!])!]|Total]
[!NbPages:=[!Total:/[!NbParPage!]!]!]
////////// Affichage Liste //////////
[!LeMail:=!]
[IF [!Total!]>0]
    <table class="ListeLots">
        <tr>
            <th class="ListeLotsDescription">Description</th>
            <th class="ListeLotsResidence">Résidence/Ville</th>
            <th class="ListeLotsPrix">Prix</th>
             <th class="ListeLotsAction">Actions</th>
            
        </tr>
        [!Pair:=0!]
        [STORPROC [!ModelR::getMesLots([!Departement!],[!Ville!],[!Budget!],[!Fiscalite!],[!Type!],[!ResidenceLot!],[!FiltreActions!],[!LAGestion!],[!LimitStart!],[!NbParPage!])!]|L]
	    	<form class="FormCommercialMaj" enctype="multipart/form-data"  method="get"  action="/[!Lien!]">
	    		//Filtre
	    		<input type="hidden" name="ResidenceLot" value="[!L::ResidenceId!]" />
	    		<input type="hidden" name="OngletLot" value="LotDesc" />
	    		<input type="hidden" name="Departement" value="[!Departement!]" />
	    		<input type="hidden" name="Ville" value="[!Ville!]" />
	    		<input type="hidden" name="Budget" value="[!Budget!]" />
	    		<input type="hidden" name="Fiscalite" value="[!Fiscalite!]" />
	    		<input type="hidden" name="Type" value="[!Type!]" />
	    		
				[!LeStatutDuLot:=Libre!]
				[IF [!L::StatutLot!]=2][!LeStatutDuLot:=<span class="optionne">Optionné</span>!][/IF]
				[IF [!L::StatutLot!]=3][!LeStatutDuLot:=<span class="reserve">Réservé</span>!][/IF]
				[IF [!L::StatutLot!]=4][!LeStatutDuLot:=<span class="acte">Vendu</span>!][/IF]
	
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
	                    <h2>[!TypeAppart!] n°[!L::Identifiant!]</h2>
	                </td>
	                <td class="ListeLotsResidence">
	                    <strong>[!L::Residence!]</strong><br /><br />
	                    [!L::Ville!]
	                </td>
	                <td class="ListeLotsPrix">
	                        [!L::Tarif!] € <br /> <div class="StatutLot">[!LeStatutDuLot!]</div>
	                </td>
	                <td class="ListeLotsAction">
	                	<table class="SousOption">
	                		<tr><td>
			                    [STORPROC ParcImmobilier/Lot/[!L::IdLot!]/Action|Act|0|1|tmsCreate|DESC]
		                			[!Action:=1!]
			                     	[STORPROC Systeme/User/Action/[!Act::Id!]|Prs][/STORPROC]
			 	                    <div class="ConfirmationMsg">
				                        [IF [!Act::Type!]=Vendu]
				                        	<span class="colorBleu">
					                            [!Tms:=[!Act::tmsCreate!]!]
					                            [!Prs::Login!]<br />
				                             	Lot vendu le [DATE d/m/Y H:00][!Tms!][/DATE].
											</span>
			                         	[/IF]
				                        [IF [!Act::Type!]=Reserver]
				                        	<span class="colorrouge">
					                            [!Prs::Login!]<br />
										 		Ce lot est réservé depuis le [DATE d/m/Y H:00][!Act::tmsCreate!][/DATE].<br />
											</span>
									 	[/IF]
				                        [IF [!Act::Type!]=Optionner]
				                        	<span class="colororange">
						                            [!Prs::Login!]<br />
						                            [!Tms:=[!Act::tmsCreate!]!]
					                            	//[!Tms+=172800!]
								    	[!Tms+=259200!]
					                             	Option sur ce lot jusqu'à [DATE d/m/Y H:00][!Tms!][/DATE].
								</span>
			                         	[/IF]
				                      </div>
			                    	  [NORESULT][!Action:=0!][/NORESULT]
			                     [/STORPROC]
	                		</td></tr>
	                		<tr><td class="ListeLotsAction">
			      	            [!Vente:=0!][!Option:=0!][!Reserve:=0!]
			                	[IF [!Action!]=1]	
				                	[IF [!Act::Type!]=Vendu]
				                		[!Vente:=1!]
				                	[/IF]
				                	[IF [!Act::Type!]=Reserver]
				                		[!Reserve:=1!]
				                	[/IF]
				                	[IF [!Act::Type!]=Optionner]
				                		[!Option:=1!]
				                	[/IF]
				                	<input type="hidden" name="Actionneur" value="[!Prs::Id!]" />
				                [ELSE]
				                	<select name="Actionneur" class="Joli">
				                		<option value="0" select="selected">Prescripteur</option>
				                		[STORPROC Systeme/Group/7/Group/*/User|PrU|0|600|Login|ASC]
					                		<option value ="[!PrU::Id!]" >[!PrU::Login!]</option>
				                		[/STORPROC]
				                	</select>
				                [/IF]
			                	<select name="ActionPrescripteur" class="Joli">
			                		[IF [!Vente!]=1]
				                		<option value ="Libre" >Remettre à la Vente</option>
			                		[/IF]
			                		[IF [!Reserve!]=1]
				                		<option value ="Vendu" >Vendu  </option>
				                		<option value ="Libre" >Remettre à la Vente</option>
			                		[/IF]
			                		[IF [!Option!]=1]
				                		<option value ="Vendu" >Vendu  </option>
				                		<option value ="Reserver" >Réservé  </option>
				                		<option value ="Libre" >Remettre à la Vente</option>
			                		[/IF]
			                		[IF [!Vente!]=0&&[!Reserve!]=0&&[!Option!]=0]
				                		<option value ="Vendu" >Vendu  </option>
				                		<option value ="Reserver" >Reservé  </option>
				                		<option value ="Optionner" >Optionné  </option>
			                			<option value ="Libre" >Remettre à la Vente</option>
			                		[/IF]
			                	</select>
			                	
			                	<input type="hidden" name="Envoi" value="EnvoiForm" />
			                	<input type="hidden" name="LotValider" value="[!L::IdLot!]" />
								<input type="submit" class="FormCommercialMaj" name="Valider"  />
				            </td></tr>
	                	</table>
		            </td>
	            </tr>
	            [IF [!Pair!]=0][!Pair:=1!][ELSE][!Pair:=0!][/IF]
		    </form>
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
