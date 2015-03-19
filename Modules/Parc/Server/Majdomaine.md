[TITLE]Admin Kob-Eye | Importation d'un fichier[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
<div id="Container">
	<div id="Arbo">
		[BLOC Panneau][/BLOC]
	</div>
	<div id="Data">
		[BLOC Panneau]
			[BLOC Panneau|background:white;position:relative;overflow:hidden;padding:5px;]
				<div class="BigTitle" style="font-weight:bold;;-moz-border-radius:5px 5px 0px 0px;clear:both;">Propriétés</div>
					[IF [!Action!]="Modifier"]
						<h1>LISTE DES DOMAINES</h1>
						<ul>
						//[STORPROC [![!domainlist!]:/%RC%!]|D|0|500]
							//<li>[!Pos!] [!D!]
							//recherche du domaine
							[STORPROC Parc/Domain|Do|0|100000]
//								//SUPRESION DES SUBDOMAIN
//								[STORPROC Parc/Domain/[!Do::Id!]/Subdomain/LdapID=|N]
//									<ul>
//								    <li>[!N::Url!] [!N::IP!]</li>
//									</ul>
//									[!N::Delete()!]
//								[/STORPROC]
//								//SYNCHRONISATYION
//								[!Do::Synchroniser!]

							//MODIFICATION MAIL
								
								[STORPROC Parc/Domain/[!Do::Id!]/Subdomain/IP=46.105.178.7|N]
									<ul>
								    <li>-[!Do::Url!]- [!N::Url!] [!N::IP!]</li>
									</ul>
									[!N::IP:=213.152.29.215!]
									//[!N::Save()!]
								[/STORPROC]
								
								//MODIFICATION DNS
//								[STORPROC Parc/Domain/[!Do::Id!]/NS|N]
//									[!N::Delete!]
//								[/STORPROC]
//								//Ajout du premier serveur dns
//								[OBJ Parc|NS|N1]
//								[!N1::Nom:=NS:1!]
//								[!N1::AddParent([!Do!])!]
//								[!N1::AddParent(Parc/Server/1)!]
//								[!N1::Save!]
//								//Ajout du premier serveur dns
//								[OBJ Parc|NS|N2]
//								[!N2::Nom:=NS:2!]
//								[!N2::AddParent([!Do!])!]
//								[!N2::AddParent(Parc/Server/41)!]
//								[!N2::Save!]
							[/STORPROC]
							//[!Do::Save!]
							//</li>
						//[/STORPROC]
						</ul>
					[ELSE]
						//Maintenant on ouvre le fichier en ecriture
						<form enctype="multipart/form-data" action="" method="post" name="frm" >
						<div class="Propriete">
							<div class="ProprieteTitre">Liste des domaine (1 par ligne) </div>
							<div class="ProprieteValeur">&nbsp;
								<textarea name="domainlist" style="width:500px;height:400px;">[STORPROC Parc/Domain|Do|0|30000][!F:=0!][STORPROC Parc/Domain/[!Do::Id!]/Subdomain/IP=91.121.17.129|N][!F:=1!][/STORPROC][IF [!F!]][!Do::Url!]
[/IF][/STORPROC]</textarea>
							</div>
						</div>
						<input type="hidden" name="Action" value="Modifier"/>
					[/IF]

			[/BLOC]
			<div class="JSFormButton" style="overflow:hidden;height:60px;margin-right:6px;">
				[IF [!action_import!]=]
					<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Annuler</a>
					<input type="submit" class="KEBouton"  value="Enregistrer" name="SaveObject" style="float:right;"/>
				[ELSE]
					<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Fermer</a>
				[/IF]
			</div>
			</form>
		[/BLOC]
	</div>
</div>