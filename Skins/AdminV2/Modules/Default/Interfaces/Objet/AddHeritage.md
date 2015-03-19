[TITLE]Admin Kob-Eye | Ajout d'un h&eacute;ritage[/TITLE]
<div class="PetiteBoiteDeDialogue">
	<div class="Titre">
		Ajouter un heritage
	</div>
	[BLOC Form]
		Nom de l'heritage:<br/>
		<input type="text" name="Heritage_NomPropriete" />
		<input type="hidden" name="FormSys_But" value="AH"/>
		<input type="hidden" name="FormSys_Module" value="[!Objet::Module!]"/>
		<input type="hidden" name="FormSys_Valid" value="AddHeritage"/>
		<input type="hidden" name="Heritage_ObjectType" value="[!Objet::ObjectType!]"/>
		<input type="hidden" name="Heritage_Id" value="[!Objet::Id!]"/>
		<input type="submit" class="BoutonBlanc" value="OK">
		<br/><br/>Type:
		<select name="Heritage_TypePropriete">
			<option value="VARCHAR">Chaine
			<option value="INT">Valeur numérique
			<option value="TEXT">Texte
			<option value="BOOLEAN">Oui/Non
		</select><br/><br/>
		Description:<br/>
		<textarea name="Heritage_DescPropriete"></textarea><br/><br/>
		Objets concern&eacute;s:
		<div style="border:1px solid black;margin-left:10px;margin-right:10px">
		[STORPROC [!Query!]::typesEnfant|Enfant]
			<input type="checkbox" name="Heritage_Enfant_[!Pos!]" value="[!Enfant::Titre!]"> [!Enfant::Titre!]
		[/STORPROC]
		</div>
	[/BLOC]
</div>