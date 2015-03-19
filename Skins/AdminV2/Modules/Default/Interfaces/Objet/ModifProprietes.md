	<td class="nomPropriete">[!Prop::Nom!][IF [!Prop::searchOrder!]=Herit] (h&eacute;rit&eacute;e)[/IF] : </td>
	<td class="valeurPropriete">
		
		[SWITCH [!Prop::Type!]|=]
			[CASE password]
				<input type="text" class="Champ" name="Form_[!Prop::Nom!]" value="*******" size="40">
			[/CASE]
			[CASE varchar]
				<input type="text" class="Champ" name="Form_[!Prop::Nom!]" value="[!Prop::Valeur!]" size="40">
			[/CASE]
			[CASE alias]
				<input type="text" class="Champ" name="Form_[!Prop::Nom!]" value="[!Prop::Valeur!]" size="40">
			[/CASE]
			[CASE link]
				<input type="text" class="Champ" name="Form_[!Prop::Nom!]" value="[!Prop::Valeur!]" size="40">
			[/CASE]
			[CASE VARCHAR]
				<input type="text" class="Champ" name="Form_[!Prop::Nom!]" value="[!Prop::Valeur!]" size="40">
			[/CASE]
			[CASE titre]
				<input type="text" class="Champ" name="Form_[!Prop::Nom!]" value="[!Prop::Valeur!]" size="40">
			[/CASE]
			[CASE url]
				<input type="text" class="Champ" name="Form_[!Prop::Nom!]" value="[!Prop::Valeur!]" size="40">
			[/CASE]
			[CASE int]
				<input type="text" class="Champ" name="Form_[!Prop::Nom!]" value="[!Prop::Valeur!]" size="10">
			[/CASE]
			[CASE INT]
				<input type="text" class="Champ" name="Form_[!Prop::Nom!]" value="[!Prop::Valeur!]" size="10">
			[/CASE]
			[CASE private]
				<input type="text" class="Champ" name="Form_[!Prop::Nom!]" value="[!Prop::Valeur!]" style="background-color:#FFCCCC" size="40">
			[/CASE]
			[CASE Order]
				<input type="text" class="Champ" name="Form_[!Prop::Nom!]" value="[!Prop::Valeur!]" size="5">
			[/CASE]
			[CASE txt]
				<TEXTAREA COLS="50" ROWS="10" class="Champ" name="Form_[!Prop::Nom!]">[!Prop::Valeur!]</TEXTAREA>
			[/CASE]
			[CASE text]
				<TEXTAREA COLS="50" ROWS="10" class="Champ" name="Form_[!Prop::Nom!]">[!Prop::Valeur!]</TEXTAREA>
			[/CASE]
			[CASE file]
				<input type="text" class="Champ" name="Form_[!Prop::Nom!]" value="[!Prop::Valeur!]" size="41" >
				<input name="Form_[!Prop::Nom!]_Upload"  type="file" size="40" style="margin-left:5px;display:none;"/>
				<a id="[!Prop::Nom!]" href="#" onClick='if(document.frm.Form_[!Prop::Nom!]_Upload.style.display=="none"){
					document.frm.Form_[!Prop::Nom!]_Upload.style.display="block";
					document.getElementById("[!Prop::Nom!]").innerHTML = "Donner une adresse sur le serveur";
					}else{
					document.getElementById("[!Prop::Nom!]").innerHTML = "Envoyer un fichier";
					document.frm.Form_[!Prop::Nom!]_Upload.value = "";
					document.frm.Form_[!Prop::Nom!]_Upload.style.display="none"
					}
					'>Envoyer un fichier</a>
			[/CASE]
			[CASE boolean]
				[IF [!Prop::Valeur!]=EMPTY]
					[IF [!Prop::Default!]=1]
					<input type="radio" name="Form_[!Prop::Nom!]" value="1" CHECKED>Oui
						<input type="radio" name="Form_[!Prop::Nom!]" value="0">Non
					[ELSE]
						<input type="radio" name="Form_[!Prop::Nom!]" value="1">Oui
						<input type="radio" name="Form_[!Prop::Nom!]" value="0" CHECKED>Non
					[/IF]
				[ELSE]
					[IF [!Prop::Valeur!]=1]
						<input type="radio" name="Form_[!Prop::Nom!]" value="1" CHECKED>Oui
						<input type="radio" name="Form_[!Prop::Nom!]" value="0">Non
					[ELSE]
						<input type="radio" name="Form_[!Prop::Nom!]" value="1">Oui
						<input type="radio" name="Form_[!Prop::Nom!]" value="0" CHECKED>Non
					[/IF]
				[/IF]
			[/CASE]
			[DEFAULT]
				<input type="text" class="Champ" name="Form_[!Prop::Nom!]" value="[!Prop::Valeur!]" size="40">
			[/DEFAULT]
		[/SWITCH]</td>