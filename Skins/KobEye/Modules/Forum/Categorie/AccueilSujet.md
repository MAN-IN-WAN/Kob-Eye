
[STORPROC [!Query!]|this]
	<table class="TableSuj">
		[!Requete:=Forum/Categorie/[!this::Id!]/Sujet!]
		[STORPROC [!Requete!]|Suj|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|tmsCreate|ASC]
			<thead>
				<tr>
					<td></td>
					<td>Sujets</td>
					<td>R&eacute;ponses</td>
					<td>Vus</td>
					<td>Dernier message</td>
					[IF [!Systeme::User::Admin!]]
						<td></td>
					[/IF]
				</tr>
			</thead>
			<tbody>
				[LIMIT 0|100]
					<tr>
						<td class="TdIco">
							<img src="/Skins/KobEye/Img/Forum/petitSujet.gif" alt="" title=""/>
						</td>
						<td class="TdText">
							<a href="/[!Systeme::CurrentMenu::Url!]/[!this::Url!]/Sujet/[!Suj::Url!]" class="Bold" title="Voir le sujet">[!Suj::Titre!]</a>
							<p>de [STORPROC Systeme/User/[!Suj::userCreate!]|Us]
								[!Us::Login!]
							[/STORPROC]
							le [UTIL FULLDATEFR][!Suj::tmsCreate!][/UTIL] &agrave; [UTIL HOUR][!Suj::tmsCreate!][/UTIL]</p>
						</td>
						<td class="TdNbr">
							[STORPROC Forum/Sujet/[!Suj::Id!]/Post|Pst]
								[!NbPost:=[!NbResult!]!]
								[LIMIT 0|1]
									[IF [!NbPost!]!=1]
										[!NbPost!]
									[ELSE]
										0
									[/IF]
								[/LIMIT]
							[/STORPROC]
						</td>
						<td class="TdNbr">[!Suj::Vu!]</td>
						<td class="TdText">
							[STORPROC Forum/Sujet/[!Suj::Id!]/Post|Pst|0|1|tmsCreate|DESC]
								[UTIL FULLDATEFR][!Pst::tmsCreate!][/UTIL] par
								[STORPROC Systeme/User/[!Pst::userCreate!]|Usr]
									[IF [!Pst::userCreate!]==[!Systeme::User::Id!]]
										<a href="/Espace-perso" title="Mon compte" class="Bold">&nbsp;[!Usr::Login!]</a>
									[ELSE]
										<a href="#nogo" class="Bold">&nbsp;[!Usr::Login!]</a>
									[/IF]
									&nbsp;&agrave; [UTIL HOUR][!Suj::tmsCreate!][/UTIL]
								[/STORPROC]
							[/STORPROC]
						</td>
						[IF [!Systeme::User::Admin!]]
							<td class="TdIco">
								<a href="/[!Lien!]?act=suppCat&chemin=[!Requete!]/[!Suj::Id!]" class="LienSupp" title="Supprimer le sujet"></a>
							</td>
						[/IF]
					</tr>
				[/LIMIT]
			</tbody>
		[/STORPROC]
	</table>
[/STORPROC]<br/><br/>
