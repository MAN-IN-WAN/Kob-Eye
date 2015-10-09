[STORPROC ParcImmobilier/Lot/[!Lot!]|L|0|1]
	[STORPROC ParcImmobilier/Lot/[!L::Id!]/GrillePrix|GP|0|1][/STORPROC]
	[STORPROC ParcImmobilier/TypeLogement/Lot/[!L::Id!]|TL|0|1][/STORPROC]
	[STORPROC ParcImmobilier/Residence/TypeLogement/[!TL::Id!]|R|0|1][/STORPROC]
	[STORPROC ParcImmobilier/Residence/[!R::Id!]/Donnee/Type=Perspective|D|0|1][/STORPROC]
	[STORPROC ParcImmobilier/Lot/[!L::Id!]/DonneeLot/Type=Plan|PL|0|1][/STORPROC]
	[STORPROC ParcImmobilier/Lot/[!L::Id!]/DonneeLot/Type=ContratSpecifique|CS|0|1][/STORPROC]
	[STORPROC ParcImmobilier/Lot/[!L::Id!]/DonneeLot/Type=AppelFond|AF|0|1][/STORPROC]

[/STORPROC]
[!TypeAppart:=]
[IF [!TL::Type!]=T1][!TypeAppart:=appartement 1 pièce!] [/IF]
[IF [!TL::Type!]=T2][!TypeAppart:=appartements 2 pièces!] [/IF]
[IF [!TL::Type!]=T3][!TypeAppart:=appartements 3 pièces!] [/IF]
[IF [!TL::Type!]=T4][!TypeAppart:=appartements 4 pièces!] [/IF]
[IF [!TL::Type!]=T5][!TypeAppart:=appartements 5 pièces!] [/IF]
[IF [!TL::Type!]=Villa][!TypeAppart:=villas!] [/IF]
[IF [!TL::Type!]=Studio][!TypeAppart:=studios!] [/IF]

