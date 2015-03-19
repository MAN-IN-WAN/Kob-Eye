[TITLE]Admin Kob-Eye | Supression d'un objet[/TITLE]
<div class="PetiteBoiteDeDialogue">
	<div class="Titre">
		Suppression
	</div>
	<div class="Message">
		Etes vous sur de vouloir supprimer ?
	</div>
	<div class="Nav">
		<span style="margin-left:40px;margin-top:5px;">
			<form action="" method="POST" style="display:inline;">
			[BLOC Inputhidden|FormSys_Module|[!Objet::Module!]]
			[/BLOC]
			[BLOC Inputhidden|Form_Query|[!Lien!]]
			[/BLOC]
			[BLOC Inputhidden|FormSys_Valid|Supprimer]
			[/BLOC]
			[BLOC Inputhidden|FormSys_But|S]
			[/BLOC]
			[BLOC Inputhidden|Form_Delete|Obj]
			[/BLOC]
		
			[STORPROC [!Lien!]::Historique|Histo]
				[IF [!NbResult!]>1]
						
					[IF [!Pos!]=[!NbResult:-1!]]
						[BLOC Inputhidden|OldQuery|[!Histo::getUrl!]][/BLOC]	
					[/IF]
				[ELSE]
					[BLOC Inputhidden|OldQuery|[!Module::Actuel::Nom!]][/BLOC]
				[/IF]
			[/STORPROC]
			<INPUT TYPE="SUBMIT"  VALUE="VALIDER" />
			</form>
		</span>
		<span style="margin-left:40px;margin-top:5px;">
			<form action="?" method="GET" style="display:inline;">
			<INPUT TYPE="SUBMIT"  VALUE="ANNULER" />
			</form>
		</span>
	</div>
</div>