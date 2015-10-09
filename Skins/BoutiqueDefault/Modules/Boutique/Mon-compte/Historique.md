//Pas connecté
[IF [!Systeme::User::Public!]=1][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/IF]
[OBJ Boutique|Magasin|Mags]
[!Mag:=[!Mags::getCurrentMagasin()!]!]
// Client connecté
[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Cli|0|1][/STORPROC] 
<div class="user">
	<h1 class="moncompte">Mon Compte</h1>
	[MODULE Boutique/Mon-compte/Home]


	<div  class="HistoCommande">
		<h2 class="moncompte">Historique de commandes</h2>
		[COUNT Boutique/Client/[!Cli::Id!]/Commande/Valide=1|NbCom]
		[IF [!NbCom!]>0]
		
			<table class="HistoCommande">
				<tr>
					<th>Ref.</th>
					<th>Contenu</th>
					<th>Date</th>
					<th>Montant</th>
					<th>Expédié</th>
					[IF [!Mag::EtapePaiement!]]<th style="border-right:none;">Télécharger</th>[/IF]
				</tr>
				[STORPROC Boutique/Client/[!Cli::Id!]/Commande/Valide=1|Com]
					<tr>
						<td class="HistoRefCommande">[!Com::RefCommande!]</td>
						<td class="HistoContenu">
							[STORPROC Boutique/Commande/[!Com::Id!]/LigneCommande|LC]
								[!LC::Quantite!] x [!LC::Titre!]<br />
							[/STORPROC]
						</td>
						<td class="HistoDate">[!Utils::getDate(d/m/Y,[!Com::tmsCreate!])!]</td>
						<td class="HistoMontant" style="text-align:right;">[!Math::PriceV([!Com::MontantTTC!])!] €</td>
						<td class="HistoPaye">
							[IF [!Mag::EtapePaiement!]]
								[IF [!Com::Paye!]=1]
									<span class="Valid">Payé le [!Utils::getDate(d/m/Y,[!Com::PayeLe!])!]</span>
								[ELSE]
									<span class="Invalid">Non payé</span>
								[/IF]
								<br />
							[/IF]
							[IF [!Com::Expedie!]=1]
								<span class="Valid">Envoyé le [!Utils::getDate(d/m/Y,[!Com::EnvoyeLe!])!]</span>
							[ELSE]
								<span class="Invalid">Non envoyé</span>
							[/IF]
						</td>
						[IF [!Mag::EtapePaiement!]]<td class="HistoPDF">
							[IF [!Com::Paye!]=1]
								[STORPROC Boutique/Commande/[!Com::Id!]/Facture|FA][/STORPROC]
								<a href="/ImpressionFacture/[!FA::Id!]" target="_blank">Facture</a>
							[ELSE]
								<a href="/ImpressionCommande/[!Com::Id!]" target="_blank">Bon de commande</a>
							[/IF]
						</td>[/IF]
					</tr>
				[/STORPROC]
			</table>
		[ELSE]
			<div class="Message">Aucune commande à ce jour</div>
		[/IF]
	</div>
</div>