<div class="DetailLot">
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
		
			
		<div id="doc-lot">
			[COUNT ParcImmobilier/Lot/[!L::Id!]/DonneeLot/Type=DocCommerciaux|NbDLot]
			[COUNT ParcImmobilier/Lot/[!L::Id!]/DonneeLot/Type=Plan|NbPLot]
			[IF [!PL:URL!]=&&[!CS:URL!]=&&[!AF:URL!]=&&[!NbDLot!]=0&&[!NbPLot!]=0]
	
			[ELSE]
				<h2>Documents du lot</h2>
				[IF [!PL:URL!]!=]<div class="PlanLot"><a href="/[!PL:URL!]" target="_blank" >Plan du lot</a></div>[/IF]
				[IF [!CS:URL!]!=]<div class="ReservationSpecif"><a href="/[!CS:URL!]" target="_blank" >Contrat spécifique réservation</a></div>[/IF]
				[IF [!AF:URL!]!=]<div class="AppelFond"><a href="/[!AF:URL!]" target="_blank" >Lettre d'appel de fond</a></div>[/IF]
			
				[STORPROC ParcImmobilier/Lot/[!L::Id!]/DonneeLot/Type=DocCommerciaux|DL]
					<div class="DocCommerciaux"><a href="/[!DL::URL!]" target="_blank" >[!DL::Titre!]</a></div>
				[/STORPROC]
				[STORPROC ParcImmobilier/Lot/[!L::Id!]/DonneeLot/Type=Plan|PL]
				
					<div class="DocCommerciaux"><a href="/[!PL::URL!]" target="_blank" >[IF [!PL::Titre!]~Plan][!PL::Titre!][ELSE]Plan du lot[/IF]</a></div>
				[/STORPROC]
			[/IF]

	
		</div>
	</div>
	<div class="ColonneDetails">
		<div class="InfoPrincipales">
                 </h2></a>
   			<div class="NomLot">[!TypeAppart!] N° [!L::Identifiant!]</div>
			<div class="Livraison">Livraison : [!R::DateLivraison!]</div>
			<div class="TarifEtCCial"><div class="TarifLot">[UTIL NUMBERMILLIER][!GP::Tarif!][/UTIL] €</div><div class="CcialLot">[!L::AccrocheCCial!]</div></div>
			<div class="Options">
            	[STORPROC ParcImmobilier/Lot/[!L::Id!]/Action|Act|0|1|tmsCreate|DESC]
                	[STORPROC Systeme/User/Action/[!Act::Id!]|Prs][/STORPROC]
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
				// [!Tms+=86400!]
				[!Tms+=259200!]
				[IF [!Prs::Id!]=[!Systeme::User::Id!]]
					Vous avez optionné ce lot jusqu'au [DATE d/m/Y H:00][!Tms!][/DATE].
					//<a class="BtnDesOptionnerLot Ajax" href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?Reference=[!R::Id!]&OngletLot=LotDesc&amp;[!Filtres!]&amp;DesAction=Optionner&amp;LotId=[!L::Id!]&amp;[!Filtres!]" ></a>
					<br /><a class="BtnDesOptionnerLot Ajax" href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?ResidenceLot=[!R::Id!]&OngletLot=LotDesc&amp;[!Filtres!]&amp;DesAction=Optionner&amp;LotId=[!L::Id!]&amp;LAction=[!Act::Id!]&amp;[!Filtres!]" style="float:left;padding-right:10px;""></a>                    	 	
					<a class="BtnReserverLot Ajax" href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?Reference=[!R::Id!]&OngletLot=LotDesc&amp;[!Filtres!]&amp;Action=Reserver&amp;LotId=[!L::Id!]&amp;LAction=[!Act::Id!]&amp;[!Filtres!]"></a>
				[ELSE]
					Une option a été émise sur ce lot jusqu'au [DATE d/m/Y H:00][!Tms!][/DATE].
				[/IF]	
			[/IF]
                  	[NORESULT]
                  	   	<a class="BtnReserverLot Ajax" href="/[!Lien!]?Action=Reserver&amp;LotId=[!L::Id!]&amp;[!Filtres!]" ></a>
                       		<a class="BtnOptionnerLot Ajax" href="/[!Lien!]?Action=Optionner&amp;LotId=[!L::Id!]&amp;[!Filtres!]" style="margin-left:10px ;"></a>
                  	[/NORESULT]
                 [/STORPROC]
			</div>
		</div>
		<div class="InfoSurfaces">
			<div class="Titre">Détail du lot</div>
			<div class="LesSurfaces">
				[IF [!L::SurfaceLogement!]!=]- Surface habitable :  [!Utils::getPrice([!L::SurfaceLogement!])!] m2<br />[/IF]
				[IF [!L::SurfaceBalcon!]!=&&[!L::SurfaceBalcon!]!=0]- Surface balcon :  [!Utils::getPrice([!L::SurfaceBalcon!])!] m2<br />[/IF]
				[IF [!L::SurfaceTerrasse!]!=&&[!L::SurfaceTerrasse!]!=0]- Surface terrasse : [!Utils::getPrice([!L::SurfaceTerrasse!])!] m2<br />[/IF]
				[IF [!GP::TarifLogementMC!]!=&&[!L::TarifLogementMC!]!=0]- Prix au m2 <u>(hors parking)</u> : [UTIL NUMBERMILLIER][!GP::TarifLogementMC!][/UTIL] €<br />[/IF]
				[IF [!GP::LoyerTotal!]!=&&[!L::LoyerTotal!]!=0]-   Loyer plafond Duflot   : [!GP::LoyerTotal!] €<br />[/IF]
				[IF [!GP::Rentabilite!]!=&&[!L::Rentabilite!]!=0]- Rentabilité brûte: [!GP::Rentabilite!] %<br />[/IF]
				[IF [!L::Stationnement!]!=]
					[IF [!GP::LoyerStationnement!]!=&&[!GP::LoyerStationnement!]!=0]- Prix du stationnement : [!GP::LoyerStationnement!] €<br />[/IF]
					- N° Stationnement : [!L::Stationnement!] <br />
				[/IF]
				[IF [!L::Etage!]!=]- Etage : [!L::Etage!]<br />[/IF]
				[IF [!L::Annexes!]!=]- Parking extérieur : [!L::Annexes!]<br />[/IF]
				[IF [!L::LoyerFoncia!]!=]- Loyer : [!L::LoyerFoncia!] €/mois<br />???[/IF]
				- Gestionnaire : [!R::Gestionnaire!]<br />
			</div>
			[IF [!L::IconeLoiResidence!]!=||[!L::LoiResidence!]!=]
				<div class="Fiscales">Zones Fiscales</div>
				<div class="LoiResidence">
					[IF [!L::IconeLoiResidence!]!=]
						<img src="/[!L::IconeLoiResidence!]" alt="[!L::Titre!]" title="[!L::Titre!]" /> 
					[/IF]
					[!L::LoiResidence!]
				</div>
			[/IF]

		//	<div class="Titre">Garanties supplémentaires</div>
		//	<div class="Titre">??????</div>
			
		</div>

	</div>
</div>


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