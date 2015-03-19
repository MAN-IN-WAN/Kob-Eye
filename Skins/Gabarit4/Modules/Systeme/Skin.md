<div id="Skin" style="padding:5px;">
	<h3>Templates</h3>
	<form action="#Skin" method="post" >
		[IF [!FormSkin_Valid!]=OK]
			[!U:=[!Systeme::User!]!]
			[METHOD U|Set][PARAM]Skin[/PARAM][PARAM][!SkinChange!][/PARAM][/METHOD]
			[METHOD U|Save][/METHOD]
			[REDIRECT][!Lien!][/REDIRECT]
		[/IF]
		<div class="LigneForm">
			<select name="SkinChange" onChange="this.submit();" style="width:150px;">
				[STORPROC Explorateur/Skins/Dossier|F]
				[IF [!F::Nom!]~Admin||[!F::Nom!]~Login][ELSE]
				<option value="[!F::Nom!]" [IF [!Systeme::User::Skin!]=[!F::Nom!]]  selected="selected"[/IF]>[!F::Nom!]</option>
				[/IF]
				[/STORPROC]
			</select>
			<input type="submit" name="FormSkin_Valid" value="OK" />
		</div>
	</form>
</div>

