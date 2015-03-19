[TITLE]Admin Kob-Eye | Delier un objet[/TITLE]
<div class="PetiteBoiteDeDialogue">
	<div class="Titre">
		D&eacute;lier
	</div>
	[BLOC Form]
		<div class="InformationImportante">
			Si l'objet n&eacute;cessite forc&eacute;ment un parent, il ne vous sera pas possible de le d&eacute;lier. Vous pourrez par contre le changer en allant dans le menu Lier.
		</div>
		<div style="width:200px;">
		<select name="Form_ObjetSuppr" size="5">
			[STORPROC [!Lien!]::typesParent|Parent]
				[IF [!Parent::Card!]="1,1"]
				[ELSE]
					[STORPROC [!Module::Actuel::Nom!]/[!Parent::Titre!]/[!Objet::ObjectType!]/[!Objet::Id!]|Obj]
						<OPTION VALUE="[!Obj::ObjectType!]/[!Obj::Id!]"> ([!Obj::ObjectType!]) [!Obj::getFirstSearchOrder!]</OPTION>
					[/STORPROC]
				[/IF]
			[/STORPROC]
		</select>
		</div>
		[BLOC Inputhidden|FormSys_Module|[!Objet::Module!]]
		[/BLOC]
		[BLOC Inputhidden|FormSys_ObjectType|[!Objet::ObjectType!]]
		[/BLOC]
		[BLOC Inputhidden|FormSys_Objet|[!Objet::Id!]]
		[/BLOC]
		[BLOC Inputhidden|FormSys_Valid|Unlink]
		[/BLOC]
		[BLOC Inputhidden|FormSys_But|S]
		[/BLOC]
		[BLOC Inputhidden|Form_Delete|Assoc]
		[/BLOC]
		<input type="submit" class="BoutonBlanc" value="D&eacute;lier">
	[/BLOC]
</div>