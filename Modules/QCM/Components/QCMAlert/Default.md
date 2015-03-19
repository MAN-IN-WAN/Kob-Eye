//[IF [!CasUser::Id!]]
<div class="EntoureComposant">
	<div class="EnteteComposant EnteteRechercheLabo">
		Questionnaires
	</div>
	<div class="ContenuComposant ContenuRechercheLabo">
//****** mode normal
//		[STORPROC ProxyCas/Host/[!CasUser::Id!]/Participation/Projet.ParticipationProjetId(DateLimite!!+DateLimite>[!TMS::Now!])+Valide=0|P]
//****** mode test
		[STORPROC ProxyCas/Host/4/Participation/Projet.ParticipationProjetId(DateLimite!!+DateLimite>[!TMS::Now!])+Valide=0|P]
//***************************
		<div  class="alert alert-danger">
			<p>Veuillez répondre aux questionnaires suivants</p>
			<ul>
			[LIMIT 0|10]
				<li><a href="[!CONF::MODULE::QCM::QCMURL!][!P::UrlProjet!]">[!P::Description!]</a></li>
			[/LIMIT]
			</ul>
		</div>
			[NORESULT]
			<p>Vous n'avez pas de questionnaire en cours</p>
			[/NORESULT]
		[/STORPROC]
	</div>
</div>
//[/IF]
