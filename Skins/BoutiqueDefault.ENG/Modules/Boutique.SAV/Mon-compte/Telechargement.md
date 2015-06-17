//Pas connecté
[IF [!Systeme::User::Public!]=1][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/IF]

// Client connecté
[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Cli|0|1][/STORPROC] 
<div class="user">
	<h1 class="moncompte">Mon Compte</h1>
	[MODULE Boutique/Mon-compte/Home]
	<div  class="HistoCommande">
		<h2 class="moncompte">Mon espace téléchargement</h2>
		[COUNT Boutique/Client/[!Cli::Id!]/Telechargement|NbTel]
		[IF [!NbTel!]>0]
			<div class="table-responsive"><table class="HistoCommande table-striped" >
				<tr>
					<th>Date</th>
					<th>Contenu</th>
					<th>Télécharger</th>
				</tr>	
				[STORPROC Boutique/Client/[!Cli::Id!]/Telechargement|Telg]
					[!OkTelg:=0!]
					[COUNT Boutique/LigneCommande/Telechargement/[!Telg::Id!]|NbAbo]
					[STORPROC Boutique/LigneCommande/Telechargement/[!Telg::Id!]|Lc]
						[STORPROC Boutique/Commande/LigneCommande/[!Lc::Id!]|Co]
							[IF [!Co::Valide!]=1&&[!Co::Paye!]=1][!OkTelg:=1!][/IF]
						[/STORPROC]
					[/STORPROC]
					//[IF [!SERVER::REMOTE_ADDR!]=178.22.145.106]
					//	<tr>
					//		<td>[!Utils::getDate(d/m/Y,[!Telg::tmsCreate!])!]</td>
					//		<td>[!Telg::Nom!]</td>
					//		<td>
					//			[STORPROC Explorateur/[!Telg::Url!]/_Fichier|Dos]
					//				<a href="/Boutique/Telechargement/[!Telg::Id!]/getDownloadDirect.htm?Fichier=[!Dos::Id!]&FicName=TelAbo" alt="[!Telg::Nom!]">Télécharger</a><br />
					//			[/STORPROC]
					//		</td>
					//	</tr>	
					//[/IF]
					<tr>
						<td>[!Utils::getDate(d/m/Y,[!Telg::tmsCreate!])!]</td>
						<td>[!Telg::Nom!]</td>
						<td>
							[IF [!OkTelg!]=1]
								<a href="/Boutique/Telechargement/[!Telg::Id!]/getDownload.htm" alt="[!Telg::Nom!]">Télécharger</a>
							[ELSE]
								[IF [!NbAbo!]]
									En attente de validation
								[ELSE]
									<a href="/Boutique/Telechargement/[!Telg::Id!]/getDownload.htm" alt="[!Telg::Nom!]">Télécharger</a>
								[/IF]
							[/IF]
						</td>
					</tr>	
				[/STORPROC]
			</table></div>
		[ELSE]
			<div class="Message">Aucun téléchargement disponible à ce jour</div>
		[/IF]

	</div>
</div>