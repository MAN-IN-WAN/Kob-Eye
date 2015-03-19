	
[IF [!Behaviour!]=Integrated]
    [!goTo:=[!Query!]?LookAtObj=[!TypeEnf!]&LookAtNum=[!Ob::Id!]!]
    [!goToNude:=[!Query!].htm?LookAtObj=[!TypeEnf!]&LookAtNum=[!Ob::Id!]!]
[ELSE]
    [!goTo:=[!Module::Actuel::Nom!]!]
    [STORPROC [!Chemin!]|Q|0|1]
    [STORPROC [!Q::Historique!]|Histo]
    	      [!goTo+=/[!Histo::ObjectType!]!]
	      [!goTo+=/[!Histo::Id!]!]
    [/STORPROC]
    [/STORPROC]
    [!goTo+=/[!Ob::ObjectType!]!]
    [!goTo+=/[!Ob::Id!]!]
[/IF]
<td class="NumCol">
	[IF [!Ob::getIcon!]!=]<img src="[!Ob::getIcon!]" style="width:14px;height:14px;margin:-2px 0 -2px 0;float:left;"/>[/IF]
    <a href="/[!goTo!]" style="width:100%;text-align:right;">&nbsp;
	[!Ob::Id!]
    </a>
</td>
[IF [!Inter!]=radio&&[!Type!]=MultiSelect]
    <td class="SelectCol">
    		<input type="radio" name="[!Var!]" value="[!Ob::Id!]" class="ListeCheckbox" [IF [!Check!]]checked="checked" [/IF]/>
		</td>		
[/IF]
[IF [!Inter!]=checkbox&&[!Type!]=MultiSelect]	
<td class="SelectCol">
  <input type="checkbox" name="[!Var!][]" value="[!Ob::Id!]" class="ListeCheckbox" [IF [!Check!]]checked="checked"[/IF]/>
</td>
[/IF]

<td class="NomCol">
    [IF [!Links!]=Ajax&&[!Behaviour!]!=Integrated]
	<a href="/[!goTo!]" class="internLink" rel="/[!goTo!]/Props.htm::/[!goTo!]::Data">
    [ELSE]
	<a href="/[!goTo!]" [IF [!Behaviour!]=Integrated]class="makePopup"[/IF] id="[!Ob::Id!][!Ob::ObjectType!]Line" rel="/Systeme/Interfaces/Etat/Popup.htm?QueryObj=[!Ob::getUrl!]::[!Ob::getUrl!]">
    [/IF]
    <span [IF [!Ob::isCurrent!]]style="font-weight:bold;font-size:11px;"[/IF]>
	
	[MODULE Systeme/Interfaces/AffichPropValue?Obj=[!Ob!]&OutVar=[!OutVar!]&Type=[!Type!]] 
    </span>
    </a>
</td>
[IF [!Type!]!=Mini&&[!Type!]!=Col]
<td class="CreaCol">
    &nbsp;[DATE d/m/Y H:i][!Ob::tmsCreate!][/DATE]
</td>
<td class="ModifCol">
    &nbsp;[DATEd/m/Y H:i][!Ob::tmsEdit!][/DATE]
</td>
<td class="UsersCol">
    <a href="/Systeme/User/[!Ob::uid!]" style="width:auto;display:inline;">[!Ob::uid!]</a> / <a href="/Systeme/Group/[!Ob::uid!]" style="width:auto;display:inline;">[!Ob::gid!]</a>
</td>
[/IF]
[IF [!Type!]=Full||[!Type!]=Mini]
    //ACTIONS
    <td class="ActionsCol">
	<a href="/[!goTo!]" class="makePopup" style="float:right;margin-top:-2px;" id="[!Ob::Id!][!Ob::ObjectType!]Line" rel="/Systeme/Interfaces/Etat/Popup.htm?QueryObj=[!Ob::getUrl!]::/[!Query!].htm"><img src="/Skins/AdminV2/Img/submenuArrow.png" class="ListeMiniImg"/></a>
	[IF [!Behaviour!]=Integrated]
	    <a href="/[!Query!]?ModifObj=[!TypeEnf!]&ModifNum=[!Ob::Id!]" 
	       class="makePopup"
	       rel="/Systeme/Interfaces/Formulaire/Popup.htm?Action=Modifier&QueryObj=[!Ob::getUrl!]::/[!Ob::getUrl!]/Modifier::true"
	       style="float:left;"><img src="/Skins/AdminV2/Img/application_edit.png" class="ListeMiniImg"/></a>
	    <a href="/[!Query!]?SupprObj=[!TypeEnf!]&SupprNum=[!Ob::Id!]" class="makePopup"
	       rel="/Systeme/Interfaces/Supprimer/Popup.htm?Action=Modifier&QueryObj=[!Ob::getUrl!]::/[!ob::getUrl!]/Supprimer::true"
	style="float:left;"><img src="/Skins/AdminV2/Img/delete.png" class="ListeMiniImg"/></a>
	[ELSE]
	    <a href="/[!Ob::getUrl!]/Modifier"><img src="/Skins/AdminV2/Img/application_edit.png" class="ListeMiniImg"/></a>
	    <a href="/[!Ob::getUrl!]/Supprimer"><img src="/Skins/AdminV2/Img/delete.png" class="ListeMiniImg"/></a>
	[/IF]
    </td>
    [IF [!Type!]=Full]
    <td class="InputCol">
	<input type="checkbox" class="listCheckBox ListeCheckbox" name="Liste[!Ob::ObjectType!][]" value="[!Ob::Id!]"/>
    </td>
    [/IF]
[/IF]

