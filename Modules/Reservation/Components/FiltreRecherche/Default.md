// Recherche de spectacles
<div class="[!NOMDIV!]">
	<div class="TitreDiv"><h2>[!TITREBLOC!]</h2></div>
	<div class="ContenuColonne">
		<form class="RechercheSpectacle" method="post" action="/[!Systeme::getMenu(Reservation/Spectacle)!]" >
			<div class="LigneForm">
				<label>Sorties</label>
				//<input type="text" name="Spec_Nom" value="[IF [!Spec_Nom!]][!Spec_Nom!][ELSE]Tapez le nom du spectacle...[/IF]" onclick="this.value='';" />
				<input name="SSortie" value="[IF [!SSortie!]=]Sorties[ELSE][!SSortie!][/IF]" onclick="this.value=''">
			</div>
			<div class="LigneForm Pair">
				<label>Lieu</label>
				//<input type="text" name="Spec_Localisation" value="[IF [!Spec_Localisation!]][!Spec_Localisation!][ELSE]Tapez le nom de votre ville...[/IF]" onclick="this.value='';" />
				<input name="LLieu" value="[IF [!LLieu!]=||[!LLieu!]=Lieu]Lieu[ELSE][!LLieu!][/IF]" onclick="this.value=''" >
			</div>
			<div class="LigneForm">
				<label>Date</label>
				<input name="JJour" value="[IF [!JJour!]=]__[ELSE][!JJour!][/IF]" size="2" onclick="this.value=''" style="width:20px;" > / <input name="MMois" value="[IF [!MMois!]=]__[ELSE][!MMois!][/IF]" size="2" onclick="this.value=''" style="width:20px;" > / <input name="AAn" value="[IF [!AAn!]=]__[ELSE][!AAn!][/IF]" size="4" onclick="this.value=''" style="width:20px;" >
			</div>
			<div class="LigneForm Pair" >
				<div class="col1">
					<label style="padding-top:3px;">Genre</label>
					<select name="GGenre">
						<option value="" [IF [!GGenre!]=] selected="selected"[/IF]></option>
						[STORPROC Reservation/Genre|Ge]
							<option value="[!Ge::Nom!]" [IF [!GGenre!]=[!Ge::Nom!]] selected="selected" [/IF]>[!Ge::Nom!]</option>
						[/STORPROC]			
					</select>
				</div>
				<div class="col2">
					<label style="padding-top:3px;">Public</label>
					<select name="PPublic">
						<option value="" [IF [!PPublic!]=] selected="selected"[/IF]></option>
						<option value="Adulte" [IF [!PPublic!]=Adulte] selected="selected" [/IF]>Adulte</option>
						<option value="Adulte et adolescent" [IF [!PPublic!]=Adulte et adolescent] selected="selected" [/IF]>Adulte et adolescent</option>
						<option value="Enfant" [IF [!PPublic!]=Enfant] selected="selected" [/IF]>Enfant</option>
						<option value="Tout public" [IF [!PPublic!]=Tout public] selected="selected" [/IF]>Tout public</option>
					</select>
				</div>
			</div>
			<input type="submit" name="[!TEXTELIEN!]" class="Rechercher" value="[!TEXTELIEN!]">
			<input type="hidden" name="RechercheOk" value="RecOk">
		</form>
	</div>
</div>