//Entete de fiche
[INFO [!Query!]|I]
[STORPROC [!Module::[!Obj::Module!]::Db::ObjectClass!]|ObjClass]
	[IF [!ObjClass::titre!]=[!Obj::ObjectType!]]
		[!OC:=[!ObjClass!]!]
	[/IF]
[/STORPROC]

<div style="overflow:hidden;width:74%;float:left;">
	[!Test:=[!Obj::getElements()!]!]
	//PROPRIETES
	[BLOC Panneau|background:white;position:relative;overflow:hidden;padding:0px;padding-bottom:5px;]
		<div class="BigTitle" style="font-weight:bold;;-moz-border-radius:5px 5px 0px 0px;clear:both;">Propriétés</div>
		[STORPROC [!Test!]|C]
				[IF [!Pos!]>1]
					[BLOC Rounded|background:#dedede;margin:2px 7px;][!Key!][/BLOC]
				[/IF]
				<div style="margin:5px;overflow:hidden;">
					//Affichage Entete + Systeme
					[IF [!Pos!]=1]
						[MODULE Systeme/Interfaces/Etat/Header?Obj=[!Obj!]]
					[/IF]
					//Affichage des proprietes
					[STORPROC [!C!]|T]
						[!K:=[!Key!]!]
						[STORPROC [!T!]|P]
							[LIMIT 0|1000]
								[SWITCH [!P::type!]|=]
									[CASE fkey][/CASE]
									[CASE rkey][/CASE]
									[DEFAULT]
										[MODULE Systeme/Interfaces/Etat/LignePropriete?Prop=[!Obj::getProperty([!P::name!])!]&Class=&Obj=[!Obj!]]
									[/DEFAULT]
								[/SWITCH]
							[/LIMIT]
						[/STORPROC]
					[/STORPROC]
				</div>
			[NORESULT]
				Aucun &eacute;l&eacute;ment trouv&eacute;
			[/NORESULT]
		[/STORPROC]
	[/BLOC]
	
	//ENFANTS
	[BLOC Panneau|background:#BABAD5;position:relative;overflow:hidden;padding:0px;padding-bottom:5px;]
		<div class="BigTitle" style="font-weight:bold;;-moz-border-radius:5px 5px 0px 0px;clear:both;background-color:#7650A5;">Contenu</div>
		[STORPROC [!Obj::getChildTypes(1)!]|Enf]
			[BLOC Panneau|background:#DFDFFF;position:relative;overflow:hidden;padding:0px;padding-bottom:5px;]
				<div class="BigTitle" style="font-weight:bold;;-moz-border-radius:5px 5px 0px 0px;background-color:#B192D8;height:20px;">
					[COUNT [!Query!]/[!Enf::Titre!]|Etn]
					[!Enf::Titre!] ([!Etn!])
					<a href="/[!Query!]/[!Enf::Titre!]/Ajouter" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" class="KEBouton">Ajouter</a>
					<a href="/[!Query!]/[!Enf::Titre!]" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" class="KEBouton">Rechercher</a>
					<a href="/[!Query!]/[!Enf::Titre!]/Selection" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" class="KEBouton">Selection</a>
				</div>
				[STORPROC [!Module::[!Obj::Module!]::Db::ObjectClass!]|ObjClass]
					[IF [!ObjClass::titre!]=[!Enf::Titre!]]
						[!ObjImg:=[!ObjClass::Icon!]!]
					[/IF]
				[/STORPROC]
				[IF [!Etn!]>0]
					[IF [!Etn!]<=50]
						[STORPROC [!Query!]/[!Enf::Titre!]|Et|0|50]
							[LIMIT 0|1]
							<table width="100%" >
								<tr style="background:#dedede;">
									<td width="35" style="padding:3px;"></td>
									[STORPROC [!Et::SearchOrder!]|P|0|4]
										[!NbSE:=[!NbResult!]!]
										<td style="padding:3px;">[!P::Nom!]</td>
									[/STORPROC]
									<td width="50" style="padding:3px;"></td>
								</tr>
							[/LIMIT]
							[LIMIT 0|50]
								<tr style="background:#ffffff;">
									<td><img src="[!ObjImg!]" style="width:20px;height:20px;float:left;"/>
									//RECHERCHE D UN CHAMPS DE TYPE ORDER
									[STORPROC [!Et::getOrderField()!]|OF]
									    [!Et::[!Key!]!]
									    [NORESULT]
										[!Et::Id!]
									    [/NORESULT]
									[/STORPROC]
									</td>
									[STORPROC [!Et::SearchOrder!]|P|0|4]
										<td style="padding:3px;"><a href="/[!Query!]/[!Enf::Titre!]/[!Et::Id!]">[MODULE Systeme/Interfaces/AffichPropValue?Prop=[!P!]&Class=&Obj=[!Et!]]</a></td>
									[/STORPROC]
									<td>
										<a href="/[!Query!]/[!Enf::Titre!]/[!Et::Id!]/Modifier" title="Modifier"><img src="/Skins/AdminV2/Img/application_edit.png" class="ListeMiniImg"/></a>
										<a href="/[!Query!]/[!Enf::Titre!]/[!Et::Id!]/Supprimer" rel="confirm" message="Attention! Vous allez supprimer l'objet [MODULE Systeme/Interfaces/AffichPropValue?Obj=[!Et!]].Etes vous sur de vouloir le supprimer ?" title="Suppression d'un élément" redirectUrl="/[!Query!].htm"><img src="/Skins/AdminV2/Img/delete.png" class="ListeMiniImg"/></a>
										<a href="/[!Query!]/[!Enf::Titre!]/[!Et::Id!]/Cloner" rel="confirm" message="Attention! Etes vous vouloir cloner l'objet [MODULE Systeme/Interfaces/AffichPropValue?Obj=[!Et!]] ?" title="CLonage d'un élément" redirectUrl="/[!Query!].htm"><img src="/Skins/AdminV2/Img/bricks.png" 
									</td>
								<tr>
								[IF [!OC::Display!]=Fiche]
									<tr style="background:#ffffff;">
										<td></td>
										<td colspan="[!NbSE:+1!]">
											[MODULE Systeme/Interfaces/Etat/ChildLevel?Enf=[!Enf!]&Et=[!Et!]&MaxLevel=1&Level=1]
										</td>
									<tr>
								[/IF]
							[/LIMIT]
							</table>
						[/STORPROC]
					[ELSE]
						<span style="margin:3px;">Trop d'éléments à afficher</span>
						<a href="/[!Query!]/[!Enf::Titre!]" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" class="KEBouton">Consulter</a>
	
					[/IF]
				[/IF]
			[/BLOC]
		[/STORPROC]
	[/BLOC]
</div>

	//PARENTS
	[BLOC Panneau|background:#BCC7BE;position:relative;overflow:hidden;padding:0px;padding-bottom:5px;]
		<div class="BigTitle" style="font-weight:bold;;-moz-border-radius:5px 5px 0px 0px;clear:both;background-color:#4E883D;">Emplacements</div>
		[STORPROC [!Obj::typesParent!]|Par]
			[BLOC Panneau|background:#E8F6EC;position:relative;overflow:hidden;padding:0px;padding-bottom:5px;]
				<div class="BigTitle" style="font-weight:bold;;-moz-border-radius:5px 5px 0px 0px;background-color:#67B350;height:20px;">
					[!Par::Titre!]
					<a href="/[!Query!]/[!Par::Titre!]/Deplacer" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" class="KEBouton">Déplacer</a>
				</div>
				[STORPROC [!Module::[!Obj::Module!]::Db::ObjectClass!]|ObjClass]
					[IF [!ObjClass::titre!]=[!Par::Titre!]]
						[!ObjImg:=[!ObjClass::Icon!]!]
					[/IF]
				[/STORPROC]

				[STORPROC [!Module::Actuel::Nom!]/[!Par::Titre!]/[!Obj::ObjectType!]/[!Obj::Id!]|Pt|0|10|tmsCreate|DESC]
					<div style="background:url([!ObjImg!]) no-repeat white top left;position:relative;padding:5px 5px 5px 130px;margin:2px 2pxpx;overflow:hidden;">
						<h2 style="text-align:left;height:auto;">[!Pt::getFirstSearchOrder!] ([!Pt::Id!])</h2>
						[!Pt::getSecondSearchOrder!]
						[STORPROC [!Pt::SearchOrder!]|P|1|3]
							<div style="overflow:hidden;"><label>[!P::Nom!] : </label>[!P::Valeur!]</div>
						[/STORPROC]
						<a href="/[!Query!]/[!Par::Titre!]/Delier" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" class="KEBouton">Délier</a>
						<a href="/[!Pt::getUrl!]" style="float:right;width:50px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" class="KEBouton">Accéder</a>
					</div>
				[/STORPROC]
				
			[/BLOC]
		[/STORPROC]
	[/BLOC]
