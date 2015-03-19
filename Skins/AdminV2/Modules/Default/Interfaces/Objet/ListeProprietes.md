<span class="unePropriete">
	<span class="NomPropriete">[!Prop::description!] : </span>
	<span style="margin-left:20%";>
	[SWITCH [!Prop::Type!]|=]
		[CASE password]
			<input type="text" class="InvisibleField" name="Form_[!Prop::Nom!]" value="*******" size="40">
		[/CASE]
		[CASE varchar]
			<input type="text" class="InvisibleField" name="Form_[!Prop::Nom!]" value="[!Prop::Valeur!]" size="40">
		[/CASE]
		[CASE text]
			<TEXTAREA COLS="50" ROWS="10" class="InvisibleField" name="Form_[!Prop::Nom!]">[!Prop::Valeur!]</TEXTAREA>
		[/CASE]
		[CASE boolean]
			//[IF [!Prop::Valeur!]="1"]
				<input type="radio" name="Form_[!Prop::Nom!]" value="1" CHECKED>Oui
				<input type="radio" name="Form_[!Prop::Nom!]" value="0">Non
			//[ELSE]
			//	<input type="radio" name="Form_[!Prop::Nom!]" value="1">Oui
			//	<input type="radio" name="Form_[!Prop::Nom!]" value="0" CHECKED>Non
			//[/IF]
		[/CASE]
		[DEFAULT]
			<input type="text" class="InvisibleField" name="Form_[!Prop::Nom!]" value="[!Prop::Valeur!]" size="40">
		[/DEFAULT]
	[/SWITCH]
	</span>
</span>