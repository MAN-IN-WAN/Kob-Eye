[OBJ ParcImmobilier|Residence|ModelR]
[!LAGestion:=!]
[IF [!Systeme::User::Login!]=CommercialAdm][!LAGestion:=1!][/IF]

[!LaResidence:=0!]
[IF [!ResidenceLot!]][!LaResidence:=[!ResidenceLot!]!][/IF]
[COUNT [!ModelR::getMesResidences([!Departement!],[!Ville!],[!Budget!],[!Fiscalite!],[!Type!],[!LaResidence!],[!LAGestion!])!]|Total]
[!NbPages:=[!Total:/[!NbParPage!]!]!]
[!UrlLot:=Residences?!]
[IF [!Departement!]!=] [!UrlLot+=Departement=[!Departement!]&!][/IF]
[IF [!Ville!]!=] [!UrlLot+=Ville=[!Ville!]&!][/IF]
[IF [!Budget!]!=] [!UrlLot+=Budget=[!Budget!]&!][/IF]
[IF [!Fiscalite!]!=] [!UrlLot+=Fiscalite=[!Fiscalite!]&!][/IF]

////////// Affichage Liste //////////
[IF [!Total!]>0]
    [STORPROC [!ModelR::getMesResidences([!Departement!],[!Ville!],[!Budget!],[!Fiscalite!],[!Type!],[!LaResidence!],[!LAGestion!],[!LimitStart!],[!NbParPage!])!]|R]
    	[STORPROC ParcImmobilier/Residence/[!R::ResidenceId!]/Donnee/Type=Perspective|D|0|1][/STORPROC]

    	<div class="ResidenceBloc">
	    	<div class="UneResidence">
			//[!R::Titre!] - [!R::Ville!] - [!R::Departement!] ([!R::NbLots!])
			<div class="Descriptif">
				<div class="VisuelResidence">
					<a href="/[!Lien!]?Reference=[!R::ResidenceId!]&amp;OngletLot=LotDesc&amp;[!Filtres!]" style="height:138px;" ><img src="[!Domaine!]/[!D::URL!].mini.160x138.jpg" alt="[!R::Titre!]" title="[!R::Titre!]" /></a>
				</div>
				<div class="Presentation">
					<div class="NomResidence"><a href="/[!Lien!]?Reference=[!R::ResidenceId!]&amp;OngletLot=LotDesc&amp;[!Filtres!]" />[!R::Titre!]</a></div>
					<div class="Ville">[SUBSTR 2][!R::CodePostal!][/SUBSTR] [!R::Ville!]</div>
					<div class="Contenu">
							[UTIL BBCODE][!R::Descriptif!][/UTIL]
					</div>...
					[IF [!R::IconeLoiResidence!]!=||[!R::LoiResidence!]!=]
						<div class="LoiResidence">
							[IF [!R::IconeLoiResidence!]!=]
								<img src="/[!R::IconeLoiResidence!]" alt="[!R::Titre!]" title="[!R::Titre!]" /> 
							[/IF]
							[!R::LoiResidence!]
						</div>
					[/IF]
	
				</div>
			</div>
			<div class="TypeLogement">
				<div class="Appartement">Lots disponibles</div>
				<div class="Livraison">Livraison : [!R::DateLivraison!]</div>
				    // le 1 : signifie qu'on veut les lots libres
				    [STORPROC [!ModelR::getMyTypeLot([!R::Id!],'1')!]|TL]
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
										<span class="LibelleApartir"></span>
										<span class="LeTarif"> 
											[IF [!TL::MaxTarif!]!=&&[!TL::MaxTarif!]!=0] 
												[UTIL NUMBERMILLIER][!TL::MaxTarif!][/UTIL] €
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
		</div>
		<div class="FinResidence">
			<div class="Pictos">
				[STORPROC ParcImmobilier/PictoResidence/Residence/[!R::ResidenceId!]|PR]
					<img src="/[!PR::Picto!]" alt="[!PR::Titre!]" title="[!PR::Titre!]" />
				[/STORPROC]
			</div>
			<div class="RetourLot"><a href="/[!Lien!]?Reference=[!R::ResidenceId!]&amp;OngletLot=LotDesc&amp;[!Filtres!]" />Voir tous les lots</a></div>
		</div>

    	</div>

    [/STORPROC]
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
					<a href="/[!Lien!]?[!Filtres!][IF [!Pos!]>1]&amp;Page=[!Cur!][/IF]&amp;Affichage=[!Affichage!]"  title="Aller &agrave; la page [!Cur!] de la liste des lots">[!Cur!]</a>
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

