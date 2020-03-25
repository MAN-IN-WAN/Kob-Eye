[TITLE]Admin Kob-Eye | Modifier un objet[/TITLE]
[!Query!]-[!Type!]-[!Interface!]
<form enctype="multipart/form-data" action="" method="post" name="frm">
	[STORPROC [!Query!]|Objet]
		[BLOC Inputhidden|FormSys_Module|[!Objet::Module!]][/BLOC]
		[BLOC Inputhidden|FormSys_ObjectType|[!Objet::ObjectType!]][/BLOC]
		[BLOC Inputhidden|FormSys_Identifiant|[!Objet::Id!]][/BLOC]
		[BLOC Inputhidden|FormSys_Valid|Modif][/BLOC]
		[BLOC Inputhidden|Form_Id|[!Objet::Id!]][/BLOC]
		[BLOC Inputhidden|FormSys_But|M][/BLOC]
	[/STORPROC]
	[!Objet::Nom!]
	<table style="padding-left:10px;">
	[STORPROC [!Query!]::Proprietes|Prop]
		[IF [!Prop::Ref!]=EMPTY]
			<tr>
				[MODULE Systeme/Interfaces/Objet/ModifProprietes]
			</tr>
		[/IF]
	[/STORPROC]
	<tr>
		<td>Utilisateur : </td>
		<td>
			<select name="Form_uid" class="Champ">
				<option value="">Choisir...</option>
				[STORPROC Systeme/User|Uid]
					<option value="[!Uid::Id!]" [IF [!Objet::uid!]=[!Uid::Id!]] selected="selected"[/IF]>[!Uid::Login!]</option>
				[/STORPROC]
			</select>
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
	[IF [!Objet::Referent!]!=faux]
		<div style="padding:10px;margin-left:7%;margin-top:10px;margin-bottom:10px;width:30%;border:1px solid black;">
		<div>Pour modifier les autres propri&eacute;t&eacute;s, assurez vous d'avoir les droits suffisants sur   [!Objet::Referent!] n�[!Objet::Liaison!] (dans le module [!Objet::ReferentModule!]), et rendez vous ici:</div>
		<div style="margin:10px;"> <a class="FauxBoutonBlanc" href="/[!Objet::ReferentModule!]/[!Objet::Referent!]/[!Objet::Liaison!]/Modifier" style="margin:10px;"> Y acc&eacute;der</a></div>
		</div>
	[/IF]
	<div style="margin-left:80px;margin-top:5px;">
		<INPUT TYPE="SUBMIT"  class="BoutonBlanc" VALUE="Enregistrer">
		<a href="/[!Query!]" class="FauxBoutonBlanc">Annuler</a>
	</div>
	<input type="hidden" name="MAX_FILE_SIZE" value="100000000000" />
</form>