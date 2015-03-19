[TITLE]Admin Kob-Eye | Ajout d'un objet[/TITLE]
[IF [!FormSys_Valid!]=OK]
	[IF [!AddNew!]!=1]
		<meta http-equiv="refresh" content="2; url=/[!OldQuery!]" />
		<div class="PetiteBoiteDeDialogue">
			<div class="Titre">
				Veuillez patienter...
			</div>
				Ajout en cours. Vous allez &ecirc;tre redirig&eacute; dans 5 secondes. Si cela ne se fait pas, cliquez 
				<a href="/[!OldQuery!]" class="LienModule">ici</a>.
		</div>
	[/IF]

[/IF]
<h1>
	Ajout d'un nouvel &eacute;l&eacute;ment
</h1>
<form enctype="multipart/form-data" action="" name="frm" method="post">
	[STORPROC ObjectClass::[!lastModule!]::[!lastObject!]::Proprietes|Prop]
		[IF [!Prop::Ref!]=EMPTY]
			[MODULE Systeme/Interfaces/Objet/ModifProprietes]
		[/IF]
	[/STORPROC]
	<div class="Rattacher">
		<div style="background-image : url(/Skins/AdminV2/Img/TraitBack.jpg);border-bottom:1px solid black;">Rattacher &agrave;</div>
	[STORPROC ObjectClass::[!lastModule!]::[!lastObject!]::Reserved|Ref]-+
		[!Ref::reference!]
	[/STORPROC]
	[STORPROC [!Query!]::Historique|Old]
		[IF [!Pos!]=[!NbResult!]]
			[IF [!Old::getUrl!]!=[!Query!]]
				[STORPROC [!Old::getUrl!]|Vieux]
				<input type="checkbox" name="Form_[!Old::ObjectType!]-[!Pos!]" value="[!Old::Id!]" class="Check">([!Vieux::ObjectType!]:[!Vieux::Id!]) [!Vieux::getFirstSearchOrder!]<br/>
				[/STORPROC]
			[/IF]
		[ELSE]
			[IF [!Vieux::ObjectType!]=[!Type::Titre!]]
				[STORPROC [!Old::getUrl!]|Vieux]
				<input type="checkbox" name="Form_[!Old::ObjectType!]-[!Pos!]" value="[!Old::Id!]" class="Check">([!Vieux::ObjectType!]:[!Vieux::Id!]) [!Vieux::getFirstSearchOrder!]<br/>
				[/STORPROC]
			[/IF]
		[/IF]
		[NORESULT] Insertion d'un objet &agrave; la racine: aucun rattachement possible[/NORESULT]
	[/STORPROC]
		[STORPROC [!Query!]|Current]
			<input type="checkbox" name="Form_[!Current::ObjectType!]-Top" value="[!Current::Id!]" class="Check" checked>([!Current::ObjectType!]:[!Current::Id!]) [!Objet::getFirstSearchOrder!]<br>
		[/STORPROC]
	</div>
	[BLOC Inputhidden|FormSys_Module|[!Objet::Module!]]
	[/BLOC]
	[BLOC Inputhidden|FormSys_ObjectType|[!Objet::ObjectType!]]
	[/BLOC]
	[BLOC Inputhidden|OldQuery|[!OldQuery!]]
	[/BLOC]
	[BLOC Inputhidden|FormSys_Valid|OK]
	[/BLOC]
	[BLOC Inputhidden|FormSys_But|A]
	[/BLOC]
	<table>
	<tr>
		<td>Utilisateur : </td>
		<td>
			<select name="Form_uid" class="Champ">
				<option value="">Choisir...</option>
				[STORPROC Systeme/User|Uid]
					<option value="[!Uid::Id!]" [IF [!Objet::uid!]=[!Uid::Id!]] selected="selected"[/IF]>[!Uid::Login!]</option>
				[/STORPROC]
			</select>
		</td>
		<td>
			<select name="Form_umod" class="Champ">
					<option value="">Choisir...</option>
					<option value="0" [IF [!Objet::umod!]=0] selected="selected"[/IF]>Aucun</option>
					<option value="1" [IF [!Objet::umod!]=1] selected="selected"[/IF]>Existence</option>
					<option value="3" [IF [!Objet::umod!]=3] selected="selected"[/IF]>Affichage</option>
					<option value="5" [IF [!Objet::umod!]=5] selected="selected"[/IF]>Modification</option>
					<option value="7" [IF [!Objet::umod!]=7] selected="selected"[/IF]>Tous</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Groupe : </td>
		<td> 
			<select name="Form_gid" class="Champ">
				<option value="">Choisir...</option>
				[STORPROC Systeme/Group/2/Group|Gid]
					<option value="[!Gid::Id!]" [IF [!Objet::gid!]=[!Gid::Id!]] selected="selected"[/IF]>[!Gid::Nom!]</option>
				[/STORPROC]
			</select>
		</td>
		<td>
			<select name="Form_gmod" class="Champ">
				<option value="">Choisir...</option>
				<option value="0" [IF [!Objet::gmod!]=0] selected="selected"[/IF]>Aucun</option>
				<option value="1" [IF [!Objet::gmod!]=1] selected="selected"[/IF]>Existence</option>
				<option value="3" [IF [!Objet::gmod!]=3] selected="selected"[/IF]>Affichage</option>
				<option value="5" [IF [!Objet::gmod!]=5] selected="selected"[/IF]>Modification</option>
				<option value="7" [IF [!Objet::gmod!]=7] selected="selected"[/IF]>Tous</option>
			</select>
		</td>
	</tr>
	<tr>
		<td> Les autres : 
		</td>
		<td></td>
		<td>
			<select name="Form_omod" class="Champ">
				<option value="">Choisir...</option>
				<option value="0" [IF [!Objet::omod!]=0] selected="selected"[/IF]>Aucun</option>
				<option value="1" [IF [!Objet::omod!]=1] selected="selected"[/IF]>Existence</option>
				<option value="3" [IF [!Objet::omod!]=3] selected="selected"[/IF]>Affichage</option>
				<option value="5" [IF [!Objet::omod!]=5] selected="selected"[/IF]>Modification</option>
				<option value="7" [IF [!Objet::omod!]=7] selected="selected"[/IF]>Tous</option>
			</select>
		</td>
	</tr>
	</table>
	<br><input type="checkbox" name="AddNew" value="1" class="Check">Ensuite, je veux encore ajouter.<br>
	!!!!! Si certains champs sont manquants, vous pourrez les remplir &agrave; l'aide de la fonction modifier
	<div style="margin-left:50px;margin-top:20px">
		<INPUT TYPE="SUBMIT"  class="BoutonBlanc" VALUE="Enregistrer">
	</div>
<input type="hidden" name="MAX_FILE_SIZE" value="100000000000" />
</form>
