//Analyse du type [!Class!]
[STORPROC [!Module::Actuel::Nom!]/[!Class!]|Test|0|1]
[IF [!Test::isReflexive!]]
	[STORPROC [!Module::Actuel::Nom!]/[!Class!]|Group]
		<div class="Decale">+<input type="radio" value="[!Group::Id!]" name="Val_Arbo" 
			[IF [!Gid!]!=""]
				[IF [!Gid!]=[!Group::Id!]] checked="true" [/IF]
			[ELSE]
				[IF [!Objet::Gid!]=[!Group::Id!]] checked="true" [/IF]
			[/IF]
			/>
			[!Group::Nom!]
			[RECURSIV [!Group::Id!]/Group]
		</div>
	[/STORPROC]
	<input type="hidden" name="Type_Arbo" value="Gid"/>
[ELSE]
	<select name="Val_Arbo" size="11" style="width:100%;border:0;">
		[STORPROC [!Module::Actuel::Nom!]/[!Class!]|Usr]
			<option value="[!Usr::Id!]" 
			[IF [!Uid!]!=""]
				[IF [!Uid!]=[!Usr::Id!]] selected="selected" [/IF]
			[ELSE]
				[IF [!Objet::Uid!]=[!Usr::Id!]] selected="selected" [/IF]
			[/IF]
			>[!Usr::Login!]</option>
		[/STORPROC]
	</select>
	<input type="hidden" name="Type_Arbo" value="Uid"/>
[/IF]
[/STORPROC]