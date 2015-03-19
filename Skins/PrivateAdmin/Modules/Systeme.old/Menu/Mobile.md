<select class="selectnav" id="selectnav1">
<option selected="selected" value="">Quick Menu </option>
[STORPROC [!Systeme::Menus!]|M]
<option value="/[!M::Url!]">[!M::Titre!]</option>
	[STORPROC [!M::Menus!]|M2]
	<option value="/[!M::Url!]/[!M2::Url!]">[!M::Titre!] -> [!M2::Titre!]</option>
	[/STORPROC]
[/STORPROC]
<option value="/Systeme/Deconnexion">Déconnexion</option>
</select>
