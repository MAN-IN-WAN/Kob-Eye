

[STORPROC Systeme/User/[!Systeme::User::Id!]/Third:NOVIEW|Th|0|1]
	//MiniEtat Tiers
	<section>
		<h4>__MY_PROFILE__</h4>
		<div class="well form-horizontal">
			[MODULE Murphy/Third/MiniEtat?Id=[!Th::Id!]]
		</div>
	</section>

<!--	<section id="alerts">
		//Alerts
		<h4>__MY_ALERTS__</h4>
		<div class="alert alert-block">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Warning!</strong> Best check yo self, you're not looking too good.
		</div>
		<div class="alert alert-block alert-error">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Warning!</strong> Best check yo self, you're not looking too good.
		</div>
		<div class="alert alert-block alert-success">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Warning!</strong> Best check yo self, you're not looking too good.
		</div>
		<div class="alert alert-block alert-info">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Warning!</strong> Best check yo self, you're not looking too good.
		</div>
	</section>
-->

	[IF [!Th::Buyer!]]
	<section id="enquiry">
		//Dernieres enquiry
		<div class="inline"><h4>__HOME_DEMANDES__</h4><span> Total : [!Cpt!] (<a href="/[!Systeme::getMenu(Murphy/Enquiry)!]">__SEE_ALL__</a>)</span></div>
		[STORPROC Murphy/Third/[!Th::Id!]/Enquiry/StatusId!=[!CONF::MODULE::MURPHY::STE_COMPLETED!]&StatusId!=[!CONF::MODULE::MURPHY::STE_CANCELLED!]|Enq|0|3|Reference|DESC]
			[MODULE Murphy/Enquiry/EtatDetail?Id=[!Enq::Id!]&CONTROL=1]
			[NORESULT]
				<div class="alert alert-block alert-info">__NO_RESULT__</div>
			[/NORESULT]
		[/STORPROC]
		[COUNT Murphy/Third/[!Th::Id!]/Enquiry|Cpt]
	</section>
	[/IF]

	[IF [!Th::Supplier!]]
	<section id="proposal">	
		//Dernieres propositions
		<h4>__HOME_PROPOSALS__</h4>
		[STORPROC Murphy/Third/[!Th::Id!]/Proposal.ProposalSupplierId/StatusId!=[!CONF::MODULE::MURPHY::STP_CANCELLED!]|Prop|0|3|Reference|DESC]
			<div class="well">
				[MODULE Murphy/Proposal/EtatDetail?Id=[!Prop::Id!]&CONTROL=1]
			</div>
			[NORESULT]
				<div class="alert alert-block alert-info">__NO_RESULT__</div>
			[/NORESULT]
		[/STORPROC]
		[COUNT Murphy/Third/[!Th::Id!]/Proposal|Cpt]
		Total : [!Cpt!] (<a href="/[!Systeme::getMenu(Murphy/Proposal)!]">__SEE_ALL__</a>)
	</section>
	[/IF]
	
	<section id="contracts">
		//Derniers contrats
		<h4>__HOME_CONTRACTS__</h4>
		[STORPROC Murphy/Third/[!Th::Id!]/Contract/StatusId>[!CONF::MODULE::MURPHY::STC_DRAFT!]&StatusId!=[!CONF::MODULE::MURPHY::STC_CANCELLED!]&StatusId!=[!CONF::MODULE::MURPHY::STC_COMPLETED!]|Con|0|3|Date|DESC]
			[MODULE Murphy/Contract/EtatList?Con=[!Con!]]
			[NORESULT]
				<div class="alert alert-block alert-info">__NO_RESULT__</div>
			[/NORESULT]
		[/STORPROC]
		[COUNT Murphy/Third/[!Th::Id!]/Contract|Cpt]
		Total : [!Cpt!] (<a href="/[!Systeme::getMenu(Murphy/Contract)!]">__SEE_ALL__</a>)
	</section>

	//Tiers introuvable
	[NORESULT]
		<p>__ERR_NO_THIRD__</p>
	[/NORESULT]
[/STORPROC]
</div>
