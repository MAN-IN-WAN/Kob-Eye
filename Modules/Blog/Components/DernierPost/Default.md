//affichage contenu des posts
<div class="[!NOMDIV!]" style="padding-bottom:[!PADDINGBOTTOM!]px">
	<div class="ContenuComposantNavigation">
		[STORPROC Blog/Categorie|Cat]		
			[COUNT Blog/Post/Brouillon=0&Actif=1|NbPost]
			[IF [!NbPost!]>0]
				<div class="blocTitrePost" >
					<div class="TitreIcone"></div>
					<div class="TitreCat">[!Cat::Titre!]</div>
					
				</div>
				[STORPROC Blog/Post/Brouillon=0&Actif=1|Pst|0|1|tmsCreate|DESC]
					<div class="blocTitreAuteur" >
						<div class="TitreAuteurPost">Posté par (metre user create)</div>
						<div class="TitreDatePost">le [DATE d.m.Y, à hh:mm][!Pst::tmsCreate!][/DATE]</div>
						
					</div>
					<div class="Contenu">[!Utils::noHtml([!Pst::Contenu!])!]</div>
		
					<div >
						//On verifie si il y a des fichiers
						[STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=Image|Don|||tmsCreate|DESC]
							<div class="FichPost">
								<img src="/[!Don::Fichier!].mini.530x130.jpg" alt="[!Pst::Titre!]"  />
							</div>
						[/STORPROC]
						[STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=Video|Donn|||tmsCreate|ASC]
							<div class="FichPost">
								<a href="/[!Donn::Fichier!]" class="mb"  title="[IF [!Donn::Titre!]!=][!Donn::Titre!][ELSE][!Pst::Titre!][/IF]">[!Donn::Titre!]</a>
							</div>
						[/STORPROC]
						[STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=Son|Son|0|1|tmsCreate|ASC]
							<div class="FichPost">
								<a href="/[!Donn::Fichier!]" class="mb"  title="[IF [!Donn::Titre!]!=][!Donn::Titre!][ELSE][!Pst::Titre!][/IF]">[!Donn::Titre!]</a>
							</div>
						[/STORPROC]
					</div>
					<div class="blocFinPost" >
						<div class="CatPost">[!Cat::Titre!]</div>
						<div class="CommentPost">Proposer un commentaire</div>
						<div class="VoirPost">Voir le post</div>
						<div class="CommunicationPost"></div>
					</div>
				[/STORPROC]
			[/IF]
		[/STORPROC]
	</div>
</div>
