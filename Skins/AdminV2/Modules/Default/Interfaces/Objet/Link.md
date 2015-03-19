[TITLE]Admin Kob-Eye | Lier un objet[/TITLE]
<div class="PetiteBoiteDeDialogue" style="min-width:350px;min-height:400px;top:25%;">
	<div class="Titre">
		[IF [!Opt!]=Child]
		Lier - Enfant (<a href="?Action=Link&Opt=Parent">Parent?</a>)
		[ELSE]
		Lier - Parent (<a href="?Action=Link&Opt=Child">Enfant?</a>)
		[/IF]
	</div>
	[BLOC Form]
		<select name="Type" size="1">
			[IF [!Opt!]=Child]
				[STORPROC [!Lien!]::typesEnfant|Enfant]
					<OPTION VALUE="[!Enfant::Titre!]" [IF [!Type!]=[!Enfant::Titre!]] SELECTED[/IF]>[!Enfant::Titre!]</OPTION>
				[/STORPROC]

			[ELSE]
				[STORPROC [!Lien!]::typesParent|Parent]
					<OPTION VALUE="[!Parent::Titre!]" [IF [!Type!]=[!Parent::Titre!]] SELECTED[/IF]>[!Parent::Titre!]</OPTION>
				[/STORPROC]
			[/IF]
		</select>
		<input type="submit" class="BoutonBlanc" value="OK">
	[/BLOC]
	[BLOC Form]
		[IF [!Type!]=EMPTY]
		[ELSE]
			<div style="border-top:1px solid black;padding-top:10px;margin-top:10px;width:200px;">
			[BLOC Form]
				[IF [!Opt!]=Child]
					[BLOC Inputhidden|Form_ParentId|[!Objet::Id!]]
					[/BLOC]
					[BLOC Inputhidden|Form_ObjectParent|[!Objet::ObjectType!]]
					[/BLOC]
					<select name="Form_Objet" size="10" rows="60" style="min-width:195px">
						[STORPROC [!Module::Actuel::Nom!]/[!Type!]|Enfant]
							<OPTION VALUE="[!Enfant::Id!]">[!Enfant::getFirstSearchOrder!] ([!Enfant::Id!])</OPTION>
						[/STORPROC]
					</select>
					[BLOC Inputhidden|FormSys_Module|[!Objet::Module!]]
					[/BLOC]
					[BLOC Inputhidden|FormSys_ObjectType|[!Type!]]
					[/BLOC]
					[BLOC Inputhidden|FormSys_Valid|Link]
					[/BLOC]
					[BLOC Inputhidden|FormSys_But|AJ]
					[/BLOC]
				[ELSE]
				[STORPROC [!Lien!]::typesParent|Parent]
					[IF [!Type!]=[!Parent::Titre!]]
						[IF [!Parent::Card!]=1,1]
							<div class="InformationImportante">
								Attention, si vous donnez un nouveau parent de type [!Parent::ObjectType!] &egrave; a votre objet, l'ancien parent de ce type sera automatiquement &eacute;cras&eacute;.
							</div>
						[/IF]
						[IF [!Parent::Card!]=0,1]
							<div class="InformationImportante">
								Attention, si vous donnez un nouveau parent de type [!Parent::ObjectType!] &egrave; a votre objet, l'ancien parent de ce type sera automatiquement &eacute;cras&eacute;.
							</div>
						[/IF]
					[/IF]
				[/STORPROC]
				<select name="Form_ParentId" size="17" rows="60" style="min-width:195px">
					[STORPROC [!Module::Actuel::Nom!]/[!Type!]|Parent|0|1000]
						<OPTION VALUE="[!Parent::Id!]">[SUBSTR 29][!Parent::getFirstSearchOrder!][/SUBSTR] ([!Parent::Id!])</OPTION>
						[STORPROC [!Module::Actuel::Nom!]/[!Type!]/[!Parent::Id!]/[!Type!]|Parent2]
							<OPTION VALUE="[!Parent2::Id!]">&nbsp;&nbsp;[SUBSTR 27][!Parent2::getFirstSearchOrder!][/SUBSTR] ([!Parent2::Id!])</OPTION>
								[STORPROC [!Module::Actuel::Nom!]/[!Type!]/[!Parent2::Id!]/[!Type!]|Parent3]
								<OPTION VALUE="[!Parent3::Id!]">&nbsp;&nbsp;&nbsp;&nbsp;[SUBSTR 25][!Parent3::getFirstSearchOrder!][/SUBSTR] ([!Parent3::Id!])</OPTION>
									[STORPROC [!Module::Actuel::Nom!]/[!Type!]/[!Parent3::Id!]/[!Type!]|Parent4]
									<OPTION VALUE="[!Parent4::Id!]">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[SUBSTR 23][!Parent4::getFirstSearchOrder!][/SUBSTR] ([!Parent4::Id!])</OPTION>
									[/STORPROC]
								[/STORPROC]
						[/STORPROC]
					[/STORPROC]
				</select>
				[BLOC Inputhidden|Form_Objet|[!Objet::Id!]]
				[/BLOC]
				[BLOC Inputhidden|Form_ObjectParent|[!Type!]]
				[/BLOC]
				[BLOC Inputhidden|FormSys_Module|[!Objet::Module!]]
				[/BLOC]
				[BLOC Inputhidden|FormSys_ObjectType|[!Objet::ObjectType!]]
				[/BLOC]
				[BLOC Inputhidden|FormSys_Valid|Link]
				[/BLOC]
				[BLOC Inputhidden|FormSys_But|AJ]
				[/BLOC]
				[/IF]
				<input type="submit" class="BoutonBlanc" value="Lier">
			[/BLOC]
		[/IF]
		</div>
	[/BLOC]
</div>