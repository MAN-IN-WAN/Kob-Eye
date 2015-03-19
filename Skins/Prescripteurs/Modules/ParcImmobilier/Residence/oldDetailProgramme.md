[STORPROC ParcImmobilier/Residence/[!Reference!]|R|0|1]
	[STORPROC ParcImmobilier/Ville/Residence/[!R::Id!]|V|0|1][/STORPROC]
	[STORPROC ParcImmobilier/Residence/[!R::Id!]/Donnee/Type=Perspective|D|0|1][/STORPROC]

[/STORPROC]
<div class="DetailProgramme">
	<div class="ColonneDocs">
		<h2>Documents du programme</h2>
		[IF [!R::Doc!]=&&[!R::PlanSitu!]=&&[!R::PlanMasse!]=&&[!R::ContratReservation!]=]
			<div class="Vide">Documents non disponibles</div>
		[ELSE]
			[IF [!R::Doc!]!=]<div class="Plaquette"><a href="/[!R::Doc!]" target="_blank" >Plaquette</a></div>[/IF]
			[IF [!R::PlanSitu!]!=]<div class="Situation"><a href="[!R::PlanSitu!]" target="_blank" >Plan de situation</a></div>[/IF]
			[IF [!R::PlanMasse!]!=]<div class="Masse"><a href="/[!R::PlanMasse!]" target="_blank" >Plan de masse</a></div>[/IF]
			[IF [!R::ContratReservation!]!=]<div class="Reservation"><a href="/[!R::ContratReservation!]" target="_blank" >Contrat générique réservation</a></div>[/IF]
		[/IF]

	</div>
	<div class="ColonneDetails">
		<div class="InfoPrincipales">
			<div class="Visuel"><img src="[!Domaine!]/[!D::URL!].mini.210x181.jpg" alt="[!R::Titre!]" title="[!R::Titre!]" /></div>
			<div class="NomResidence">[!R::Titre!]</div>
			<div class="Ville">[SUBSTR 2][!V::CodePostal!][/SUBSTR] - [!V::Nom!]</div>
			<div class="Livraison">[!R::DateLivraison!]</div>
			[IF [!L::IconeLoiResidence!]!=||[!L::LoiResidence!]!=]
				<div class="Fiscales">Plafonds de loyers théoriques</div>
				<div class="LoiResidence">
					[IF [!R::IconeLoiResidence!]!=]
						<img src="/[!R::IconeLoiResidence!]" alt="[!R::Titre!]" title="[!R::Titre!]" /> 
					[/IF]
					[!R::LoiResidence!]
				</div>
			[/IF]
			[STORPROC [!ModelR::getMyTypeLot([!R::Id!],1)!]|TL]
					[!TypeAppart:=!][!S:=!]
					[IF [!TL::NbLots!]>1][!S:=s!][/IF]
					[IF [!TL::TypeLogement!]=T1][!TypeAppart:=appartement[!S!] 1 pce!] [/IF]
					[IF [!TL::TypeLogement!]=T2][!TypeAppart:=appartement[!S!] 2 pce[!S!]!] [/IF]
					[IF [!TL::TypeLogement!]=T3][!TypeAppart:=appartement[!S!] 3 pce[!S!]!] [/IF]
					[IF [!TL::TypeLogement!]=T4][!TypeAppart:=appartement[!S!] 4 pce[!S!]!] [/IF]
					[IF [!TL::TypeLogement!]=T5][!TypeAppart:=appartement[!S!] 5 pce[!S!]!] [/IF]
					[IF [!TL::TypeLogement!]=Villa][!TypeAppart:=villa[!S!]!] [/IF]
					[IF [!TL::TypeLogement!]=Studio][!TypeAppart:=studio[!S!]!] [/IF]
					[IF [!TL::NbLots!]>0]
					<div class="AppartType">
						<div class="AppartNb"><span class="Nb">[!TL::NbLots!]</span> [!TypeAppart!]</div>
						<div class="TarifAppart">
							<span class="LibelleApartir">
							[IF [!TL::NbLots!]=1]
								</span>
								<span class="LeTarif"> 
									[!TL::MaxTarif!] €
								</span>
							[ELSE]
								[IF [!TL::MaxTarif!]=[!TL::MiniTarif!]]
									<span class="LeTarif">[!TL::MiniTarif!] €</span>
								[ELSE] 
									[IF [!TL::MaxTarif!]!=[!TL::MiniTarif!]]à partir de </span><span class="LeTarif">[!TL::MiniTarif!] €</span>[/IF]
								[/IF] 
							[/IF] 
						</div>
					</div>
			[/STORPROC]			
	</div>
</div>
<div class="Pictos">
	[STORPROC ParcImmobilier/PictoResidence/Residence/[!R::Id!]|PR]
			<img src="/[!PR::Picto!]" alt="[!PR::Titre!]" title="[!PR::Titre!]" />
	[/STORPROC]
</div>

<div class="LotDetailOnglet">
        <div class="Tabs">
            <div class="Tab [IF [!OngletLot!]!=LotDesc]TabActive[/IF]">
                <a href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?Affichage=Lot&OngletLot=LotDesc&amp;[!Filtres!]">
                   Description
                </a>
            </div>
            <div class="Tab [IF [!OngletLot!]=LotLots]TabActive[/IF]">
                <a href="/[!Systeme::getMenu(ParcImmobilier/Residence)!]?Affichage=Lot&OngletLot=LotLots&amp;[!Filtres!]">
                    Détails des lots
                </a>
            </div>
        </div>

</div>
<div class="ContenuOnglet">
	[IF [!OngletLot!]=LotDesc]
		<div class="Contenu">[!R::Descriptif]</div>
	[ELSE]
		<div class="Contenu">
		</div>
	[/IF]

</div>
