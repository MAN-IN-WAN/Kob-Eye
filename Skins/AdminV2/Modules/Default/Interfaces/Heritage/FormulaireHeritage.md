<form method="POST">
<div class="Panel" style="position:absolute;top:0;bottom:50px;">
	[IF [!Action!]=="Modifier"]
	<h1>Modifier un h&eacute;ritage [!NomProp!] - [!TypeProp!] - [!EnfantProp!]</h1>
	[ELSE]
	<h1>Ajouter un h&eacute;ritage</h1>
	[/IF]
		<div class="Propriete">
			<div class="ProprieteTitre">Nom de la propri&eacute;t&eacute; :</div>
			<div class="ProprieteValeur">
				<input  class="Champ" type="text" name="NomProp" size="40" value="[!NomProp!]" />
			</div>
		</div>
		<div class="Propriete">
			<div class="ProprieteTitre">Type de la propri&eacute;t&eacute; : </div>
			<div class="ProprieteValeur">
				<select name="TypeProp" class="Champ">
					[IF [!TypeProp!]!=""]<option value="[!TypeProp!]" selected>[!TypeProp!]</option>[/IF]
					[STORPROC [!CONF::KEML::TYPE!]|Type]
						<option value="[!Type::TITLE!]">[!Type::TITLE!]</option>
					[/STORPROC]
				</select>
			</div>
		</div>
		<div class="Propriete">
			<div class="ProprieteTitre">Type d'enfant affect&eacute; :</div>
			<div class="ProprieteValeur">
				<select name="EnfantProp"  class="Champ">
					[IF [!EnfantProp!]!=""]<option value="[!EnfantProp!]" selected>[!EnfantProp!]</option>[/IF]
					[STORPROC [!Query!]::typesEnfantHeritage|Enf]
						<option value="[!Enf::Titre!]">[!Enf::Titre!]</option>
					[/STORPROC]
				</select>
			</div>
		</div>
		<div class="Propriete">
			<div class="ProprieteTitre">Groupe de propri&eacute;t&eacute; :</div>
			<div class="ProprieteValeurDouble">
				<select name="GroupProp"  class="Champ">
					[IF [!GroupProp!]!=""]<option value="[!GroupProp!]" selected>[!GroupProp!]</option>[/IF]
					[STORPROC [!Query!]::getHeritages|Enf]
						<option value="[!Enf::Group!]">[!Enf::Group!]</option>
					[/STORPROC]
				</select>
			</div>
			<div class="ProprieteValeurDouble">
				<input  class="Champ" type="text"  maxlength="255" name="GroupProp2" size="40" value="[!GroupProp2!]" />
			</div>
		</div>
		<div class="Propriete">
			<div class="ProprieteTitre">Profondeur d'action :</div>
			<div class="ProprieteValeur">
				<select name="LevelProp"  class="Champ">
					[IF [!LevelProp!]!=""]<option value="[!LevelProp!]" selected>[!LevelProp!]</option>
					[ELSE]<option value="">*</option>[/IF]
					[STORPROC 10|Enf|2|9]
						<option value="[!Key!]">[!Key!]</option>
					[/STORPROC]
				</select>
			</div>
		</div>
		<div class="Propriete">
			<div class="ProprieteTitre">Ordre :</div>
			<div class="ProprieteValeur">
				<input  class="Champ" type="text" name="OrderProp" size="40" value="[!OrderProp!]" />
			</div>
		</div>

</div>
<div class="Nav">
	<div class="boutonGauche">
		<form action="/[!Query!]/GestionHeritage" method="post" style="display:inline;">
		<INPUT type="submit" name="Annuler"  value="Annuler" />
		</form>
	</div>
	[IF [!Action!]=="Modifier"]
		<div class="boutonDroite">
			<INPUT type="submit" name="Modifier" value="Suivant" />
		</div>
	[ELSE]
		<div class="boutonDroite">
			<INPUT type="hidden" name="Action" value="Ajouter" />
			<INPUT type="submit" name="Ajouter" value="Suivant" />
		</div>
	[/IF]
</div>
</form>

