[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1][/STORPROC]
<ul class="nav nav-tabs nav-stacked">
	//A TRAITER
	[COUNT Murphy/Third/[!Th::Id!]/Proposal.ProposalSupplierId/StatusId=[!CONF::MODULE::MURPHY::STP_DRAFT!]|NbNoArch]
	<li><a href="/[!Systeme::CurrentMenu::Url!]#top">__ACTIVE_PROPOSAL__<span class="badge badge-important pull-right" style="margin:0 0px 0px 10px;">[!NbNoArch!]</span></a></li>
	
	//EN ATTENTE
	[COUNT Murphy/Third/[!Th::Id!]/Proposal.ProposalSupplierId/StatusId=[!CONF::MODULE::MURPHY::STP_ANSWERED!]+StatusId=[!CONF::MODULE::MURPHY::STP_VALIDATED!]|NbArch]
	<li><a href="/[!Systeme::CurrentMenu::Url!]#waiting">__WAITING_PROPOSAL__<span class="badge badge-warning pull-right" style="margin:0 0px 0px 10px;">[!NbArch!]</span></a></li>
	
	//ACCEPTER
	[COUNT Murphy/Third/[!Th::Id!]/Proposal.ProposalSupplierId/StatusId=[!CONF::MODULE::MURPHY::STP_ACCEPTED!]+StatusId=[!CONF::MODULE::MURPHY::STP_REFUSED!]+StatusId=[!CONF::MODULE::MURPHY::STP_REJECTED!]+StatusId=[!CONF::MODULE::MURPHY::STP_REVISED!]+StatusId=[!CONF::MODULE::MURPHY::STP_CANCELLED!]|NbArch]
	<li><a href="/[!Systeme::CurrentMenu::Url!]#accepted">__ACCEPTED_PROPOSAL__<span class="badge badge-success pull-right" style="margin:0 0px 0px 10px;">[!NbArch!]</span></a></li>
</ul>
