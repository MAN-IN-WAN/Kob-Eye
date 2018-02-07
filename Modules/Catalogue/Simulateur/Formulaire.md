[IF [!I_DevisEnligne!]!=]
	////////////////// Devis demandé

	////////////////// On verifie les champs du formulaire
	[!I_Error:=0!]
	[IF [!Utils::isMail([!I_Mail!])!]!=1][!I_Mail_Error:=1!][!I_Error:=1!][/IF]
	[IF [!I_Nom!]=][!I_Nom_Error:=1!][!I_Error:=1!][/IF]
	[IF [!I_Tel!]=][!I_Tel_Error:=1!][!I_Error:=1!][/IF]
	[IF [!I_Ville!]=][!I_Ville_Error:=1!][!I_Error:=1!][/IF]
	[IF [!hash!]]	
		[!VerifMD5:=[!Utils::md5([!Result!])!]!]
		[IF [!VerifMD5!]!=[!hash!]][!C_Code_Error:=1!][!I_Error:=1!][/IF]
	[/IF]

	[IF [!I_Error!]=1]
		<div class="BlocError">
			<strong>Erreur dans votre formulaire :</strong>
			<ul>
				[IF [!I_Nom_Error!]]<li>Le nom est obligatoire</li>[/IF]
				[IF [!I_Ville_Error!]]<li>La ville est obligatoire</li>[/IF]
				[IF [!I_Tel_Error!]]<li>Le téléphone est obligatoire</li>[/IF]
				[IF [!I_Mail_Error!]]<li>L'adresse mail est incorrecte</li>[/IF]
				[IF [!C_Code_Error!]]<li>Opération fausse</li>[/IF]
			</ul>
		</div>
	[ELSE]
	
		// Enregistrement Contact 
		[STORPROC Newsletter/GroupeEnvoi/5|GR|0|1]
			[NORESULT]
				[OBJ Newsletter|GroupeEnvoi|GR]
				[METHOD GR|Set]
					[PARAM]Titre[/PARAM]
					[PARAM]Simulation_[!I_Simulateur!][/PARAM]
				[/METHOD]
				[METHOD GR|Save][/METHOD]
			[/NORESULT]
		[/STORPROC]
		// 2 - on vérifie que le contact existe, s'il n'existe pas on le créé
		[STORPROC Newsletter/GroupeEnvoi/[!GR::Id!]/Contact/Email=[!I_Mail!]|Con|0|1]
			[NORESULT]
				[OBJ Newsletter|Contact|Con]
				[METHOD Con|Set]
					[PARAM]Email[/PARAM]
					[PARAM][!I_Mail!][/PARAM]
				[/METHOD]
				[METHOD Con|Set]
					[PARAM]Nom[/PARAM]
					[PARAM][!I_Nom!][/PARAM]
				[/METHOD]
				[METHOD Con|Set]
					[PARAM]Prenom[/PARAM]
					[PARAM][/PARAM]
				[/METHOD]
				[METHOD Con|Set]
					[PARAM]Telephone[/PARAM]
					[PARAM][!I_Tel!][/PARAM]
				[/METHOD]
				[METHOD Con|AddParent]
					[PARAM]Newsletter/GroupeEnvoi/[!GR::Id!][/PARAM]
				[/METHOD]
				[METHOD Con|Save][/METHOD]
			[/NORESULT]
		[/STORPROC]
		
		
		// Enregistrement devis
		[!Chemin:=Catalogue/Simulateur!]
		[STORPROC [!Chemin!]/[!I_Simulateur!]|Sim|0|1]
			[OBJ Catalogue|Devis|Dv]
			[METHOD Dv|Set]
				[PARAM]Titre[/PARAM]
				[PARAM][!Sim::Titre!][/PARAM]
			[/METHOD]
			[METHOD Dv|Set]
				[PARAM]Nom[/PARAM]
				[PARAM][!I_Nom!][/PARAM]
			[/METHOD]
			[METHOD Dv|Set]
				[PARAM]Email[/PARAM]
				[PARAM][!I_Mail!][/PARAM]
			[/METHOD]
			[METHOD Dv|Set]
				[PARAM]Ville[/PARAM]
				[PARAM][!I_Ville!][/PARAM]
			[/METHOD]
			[METHOD Dv|Set]
				[PARAM]Telephone[/PARAM]
				[PARAM][!I_Tel!][/PARAM]
			[/METHOD]
			[METHOD Dv|AddParent]
				[PARAM]Catalogue/Simulateur/[!Sim::Id!][/PARAM]
			[/METHOD]
			[METHOD Dv|Save][/METHOD]
			[!LeDevis:=[!Dv::Id!]!]
		
			[STORPROC [!Chemin!]/[!I_Simulateur!]/Etape/Publier=1|Etp]
				// lecture etape
				[STORPROC [!Chemin!]/[!I_Simulateur!]/Etape/[!Etp::Id!]/Question/Publier=1|Qst|||Ordre|ASC]
					// lecture Question
					[STORPROC [!Chemin!]/[!I_Simulateur!]/Etape/[!Etp::Id!]/Question/[!Qst::Id!]/Choix|Chx]
						// lecture Choix de réponse
						[IF [!Rq_[!Qst::Id!]!]=[!Chx::Id!]]						
							[OBJ Catalogue|Reponse|RpDv]
							[METHOD RpDv|Set]
								[PARAM]Etape[/PARAM]
								[PARAM][!Etp::Id!][/PARAM]
							[/METHOD]
							[METHOD RpDv|Set]
								[PARAM]Question[/PARAM]
								[PARAM][!Qst::Id!][/PARAM]
							[/METHOD]
							[METHOD RpDv|Set]
								[PARAM]Reponse[/PARAM]
								[PARAM][!Chx::Id!][/PARAM]
							[/METHOD]
							[METHOD RpDv|AddParent]
								[PARAM]Catalogue/Devis/[!Dv::Id!][/PARAM]
							[/METHOD]
							[METHOD RpDv|Save][/METHOD]
						[/IF]
					[/STORPROC]
				[/STORPROC]
			[/STORPROC]		
			
			// IL FAUDRA LIER LES PRODUITS REPONDANT AU DEVIS !!!!!!!!!!!!!!!!!!!
			
		[/STORPROC]


		//Redirection vers résultats
		[IF [!Redirect!]=][!Redirect:=[!Lien!]!][/IF]
		[REDIRECT][!Redirect!]/Resultat?Devis=[!LeDevis!]&I_Nom=[!I_Nom!]&I_Tel=[!I_Tel!]&I_Mail=[!I_Mail!]&I_Ville=[!I_Ville!][/REDIRECT]
	[/IF]

