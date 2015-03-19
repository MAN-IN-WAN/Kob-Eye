<h1>Dénonciation</h1>
[!Reset:=1!]
[IF [!Envoi!]!=EnvoiForm||[!C_Pr_Error!]]
	[IF [!C_Pr_Error!]][!Reset:=0!][/IF]
	<div class="DivFormDenonce">
		<form class="FormDenonce" enctype="multipart/form-data"  method="post"  action="/[!Lien!]">
			<div Class="Partie1">

				<div class="LigneForm">
					<div class="BoxCheck">
						<label >Monsieur</label>
						<input type="radio" name="C_Pr_Sexe"  value="M." checked="checked" style="border:none;width:auto"/>
					</div>
					<div class="BoxCheck">
						<label >Madame</label>
						<input type="radio" name="C_Pr_Sexe"  value="Mme" style="border:none;width:auto" [IF [!C_Pr_Sexe!]=Mme&&[!Reset!]=0] checked="checked" [/IF]/>
					</div>
					<div class="BoxCheck" style="width: 104px;">
						<label >Mademoiselle</label>
						<input type="radio" name="C_Pr_Sexe"  value="Mlle" style="border:none;width:auto" [IF [!C_Pr_Sexe!]=Mlle&&[!Reset!]=0] checked="checked" [/IF]/>
					</div>
				</div>
				<div class="LigneForm">
					<label>Nom <span class="Obligatoire">*</span></label>
					<input type="text" name="C_Pr_Nom"  value="[IF [!Reset!]=0][!C_Pr_Nom!][/IF]"  class="[IF [!C_Pr_Nom_Error!]]inputError[/IF]" onkeyup='this.value=this.value.toUpperCase()'  onblur='this.value=this.value.toUpperCase()' />
				</div>
				<div class="LigneForm">
					<label>Pr&eacute;nom <span class="Obligatoire">*</span></label>
						<input type="text" name="C_Pr_Prenom" value="[IF [!Reset!]=0][!C_Pr_Prenom!][/IF]" class="[IF [!C_Pr_Prenom_Error!]]inputError[/IF]" onkeyup='this.value=this.value.toUpperCase()' onblur='this.value=this.value.toUpperCase()' />
				</div>
				<div class="LigneForm">
					<label>Adresse</label>
					<input type="text" name="C_Pr_Adresse1" value="[IF [!Reset!]=0][!C_Pr_Adresse1!][/IF]" />
				</div>
				<div class="LigneForm">
					<label>(suite)</label>
					<input type="text" name="C_Pr_Adresse2" value="[IF [!Reset!]=0][!C_Pr_Adresse2!][/IF]" class="adresse2"/>
				</div>
				<div class="LigneForm">
					<label>Code Postal</label>
					<input type="text" name="C_Pr_CodePostal" value="[IF [!Reset!]=0][!C_Pr_CodePostal!][/IF]" class="Reduit" />
				</div>
				<div class="LigneForm">
					<label>Ville <span class="Obligatoire">*</span></label>
					<input type="text" name="C_Pr_Ville" value="[IF [!Reset!]=0][!C_Pr_Ville!][/IF]"  class="[IF [!C_Pr_Ville_Error!]]inputError[/IF]" onkeyup='this.value=this.value.toUpperCase()' onblur='this.value=this.value.toUpperCase()' />
				</div>
				<div class="LigneForm">
					<label>N&deg; de t&eacute;l&eacute;phone <span class="Obligatoire">*</span></label>
					<input type="text" name="C_Pr_Tel" value="[IF [!Reset!]=0][!C_Pr_Tel!][/IF]"  class="[IF [!C_Pr_Tel_Error!]]inputError[/IF] Reduit"/>
				</div>
				<div class="LigneForm">
					<label>N&deg; de t&eacute;l&eacute;phone 2</label>
					<input type="text" name="C_Pr_Tel2" value="[IF [!Reset!]=0][!C_Pr_Tel2!][/IF]"  class="Reduit"/>
				</div>
				<div class="LigneForm">
					<label>N&deg; de t&eacute;l&eacute;phone 3</label>
					<input type="text" name="C_Pr_Tel3" value="[IF [!Reset!]=0][!C_Pr_Tel3!][/IF]"  />
				</div>
				<div class="LigneForm">
					<label>N&deg; de Fax</label>
					<input type="text" name="C_Pr_Fax" value="[IF [!Reset!]=0][!C_Pr_Fax!][/IF]"  />
				</div>
				<div class="LigneForm">
					<label>Adresse e-mail</label>
					<input type="text" name="C_Pr_MailContact" value="[IF [!Reset!]=0][!C_Pr_MailContact!][/IF]"  class="[IF [!C_Pr_Mail_Error!]]inputError[/IF]" />
				</div>
			</div>
			<div Class="Partie2">
				<div class="LigneForm">
					<h3>Intéressé par les types suivants</h3>
				</div>
				<div class="LigneForm">
					<div class="BoxCheck">
						<label >Studio</label>
						<input type="checkbox" name="C_Pr_TypeSt"  value="St" [IF [!C_Pr_TypeSt!]&&[!Reset!]] checked="checked" [/IF] style="border:none;width:auto"/>
					</div>
					<div class="BoxCheck">
						<label >T1</label>
						<input type="checkbox" name="C_Pr_TypeT1"  value="T1" [IF [!C_Pr_TypeT1!]&&[!Reset!]] checked="checked" [/IF]style="border:none;width:auto"/>
					</div>
					<div class="BoxCheck">
						<label >T2</label>
						<input type="checkbox" name="C_Pr_TypeT2"  value="T2" [IF [!C_Pr_TypeT2!]&&[!Reset!]] checked="checked" [/IF] style="border:none;width:auto"/>
					</div>
					<div class="BoxCheck">
						<label >T3</label>
						<input type="checkbox" name="C_Pr_TypeT3"  value="T3" [IF [!C_Pr_TypeT3!]&&[!Reset!]] checked="checked" [/IF] style="border:none;width:auto"/>
					</div>
					<div class="BoxCheck">
						<label >T4</label>
						<input type="checkbox" name="C_Pr_TypeT4"  value="T4" [IF [!C_Pr_TypeT4!]&&[!Reset!]] checked="checked" [/IF] style="border:none;width:auto"/>
					</div>
					<div class="BoxCheck">
						<label >T5</label>
						<input type="checkbox" name="C_Pr_TypeT5"  value="T5" [IF [!C_Pr_TypeT5!]&&[!Reset!]] checked="checked" [/IF] style="border:none;width:auto"/>
					</div>
					<div class="BoxCheck">
						<label >Villa</label>
						<input type="checkbox" name="C_Pr_TypeVilla"  value="Villa" [IF [!C_Pr_TypeVilla!]&&[!Reset!]] checked="checked" [/IF] style="border:none;width:auto"/>
					</div>
					<div class="BoxCheck">
						<label >LC</label>
						<input type="checkbox" name="C_Pr_TypeLC"  value="LC" [IF [!C_Pr_TypeLC!]&&[!Reset!]] checked="checked" [/IF] style="border:none;width:auto"/>
					</div>
					<div class="BoxCheck">
						<label >Terrain</label>
						<input type="checkbox" name="C_Pr_TypeTerrain"  value="Terrain" [IF [!C_Pr_TypeTerrain!]&&[!Reset!]] checked="checked" [/IF] style="border:none;width:auto"/>
					</div>
				</div>
				<div class="LigneForm">
						<label>Surface</label>
						<input type="text" name="C_Pr_Surface" value="[IF [!Reset!]=0][!C_Pr_Surface!][/IF]"  />
				</div>
				<div class="LigneForm">
					<label>Ville</label>
					<input type="text" name="C_Pr_VilleRecherche" value="[IF [!Reset!]=0][!C_Pr_VilleRecherche!][/IF]"  />
				</div>
				<div class="LigneForm">
					<label>Quartier</label>
					<input type="text" name="C_Pr_Quartier" value="[IF [!Reset!]=0][!C_Pr_Quartier!][/IF] " />
				</div>
				<div class="LigneForm">
					<label>Résidence</label>
					<input type="text" name="C_Pr_Residence" value="[IF [!Reset!]=0][!C_Pr_Residence!][/IF]"  />
				</div>
				<div class="LigneForm">
					<label>Montant de l'investissement envisagé</label>
					<input type="text" name="C_Pr_Budget" value="[IF [!Reset!]=0][!C_Pr_Budget!][/IF]"  />
				</div>
				<div class="LigneForm">
					<label>Motif (R.P  R.S. INV.)</label>
					<input type="text" name="C_Pr_Motifs" value="[IF [!Reset!]=0][!C_Pr_Motifs!][/IF]"  />
				</div>
				<div class="LigneForm">
					<label>Date de livraison souhaitée</label>
					<input type="text" name="C_Pr_Livraison" value="[IF [!Reset!]=0][!C_Pr_Livraison!][/IF]"  />
				</div>
				<div class="LigneForm">
					<label >Autres Renseignements </label>
					<textarea cols="80" rows="8" name="C_Pr_AutreRenseignement" style="[IF [!C_Pr_AutreRenseignement_Error!]]background-color:#FFDE01;[/IF]" >[IF [!Reset!]=0][!C_Pr_AutreRenseignement!][/IF]</textarea>
				</div>
			</div>
			<div class="PartieBas">
				<div class="LigneForm" style="padding-left:10px;">
					Les champs marqu&eacute;s (<span class="obligatoire">*</span>) sont obligatoires.
				</div>
				<div class="LigneForm ">
					<input type="hidden" name="Envoi" value="EnvoiForm" />
					<input type="hidden" name="Affichage" value="Liste" />
					<div class="lienBtnCnt">
						<input type="submit" value="Envoyer">
					</div>
				</div>
			</div>
		</form>
	</div>
[/IF]

