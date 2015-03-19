[IF [!Lot!]]
	[STORPROC ParcImmobilier/TypeLogement/Lot/[!Lot!]|TL|0|1]
		[STORPROC ParcImmobilier/Residence/TypeLogement/[!TL::Id!]|R]
			[STORPROC ParcImmobilier/Ville/Residence/[!R::Id!]|V|0|1][/STORPROC]
			[STORPROC ParcImmobilier/Residence/[!R::Id!]/Donnee/Type=Perspective|D|0|1][/STORPROC]
		[/STORPROC]
	[/STORPROC]

   	<div id="ColonneResidence">
		<div class="Interieur">
			<div class="Visuel"><img src="[!Domaine!]/[!D::URL!].mini.190x160.jpg" alt="[!R::Titre!]" title="[!R::Titre!]" /></div>
			<div class="NomResidence">[!R::Titre!]</div>
			<div class="Ville">[SUBSTR 2][!V::CodePostal!][/SUBSTR] - [!V::Nom!]</div>
			<div class="Livraison">[!R::DateLivraison!]</div>
			<div class="Contenu">[!R::Descriptif!]</div>
			<div class="Pictos">
				[STORPROC ParcImmobilier/PictoResidence/Residence/[!R::Id!]|PR]
					<img src="/[!PR::Picto!]" alt="[!PR::Titre!]" title="[!PR::Titre!]" />
				[/STORPROC]
			</div>
		</div>
	</div>
	
