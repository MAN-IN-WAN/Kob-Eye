[BLOC Rounded||]
	[IF [!Type!]!=Full&&[!Type!]!=Mini]
		[IF [!Inter!]=checkbox]
			<input type="checkbox" value="[!PrefixeVar!][!Objet::Id!]" name="[IF [!Var!]=][!Prefixe!][!TypeEnf!][ELSE][!Var!]_Multi[/IF][]" style="float:left;" 
			[IF [!Objet::Id!]=[!Disable!]]disabled="disabled"[/IF] [IF [!Ch!]]checked="checked"[/IF] />
			[IF [!Test!]]
				<input type="hidden" name="[!Var!]Test[]" value="[!Objet::Id!]" />
			[/IF]
		[ELSE]
			<input type="radio" value="[!PrefixeVar!][!Objet::Id!]" name="[IF [!Var!]=][!Prefixe!][!TypeEnf!][ELSE][!Var!]_Multi[/IF][]" style="float:left;" [STORPROC [!Check!]|Par][IF [!Ch!]]checked="checked"[/IF][/STORPROC][IF [!Objet::Id!]=[!Disable!]]disabled="disabled"[/IF] />
		[/IF]
	[/IF]
	[IF [!Type!]!=]<a href="/[!Objet::getUrl!]" style="float:left;[IF [!Objet::isCurrent!]==2]background:#cdcdcd;[/IF][IF [!Select!]]font-weight:bold;font-size:11px;[/IF]">[/IF]
	[IF [!Objet::getIcon!]!=]<img src="[!Objet::getIcon!]" style="width:16px;height:16px;"/>[/IF]
	[SUBSTR 30][!Objet::getFirstSearchOrder!][/SUBSTR]
	[IF [!Type!]!=]</a>[/IF]
	[IF [!Type!]!=]
	//<a href="/[!Objet::getUrl!]/Supprimer" style="float:right;"><img src="/Skins/AdminV2/Img/delete.png" /></a>
	<a href="/[!Objet::getUrl!]/Supprimer" rel="confirm" message="Attention! Vous allez supprimer l'objet [MODULE Systeme/Interfaces/AffichPropValue?Obj=[!Objet!]].Etes vous sur de vouloir le supprimer ?" title="Suppression d'un élément" redirectUrl="/[!Query!].htm" style="float:right;"><img src="/Skins/AdminV2/Img/delete.png"/></a>
	<a href="/[!Objet::getUrl!]/Modifier" style="float:right;"><img src="/Skins/AdminV2/Img/application_edit.png" /></a>
	[/IF]
[/BLOC]
