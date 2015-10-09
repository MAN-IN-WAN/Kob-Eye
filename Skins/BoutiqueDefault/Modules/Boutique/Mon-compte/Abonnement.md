//Pas connecté
[IF [!Systeme::User::Public!]=1][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/IF]

// Client connecté
[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Cli|0|1][/STORPROC] 
<div class="user">
	<h1 class="moncompte">Mon Compte</h1>
	[MODULE Boutique/Mon-compte/Home]


	<div  class="HistoCommande">
		<h2 class="moncompte">Mon espace abonnement</h2>
		[COUNT Boutique/Client/[!Cli::Id!]/Service|NbServ]
		[!Trouve:=0!]
		[IF [!NbServ!]>0]
			<div class="table-responsive"><table class="HistoCommande table-striped" >
				<tr>
					<th>Date</th>
					<th>Service</th>
					<th>Du </th>
					<th>Au</th>
				</tr>	
				[STORPROC Boutique/Client/[!Cli::Id!]/Service|Serv]
					[!OkAbo:=0!]
					[STORPROC Boutique/LigneCommande/Service/[!Serv::Id!]|Lc]
						[STORPROC Boutique/Commande/LigneCommande/[!Lc::Id!]|Co]
							[IF [!Co::Valide!]=1&&[!Co::Paye!]=1][!OkAbo:=1!][/IF]
						[/STORPROC]
					[/STORPROC]
// rustine créati abo
[IF [!Serv::tmsCreate!]>1412668800&&[!Serv::tmsCreate!]<1412755200]
[!OkAbo:=1!]
[/IF]
					<tr>
						<td>[!Utils::getDate(d/m/Y,[!Serv::tmsCreate!])!]</td>
						<td>[!Serv::Nom!]</td>
						[IF [!OkAbo!]=1]
							<td>[!Utils::getDate(d/m/Y,[!Serv::DateDebut!])!]</td>
							<td>[!Utils::getDate(d/m/Y,[!Serv::DateFin!])!]</td>
						[ELSE]
							<td colspan="2">Abonnement en attente de validation</td>
						[/IF]
					</tr>	
				[/STORPROC]
			</table></div>
		[ELSE]
			<div class="Message">Aucun service disponible à ce jour</div>
		[/IF]

	</div>
</div>