[/IF]
[STORPROC [!Chemin!]|Sim][/STORPROC]
<form action="/[!Lien!]" method="post" enctype="multipart/form-data" name="form_Simulateur" class="form_Simulateur">

	<div class="Simulateur[!Sim::Id!]">
		<div class="SimulateurDescriptif">
			<h1>[!Sim::Titre!]</h1>
			[IF [!Sim::Chapo!]]<div class="Chapo">[!Sim::Chapo!]</div>[/IF]
			[IF [!Sim::Description!]]<p>[!Sim::Description!]</p>[/IF]
		</div>
		// Affichage des coordonnées
		[STORPROC [!Chemin!]/Etape/Publier=1|Etp|||Ordre|ASC]
			[LIMIT 0|100]
				// Etapes
				<div class="UneEtape UneEtape[!Etp::Id!]">
					[IF [!Etp::AfficheTitre!]]
						<div class="TitreEtape">
							[IF [!Etp::IconeTitre!]!=]
								<div class="IconeEtape"><img src="/[!Etp::IconeTitre!]" alt="[!Etp::Titre!]" title="[!Etp::Titre!]" /></div>
							[/IF]
							<div class="LeTitre">[!Etp::Titre!]</div>
						</div>
					[/IF]

					[IF [!Key!]=0]
						<div class="Coordonnes">
							<div class="LigneForm">
								<label>Nom <span class="obligatoire">*</span></label>
								<input type="text"  name="I_Nom" value="[IF [!Reset!]=][!I_Nom!][/IF]" tabindex="10"  style="text-transform:uppercase;" [IF [!I_Nom_Error!]]class="Error"[ELSE][/IF]/>
							</div>
							<div class="LigneForm">
								<label>Téléphone <span class="obligatoire">*</span></label>
								<input type="text"  name="I_Tel" value="[IF [!Reset!]=][!I_Tel!][/IF]" tabindex="15"  [IF [!I_Tel_Error!]]class="Error"[ELSE][/IF]/>
							</div>
							<div class="LigneForm">
								<label>Ville <span class="obligatoire">*</span></label>
								<input type="text"  name="I_Ville" value="[IF [!Reset!]=][!I_Ville!][/IF]" tabindex="20"  [IF [!I_Ville_Error!]]class="Error"[ELSE][/IF]/>
								
							</div>
							<div class="LigneForm" >
								<label>Votre e-mail <span class="obligatoire">*</span></label>
								<input type="text"  name="I_Mail" value="[IF [!Reset!]=][!I_Mail!][/IF]" tabindex="25" [IF [!I_Mail_Error!]]class="Error"[/IF]/>				
							</div>
							<div class="LigneForm12" >
								<label>*Champs obligatoires.</label>
								
							</div>
						</div>
					[/IF]
					// Questions
					[!Pair:=0!]
					[COUNT [!Chemin!]/Etape/[!Etp::Id!]/Question/Publier=1|NbQst]
					[STORPROC [!Chemin!]/Etape/[!Etp::Id!]/Question/Publier=1|Qst|||Ordre|ASC]
						<div class="UneQuestion[IF [!Pair!]=1||[!NbQst!]=1]Pair[/IF]" style="width:[IF [!NbQst!]>1]45[ELSE]100[/IF]%;">
							[IF [!Pair!]=1][!Pair:=0!][ELSE][!Pair:=1!][/IF]
							[IF [!Qst::AfficheTitre!]]<div class="TitreQst">[!Qst::Titre!]</div>[/IF]
							
							// Choix
							[COUNT [!Chemin!]/Etape/[!Etp::Id!]/Question/[!Qst::Id!]/Choix|NbChx]
							[!Taille:=100!]
							[IF [!NbChx!]<4][!Taille:=30!][/IF]
							[IF [!NbChx!]<3][!Taille:=45!][/IF]
							
							[STORPROC [!Chemin!]/Etape/[!Etp::Id!]/Question/[!Qst::Id!]/Choix|Chx|||Id|ASC]
								<div class="UnChoix" style="width:[!Taille!]%;">
									[IF [!Chx::Icone!]]<div class="IconeRep"><img src="/[!Chx::Icone!]" alt="[!Qst::Titre!]" title="[!Qst::Titre!]" /></div>[/IF]
									<div class="TitreRep">[!Chx::Titre!]</div>
									[IF [!Chx::Type!]=Radio]
										<input type="radio" name="Rq_[!Qst::Id!]" [IF [!Pos!]=1]checked="checked"[/IF] value="[!Chx::Id!]">
									[/IF]
									[IF [!Chx::Description!]]<div class="DescRep">[!Chx::Description!]</div>[/IF]
								</div>
							[/STORPROC]
						</div>
					[/STORPROC]
				</div>
			[/LIMIT]
		[/STORPROC]
		<div class="ADroite">
			//<div class="Operations">
				//<label>Merci de résoudre cette opération </label>
				//[IF [!Nb1!]=]
					//[!Nb1:=[!Utils::random(5)!]!]
					//[!Nb1+=4!]
					//[!Nb2:=[!Utils::random(4)!]!]
					//[IF [!Utils::random(1)!]][!Op:=-!][ELSE][!Op:=+!][/IF]
					//[!Tot:=[!Nb1!]!]
					//[IF [!Op!]=-][!Tot-=[!Nb2!]!][ELSE][!Tot+=[!Nb2!]!][/IF]
					//[!hash:=[!Utils::md5([!Tot!])!]!]
				//[/IF]
				//<input type="text" readonly="readonly"  name="Nb1"    value="[!Nb1!]" style="width:25px;float:none;text-align:center;" />
				//<input type="text"                      name="Op"     value="[!Op!]"  style="width:15px;float:none;text-align:center;"  />
				//<input type="text" readonly="readonly"  name="Nb2"    value="[!Nb2!]" style="width:25px;float:none;text-align:center;"/>
				//= <input type="text"                    name="Result" value="[!Result!]" style="width:25px;float:none;text-align:center;" [IF [!C_Code_Error!]] class="Error" [/IF] />
				//<input type="hidden" name="hash" value="[!hash!]" />
				
			//</div>
					
			<div class="Buttons">
				<button type="submit">Valider</button>
				<input type="hidden" name="I_DevisEnligne" value="1" />
				<input type="hidden" name="I_Simulateur" value="[!Sim::Url!]" />
				<input type="hidden" name="Redirect" value="[!Lien!]" />
				//<div class="Mentions">(votre pré-devis va vous être envoyé par email)</div>
			</div>
		</div>
		
	</div>
</form>


	
