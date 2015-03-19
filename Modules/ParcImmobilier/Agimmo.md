[IF [!Pass!]=ju23686]
	<REPONSE>
		[STORPROC Systeme/User/103/Residence|Res]
			<ENTETE>
				<PROMOTEUR>[!Res::Architecte!]</PROMOTEUR>
				<DATE>[!Res::DateLivraison!]</DATE>
			</ENTETE>
            <PROGRAMME>
                <REF_OPERATION>[!Res::Id!]</REF_OPERATION>
                <NUMERO>[!Res::Id!]</NUMERO>
				<NOM>[!Res::Titre!]</NOM>
				<ADRESSE>[!Res::Adresse!]</ADRESSE>
				[STORPROC ParcImmobilier/Ville/[!Res::VilleId!]|Ville][/STORPROC]
				<CP>[!Ville::CodePostal!]</CP>
				<VILLE>[!Ville::Nom!]</VILLE>
				<PAYS>France</PAYS>
				<ADRESSE_MAIL></ADRESSE_MAIL>
				<ADRESSE_WEB></ADRESSE_WEB>
				<TYPE_BIEN>[!Res::Id!]</TYPE_BIEN>
				<NB_LOTS>[!Res::NbApparts!]</NB_LOTS>
				<DATE_LIVRAISON>[!Res::DateLivraison!]</DATE_LIVRAISON>
				<OBJECTIF>mixte</OBJECTIF>
				<DESCRIPTIF_COURT>[UTIL NOHTML][!Res::Chapo!][/UTIL]</DESCRIPTIF_COURT>
				<DESCRIPTIF_LONG>[UTIL NOHTML][!Res::Texte!][/UTIL]</DESCRIPTIF_LONG>
				<DESCRIPTIF_COURT_EN>[UTIL NOHTML][!Res::EN-Chapo!][/UTIL]</DESCRIPTIF_COURT_EN>
				<DESCRIPTIF_LONG_EN>[UTIL NOHTML][!Res::EN-Texte!][/UTIL]</DESCRIPTIF_LONG_EN>
	                [STORPROC ParcImmobilier/Residence/[!Res::Id!]/TypeLogement|TL]
	                    [STORPROC ParcImmobilier/TypeLogement/[!TL::Id!]/Lot|Lot]
	                            <LOT>
	                                <REF_LOT>[!Lot::Id!]</REF_LOT>
	                                <NUMERO>[!Lot::Identifiant!]</NUMERO>
	                                [STORPROC ParcImmobilier/TypeLogement/[!Lot::TypeLogementId!]|Type][/STORPROC]
									<TYPE_BIEN>[!Type::Titre!]</TYPE_BIEN>
									<ORIENTATION>[!Lot::Orientation!]</ORIENTATION>
									<SURFACE_HABITABLE>[!Lot::SurfaceLogement!]</SURFACE_HABITABLE>
									<SURFACE_TERRAIN>[!Lot::SurfaceJardin!]</SURFACE_TERRAIN>
									<NB_PIECES>[!Lot::NombrePiece!]</NB_PIECES>
									<ETAGE>[!Lot::Etage!]</ETAGE>
									<DATE_LIVRAISON>[!Lot::DateLivraison!]</DATE_LIVRAISON>
									<OPTION></OPTION>
									<SURFACE_TERRASSE>[!Lot::SurfaceTerrasse!]</SURFACE_TERRASSE>
									<SURFACE_BALCON>[!Lot::SurfaceBalcon!]</SURFACE_BALCON>
									<PARKINGS>[!Lot::Stationnement!]</PARKINGS>
									<NOM_BATIMENT>[!Lot::Batiment!]</NOM_BATIMENT>
	                            </LOT>
	                    [/STORPROC]
	                [/STORPROC]
            </PROGRAMME>
        [/STORPROC]
	</REPONSE>
[/IF]