[ELSE]

	<div id="RecherchePrescripteurs">
		<form action="/[!Systeme::getMenu(ParcImmobilier/Residence)!]" method="get" >
			<div class="TitreBloc" id="RecherchePrescripteursTitre">Recherche</div>
			<div class="LigneForm LigneRecherche">
				<select name="Reference"  >
					<option value="">Choisir une référénce</option>
					[IF [!Systeme::User::Login!]!=CommercialAdm]
						[OBJ ParcImmobilier|Residence|ModelR]
						[STORPROC [!ModelR::getMesResidences()!]|R|0|30]
							<option value="[!R::Id!]" [IF [!Reference!]=[!R::Id!]]selected="selected"[/IF]>[!R::Titre!]</option>
						[/STORPROC]
					[ELSE]
						[STORPROC ParcImmobilier/Residence/Prescripteur=1|R|0|30|Id|DESC]
							<option value="[!R::Id!]" [IF [!Reference!]=[!R::Id!]]selected="selected"[/IF]>[!R::Titre!]</option>
						[/STORPROC]
					[/IF]
				</select>
				<button type="submit" class="BtnRecherche" ></button>
			</div>
		</form>
		<div class="TitreBloc" id="RecherchePrescripteursTitre2">Recherche avancée</div>
		<form action="/[!Systeme::getMenu(ParcImmobilier/Residence)!]" method="get" >
			<div class="LigneForm">
				<div class="LigneForm1">
					<label>Référence</label>
					<select name="ResidenceLot" >
						<option value="">Choisir une référénce</option>
						[IF [!Systeme::User::Login!]!=CommercialAdm]
							[OBJ ParcImmobilier|Residence|ModelR]
							[STORPROC [!ModelR::getMesResidences()!]|R|0|30]
								<option value="[!R::Id!]" [IF [!ResidenceLot!]=[!R::Id!]]selected="selected"[/IF]>[!R::Titre!]</option>
							[/STORPROC]
						[ELSE]
							[STORPROC ParcImmobilier/Residence/Prescripteur=1|R|0|30|Id|DESC]
								<option value="[!R::Id!]" [IF [!ResidenceLot!]=[!R::Id!]]selected="selected"[/IF]>[!R::Titre!]</option>
							[/STORPROC]
						[/IF]
					</select>
				</div>
				<div class="LigneForm2">
					<label>Département</label>
					<select name="Departement">
						<option value="">- Veuillez sélectionner -</option>
						[OBJ ParcImmobilier|Residence|R]
						[STORPROC ParcImmobilier/Departement|D|0|1000|Code|ASC]
	                        [COUNT [!R::getMesResidences([!D::Id!])!]|Cpt]
	                        [IF [!Cpt!]>0]
								<option value="[!D::Id!]" [IF [!Departement!]=[!D::Id!]]selected="selected"[/IF]>[!D::Code!] - [!D::Nom!]</option>
							[/IF]
						[/STORPROC]
					</select>
				</div>
			</div>
			<div class="LigneForm">
				<div class="LigneForm1">
					<label>Budget</label>
					<select name="Budget">
						<option value="0">- Veuillez sélectionner -</option>
						<option value="1" [IF [!Budget!]=1]selected="selected" [/IF]>Moins 120 000&euro;</option>
						<option value="2" [IF [!Budget!]=2]selected="selected" [/IF]>De 121 000&euro; &agrave; 160 000&euro;</option>
						<option value="3" [IF [!Budget!]=3]selected="selected" [/IF]>De 161 000&euro; &agrave; 190 000&euro;</option>		
						<option value="4" [IF [!Budget!]=4]selected="selected" [/IF]>De 191 000&euro; &agrave; 260 000&euro;</option>		
						<option value="5" [IF [!Budget!]=5]selected="selected" [/IF]>De 261 000&euro; &agrave; 350 000&euro;</option>		
						<option value="6" [IF [!Budget!]=6]selected="selected" [/IF]>Plus de 350 000&euro;</option>
					</select>
				</div>
				<div class="LigneForm2">
					<label>Ville</label>
					<select name="Ville">
						<option value="">- Veuillez sélectionner -</option>
						[OBJ ParcImmobilier|Residence|R]
						[STORPROC ParcImmobilier/Ville|V|0|1000|Nom|ASC]
	                        [COUNT [!R::getMesResidences(0,[!V::Id!])!]|Cpt]
	                        [IF [!Cpt!]>0]
									<option value="[!V::Id!]" [IF [!Ville!]=[!V::Id!]]selected="selected"[/IF]>[!V::Nom!]</option>
							[/IF]
						[/STORPROC]
					</select>
				</div>
			</div>
			<div class="LigneForm">
				<div class="LigneForm1">
					<label>Type d'appartement (N<span style="text-transform: lowercase">ombre pièces</span>)</label>
					<input class="checkbox" type="checkbox" name="Type[]" [STORPROC [!Type!]|T][IF [!T!]=1]checked="checked"[/IF][/STORPROC] value="1" />1 pce
					<input class="checkbox" type="checkbox" name="Type[]" [STORPROC [!Type!]|T][IF [!T!]=2]checked="checked"[/IF][/STORPROC] value="2" />2 pces
					<input class="checkbox" type="checkbox" name="Type[]" [STORPROC [!Type!]|T][IF [!T!]=3]checked="checked"[/IF][/STORPROC] value="3" />3 pces<br />
					<input class="checkbox" type="checkbox" name="Type[]" [STORPROC [!Type!]|T][IF [!T!]=4]checked="checked"[/IF][/STORPROC] value="4" />4 pces
					<input class="checkbox" type="checkbox" name="Type[]" [STORPROC [!Type!]|T][IF [!T!]=5]checked="checked"[/IF][/STORPROC] value="5" />5 pces et plus
					<input class="checkbox" type="checkbox" name="Type[]" [STORPROC [!Type!]|T][IF [!T!]=Ccial]checked="checked"[/IF][/STORPROC] value="Ccial" />CCiaux
				</div>
				<div class="LigneForm2">
					<label>Fiscalité</label>
					<select name="Fiscalite">
						<option value="">- Veuillez sélectionner -</option>
						<option value="Duflot" [IF [!Fiscalite!]=Duflot]selected="selected"[/IF]>Duflot</option>
//						<option value="Scellier" [IF [!Fiscalite!]=Scellier]selected="selected"[/IF]>Scellier</option>
					</select>
					<div><button type="submit" class="BtnRecherche" ></button></div>
				</div>
			</div>
//			<input type="hidden" name="Affichage" value="[!Affichage!]" />
			<input type="hidden" name="Affichage" value="Lots" />
		</form>
	</div>
[/IF]
