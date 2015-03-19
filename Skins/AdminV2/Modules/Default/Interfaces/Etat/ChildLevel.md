//INPUT 
//Et= ObjetParent
//Enf = Type d'enfant
//MaxLevel = Niveau de recursivite maximum
//Level = Niveau de recursivite actuel
[STORPROC [!Et::typesEnfant!]|Enf2]
	[BLOC Panneau|background:#DFDFFF;position:relative;overflow:hidden;padding:0px;padding-bottom:5px;]
		<div class="BigTitle" style="font-weight:bold;;-moz-border-radius:5px 5px 0px 0px;background-color:#B192D8;height:20px;">
			[!Enf2::Titre!]
			<a href="/[!Query!]/[!Enf::Titre!]/[!Et::Id!]/[!Enf2::Titre!]/Ajouter" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" class="KEBouton">Ajouter</a>
			<a href="/[!Query!]/[!Enf::Titre!]/[!Et::Id!]/[!Enf2::Titre!]" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" class="KEBouton">Rechercher</a>
			<a href="/[!Query!]/[!Enf::Titre!]/[!Et::Id!]/[!Enf2::Titre!]/Selection" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" class="KEBouton">Selection</a>
		</div>
		[STORPROC [!Module::[!Et::Module!]::Db::ObjectClass!]|ObjClass]
			[IF [!ObjClass::titre!]=[!Enf2::Titre!]]
				[!ObjImg2:=[!ObjClass::Icon!]!]
			[/IF]
		[/STORPROC]
		[COUNT [!Query!]/[!Enf::Titre!]/[!Et::Id!]/[!Enf2::Titre!]|Etn2]
		[IF [!Etn2!]<=50&&[!Etn2!]>0]
			[STORPROC [!Query!]/[!Enf::Titre!]/[!Et::Id!]/[!Enf2::Titre!]|Et2|0|20|tmsCreate|DESC]
				[LIMIT 0|1]
				<table width="100%" >
					<tr style="background:#dedede;">
						<td width="25" style="padding:3px;"></td>
						[STORPROC [!Et2::SearchOrder!]|P2|0|4]
							<td style="padding:3px;">[!P2::Nom!]</td>
						[/STORPROC]
						<td width="50" style="padding:3px;"></td>
					</tr>
				[/LIMIT]
				[LIMIT 0|50]
					<tr style="background:#ffffff;">
						<td><img src="[!ObjImg2!]" style="width:20px;height:20px;float:left;"/></td>
						[STORPROC [!Et2::SearchOrder!]|P2|0|4]
							<td style="padding:3px;"><a href="/[!Query!]/[!Enf::Titre!]/[!Et::Id!]/[!Enf2::Titre!]/[!Et2::Id!]">[!P2::Valeur!]</a></td>
						[/STORPROC]
						<td>
							<a href="/[!Query!]/[!Enf::Titre!]/[!Et::Id!]/[!Enf2::Titre!]/[!Et2::Id!]/Modifier" title="Modifier"><img src="/Skins/AdminV2/Img/application_edit.png" class="ListeMiniImg"/></a>
							<a href="/[!Query!]/[!Enf::Titre!]/[!Et::Id!]/[!Enf2::Titre!]/[!Et2::Id!]/Supprimer" title="Supprimer"><img src="/Skins/AdminV2/Img/delete.png" class="ListeMiniImg"/></a>
							<a href="/[!Query!]/[!Enf::Titre!]/[!Et::Id!]/[!Enf2::Titre!]/[!Et2::Id!]/Cloner" title="Dupliquer"><img src="/Skins/AdminV2/Img/bricks.png" class="ListeMiniImg"/></a>
						</td>
					<tr>
					//[IF [!MaxLevel!]>[!Level!]]
					//	<tr style="background:#ffffff;">
					//		<td></td>
					//		<td colspan="[!NbSE:+1!]">
					//			[MODULE Systeme/Interfaces/Etat/ChildLevel?Enf=[!Enf2!]&Et=[!Et2!]&MaxLevel=[!MaxLevel!]&Level=[!Level:+1!]]
					//		</td>
					//	<tr>
					//[/IF]
				[/LIMIT]
				</table>
			[/STORPROC]
		[/IF]
	[/BLOC]
[/STORPROC]