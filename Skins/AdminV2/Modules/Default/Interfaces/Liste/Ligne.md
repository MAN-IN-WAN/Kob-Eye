[!goTo:=[!Module::Actuel::Nom!]!]
[INFO [!Chemin!]|Q]
[STORPROC [!Q::Historique!]|Histo|0|[!Q::NbHisto:-1!]]
	[!goTo+=/[!Histo::DataSource!]!]
	[!goTo+=/[!Histo::Value!]!]
[/STORPROC]
[!goTo+=/[!Ob::ObjectType!]!]
[!goTo+=/[!Ob::Id!]!]
<td class="NumCol">
	[IF [!Ob::getIcon!]!=]<img src="[!Ob::getIcon!]" style="width:14px;height:14px;margin:-2px 0 -2px 0;float:left;"/>[/IF]
    <a href="/[!goTo!]" style="width:100%;text-align:right;">&nbsp;
	//RECHERCHE D UN CHAMPS DE TYPE ORDER
	[STORPROC [!Ob::getOrderField()!]|OF]
	    [!Ob::[!Key!]!]
	    [NORESULT]
		[!Ob::Id!]
	    [/NORESULT]
	[/STORPROC]
    </a>
</td>
[IF [!Inter!]=radio&&[!Type!]=MultiSelect]
    <td class="SelectCol">
    		<input type="radio" name="[!Var!][]" value="[!Ob::Id!]" class="ListeCheckbox" [IF [!Check!]]checked="checked" [/IF]/>
		</td>		
[/IF]
[IF [!Inter!]=checkbox&&[!Type!]=MultiSelect]	
<td class="SelectCol">
  <input type="checkbox" name="[!Var!][]" value="[!Ob::Id!]" class="ListeCheckbox" [IF [!Check!]]checked="checked"[/IF]/>
</td>
[/IF]

[STORPROC [!Ob::SearchOrder!]|Prop|0|[!NbChamp!]]
	<td class="NomCol" width="[!60:/[!NbResult!]!]%">
		[IF [!Type!]=Full||[!Type!]=Mini||[!Type!]=Col]
			<a href="/[!goTo!]" [IF [!Behaviour!]=Integrated]class="makePopup"[/IF] id="[!Ob::Id!][!Ob::ObjectType!]Line" rel="/Systeme/Interfaces/Etat/Popup.htm?QueryObj=[!Ob::getUrl!]::[!Ob::getUrl!]">
		[/IF]
	    <span [IF [!Ob::isCurrent!]]style="font-weight:bold;font-size:11px;"[/IF]>
			[MODULE Systeme/Interfaces/AffichPropValue?Obj=[!Ob!]&OutVar=[!OutVar!]&Type=[!Type!]&Prop=[!Prop!]]
	    </span>
	    [IF [!Type!]=Full||[!Type!]=Mini||[!Type!]=Col]</a>[/IF]
	</td>
[/STORPROC]
[IF [!Type!]!=Mini&&[!Type!]!=Col]
<td class="CreaCol">
    &nbsp;[DATE d/m/Y H:i][!Ob::tmsCreate!][/DATE]
</td>
<td class="ModifCol">
    &nbsp;[DATEd/m/Y H:i][!Ob::tmsEdit!][/DATE]
</td>
<td class="UsersCol">
	[IF [!Type!]=Full||[!Type!]=Mini||[!Type!]=Col]
    <a href="/Systeme/User/[!Ob::uid!]" style="width:auto;display:inline;">[!Ob::uid!]</a> / <a href="/Systeme/Group/[!Ob::uid!]" style="width:auto;display:inline;">[/IF][!Ob::gid!][IF [!Type!]=Full||[!Type!]=Mini||[!Type!]=Col]</a>[/IF]
</td>
[/IF]
[IF [!Type!]=Full||[!Type!]=Mini||[!Type!]=Col]
    //ACTIONS
    <td class="ActionsCol">
	<a href="/[!goTo!]" class="makePopup" style="float:right;margin-top:-2px;" id="[!Ob::Id!][!Ob::ObjectType!]Line" rel="/Systeme/Interfaces/Etat/Popup.htm?QueryObj=[!Ob::getUrl!]::/[!Query!].htm"><img src="/Skins/AdminV2/Img/submenuArrow.png" class="ListeMiniImg"/></a>
	[IF [!Behaviour!]=Integrated]
		<a href="/[!Query!]?ModifObj=[!TypeEnf!]&ModifNum=[!Ob::Id!]" class="makePopup" rel="/Systeme/Interfaces/Formulaire/Popup.htm?Action=Modifier&QueryObj=[!Ob::getUrl!]::/[!Ob::getUrl!]/Modifier::true" style="float:left;"><img src="/Skins/AdminV2/Img/application_edit.png" class="ListeMiniImg"/></a>
		<a href="/[!Query!]/[!Ob::Id!]/Supprimer" rel="confirm" message="Attention! Vous allez supprimer l'objet.Etes vous sur de vouloir le supprimer ?" title="Suppression d'un élément" redirectUrl="/[!Query!].htm"><img src="/Skins/AdminV2/Img/delete.png" class="ListeMiniImg"/></a>
//	    <a href="/[!Query!]?SupprObj=[!TypeEnf!]&SupprNum=[!Ob::Id!]" class="makePopup"
//	       rel="/Systeme/Interfaces/Supprimer/Popup.htm?Action=Modifier&QueryObj=[!Ob::getUrl!]::/[!ob::getUrl!]/Supprimer::true"
//	style="float:left;"><img src="/Skins/AdminV2/Img/delete.png" class="ListeMiniImg"/></a>
	[ELSE]
		<a href="/[!Ob::getUrl!]/Modifier"><img src="/Skins/AdminV2/Img/application_edit.png" class="ListeMiniImg"/></a>
		<a href="/[!Query!]/[!Ob::Id!]/Supprimer" rel="confirm" message="Attention! Vous allez supprimer l'objet.Etes vous sur de vouloir le supprimer ?" title="Suppression d'un élément" redirectUrl="/[!Query!].htm"><img src="/Skins/AdminV2/Img/delete.png" class="ListeMiniImg"/></a>
	[/IF]
    </td>
    [IF [!Type!]=Full]
    <td class="InputCol">
	<input type="checkbox" class="listCheckBox ListeCheckbox" name="Liste[!Ob::ObjectType!][]" value="[!Ob::Id!]"/>
    </td>
    [/IF]
[/IF]

