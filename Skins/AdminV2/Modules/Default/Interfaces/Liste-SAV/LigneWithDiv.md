	
[IF [!Behaviour!]=Integrated]
    [!goTo:=[!Query!]?LookAtObj=[!TypeEnf!]&LookAtNum=[!Ob::Id!]!]
    [!goToNude:=[!Query!].htm?LookAtObj=[!TypeEnf!]&LookAtNum=[!Ob::Id!]!]
[ELSE]
    [!goTo:=[!Ob::myUrl!]!]
[/IF]
[IF [!Type!]!=Explorer&&[!Type!]!=Select]
    //ID
    <td>
	<a href="/[!goTo!]" style="width:100%;text-align:right;">&nbsp;
	    [!Ob::Id!]
	    [IF [!Ob::getIcon!]!=]<img src="[!Ob::getIcon!]" style="width:14px;height:14px;margin:-2px 0 -2px 0"/>[/IF]
	</a>
    </td>

			//LIGNE DROITE
			[IF [!Type!]!=Explorer&&[!Type!]!=Select]
				//ACTIONS
				[BLOC Rounded||width:70px;float:right;]
				    [IF [!Behaviour!]=Integrated]
					<a href="/[!Query!]?ModifObj=[!TypeEnf!]&ModifNum=[!Ob::Id!]" style="float:left;"><img src="/Skins/AdminV2/Img/application_edit.png" class="ListeMiniImg"/></a>
					<a href="/[!Query!]?SupprObj=[!TypeEnf!]&SupprNum=[!Ob::Id!]" style="float:left;"><img src="/Skins/AdminV2/Img/delete.png" class="ListeMiniImg"/></a>
				    [ELSE]
					<a href="/[!Ob::getUrl!]/Modifier" style="float:left;"><img src="/Skins/AdminV2/Img/application_edit.png" class="ListeMiniImg"/></a>
					<a href="/[!Ob::getUrl!]/Supprimer" style="float:left;"><img src="/Skins/AdminV2/Img/delete.png" class="ListeMiniImg"/></a>
                                    [/IF]
				[/BLOC]
			[/IF]
			[IF [!Type!]=Full]
				//SELECTION
				[BLOC Rounded||width:25px;float:right;]
					<input type="checkbox" class="listCheckBox ListeCheckbox" name="Liste[!Ob::ObjectType!][]" value="[!Ob::Id!]"/>
				[/BLOC]
				//ORDRE
				[BLOC Rounded||width:80px;float:right;|min-height:11px;]
					<a href="/Systeme/User/[!Ob::uid!]" style="width:auto;display:inline;">[!Ob::uid!]</a> / <a href="/Systeme/Group/[!Ob::uid!]" style="width:auto;display:inline;">[!Ob::gid!]</a>
				[/BLOC]
				//DATE CREATION
				[BLOC Rounded||width:120px;float:right;]
					&nbsp;[DATE d/m/Y H:i][!Ob::tmsCreate!][/DATE]
				[/BLOC]
				//DATE MODIFICATION
				[BLOC Rounded||width:120px;float:right;]
					&nbsp;[DATEd/m/Y H:i][!Ob::tmsEdit!][/DATE]
				[/BLOC]
			[/IF]
			[SWITCH [!Type!]|=]
				[CASE Explorer]
					[BLOC Rounded||width:60px;float:left;]
						[!Ob::Id!]
					[/BLOC]
					[BLOC Rounded||float:left;width:25px;]
						[IF [!Inter!]=radio]
							<input type="radio" name="[!Var!]_Multi[]" value="[!Ob::myUrl!]" class="ListeCheckbox" [IF [!Check!]]checked="checked"[/IF]/>
						[ELSE]
							<input type="checkbox" name="[!Var!]_Multi[]" value="[!Ob::myUrl!]" class="ListeCheckbox" [IF [!Check!]]checked="checked"[/IF]/>
						[/IF]
						[IF [!Test!]]
							<input type="hidden" name="[!Var!]Test[]" value="[!Ob::Id!]" />
						[/IF]
					[/BLOC]
					[BLOC Rounded||float:left;width:333px;;]
						<input type="submit" name="Requete" value="[!Ob::myUrl!]" class="ListeSubmit" src=""/>
						<span [IF [!Ob::isCurrent!]]style="font-weight:bold;font-size:11px;"[/IF] style="position:absolute;top:3px;left:25px;">&nbsp;[MODULE Systeme/Interfaces/AffichPropValue?Obj=[!Ob!]] </span>
					[/BLOC]
				[/CASE]
				[CASE Select]
					//ID
					[BLOC Rounded||float:left;]
						[!Ob::Id!]
					[/BLOC]
					[!LargeurGauche:=60!]
					[BLOC Rounded||width:25px;]
						[IF [!Inter!]=radio]
							<input type="radio" name="[!Var!][]" value="[!Ob::[!OutVar!]!]" class="ListeCheckbox" [IF [!Check!]]checked="checked"[/IF]/>
						[ELSE]
							<input type="checkbox" name="[!Var!][]" value="[!Ob::[!OutVar!]!]" class="ListeCheckbox" [IF [!Check!]]checked="checked"[/IF]/>
						[/IF]
						[IF [!Test!]]
							<input type="hidden" name="[!Var!]Test[]" value="[!Ob::[!OutVar!]!]" />
						[/IF]
					[/BLOC]
					[BLOC Rounded||width:333px;|height:auto;]
						<span [IF [!Ob::isCurrent!]]style="font-weight:bold;font-size:11px;"[/IF]>&nbsp;[MODULE Systeme/Interfaces/AffichPropValue?Obj=[!Ob!]] 
						( 
						[STORPROC [!Ob::SearchOrder()!]|Prop]
							[LIMIT 1|[!NbChamp!]]
								[!Ob::[!Prop::Nom!]!]
							[/LIMIT]
						[/STORPROC]
						)
						</span>
					[/BLOC]
				[/CASE]
				[DEFAULT]
					[BLOC Rounded||width:333px;;|height:auto;]
					      [IF [!Links!]=Ajax]
						<a href="/[!goTo!]" class="internLink" rel="/Systeme/Interfaces/Etat?QueryObj=[!Ob::myUrl!]&Query=[!Ob::myUrl!]::/[!goTo!]::Data">
					      [ELSE]
						<a href="/[!goTo!]" [IF [!Type!]=Mini]class="makePopup"[/IF] id="[!Ob::Id!][!Ob::ObjectType!]Line" rel="/Systeme/Interfaces/Etat/Popup.htm?QueryObj=[!Query!]">
					      [/IF]
							<span [IF [!Ob::isCurrent!]]style="font-weight:bold;font-size:11px;"[/IF]>&nbsp;[MODULE Systeme/Interfaces/AffichPropValue?Obj=[!Ob!]] 
							[IF [!NbChamp!]>0]
							( 
							[STORPROC [!Ob::SearchOrder()!]|Prop|1|[!NbChamp!]]
								[SWITCH [!Prop::Type!]|=]
									[CASE image][/CASE]
									[CASE text][/CASE]
									[CASE file][/CASE]
									[CASE date]
										[DATE d/m/Y H:i][!Ob::[!Prop::Nom!]!][/DATE]
									[/CASE]
									[DEFAULT]
										[!Ob::[!Prop::Nom!]!]
									[/DEFAULT]
								[/SWITCH]
							[/STORPROC]
							)
							[/IF]
							</span>
						</a>
					[/BLOC]
				[/DEFAULT]
			[/SWITCH]
		</div>
