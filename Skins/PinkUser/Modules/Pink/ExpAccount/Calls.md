//Pas connecté
[IF [!Systeme::User::Public!]=1][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/IF]
// Client connecté
[STORPROC Pink/Expert/UserId=[!Systeme::User::Id!]|Cli|0|1][/STORPROC]
<div class="user">
	<h1 class="moncompte">Mon Compte</h1>
	[MODULE Pink/ExpAccount/Home]
	<div  class="HistoCommande">
		<h2 class="moncompte">Historique d'appel</h2>
		[COUNT Pink/Expert/[!Cli::Id!]/Call|NbCom]
		[IF [!NbCom!]>0]
			<table class="HistoCommande">
				<tr>
					<th>Date</th>
					<th>Utilisateur</th>
					<th>Durée</th>
					<th>Unités</th>
					<th>Numéro</th>
				</tr>
				[STORPROC Pink/Expert/[!Cli::Id!]/Call|Com]
					<tr>
						<td class="HistoDate">[!Utils::getDate(d/m/Y h:i,[!Com::Date!])!]</td>
						<td class="HistoRefCommande">[!Com::Mail!]</td>
						<td class="HistoMontant" style="text-align:right;">[!Utils::getDate(i:s,[!Com::Duration!])!]</td>
						<td class="HistoPaye" style="text-align:right;">[!Com::Units!]</td>
						<td class="HistoPaye" >[!Com::ANumber!]</td>
					</tr>
				[/STORPROC]
			</table>
		[ELSE]
			<div class="Message">Aucun appel à ce jour</div>
		[/IF]
	</div>
</div>