[STORPROC Systeme/User/[!Systeme::User::Id!]/Third:NOVIEW|Th|0|1][/STORPROC]
<ul class="nav nav-tabs nav-stacked">
	//MY PROFILE
	<li><a href="/[!Systeme::CurrentMenu::Url!]#top">__MY_PROFILE__</a></li>
	
	//ALERTS
	//[COUNT Murphy/Third/[!Th::Id!]/Enquiry/StatusId=[!CONF::MODULE::MURPHY::STE_CONFIRMED!]|NbArch]
//	<li><a href="/[!Systeme::CurrentMenu::Url!]#alerts">__MY_ALERTS__<span class="badge badge-warning pull-right" style="margin:0 0px 0px 10px;">4[!NbArch!]</span></a></li>
	
	[IF [!Th::Buyer!]]
	//LAST ENQUIRIES
	<li><a href="/[!Systeme::CurrentMenu::Url!]#enquiry">__HOME_DEMANDES__</a></li>
	[/IF]
	
	[IF [!Th::Supplier!]]
	//LAST PROPOSALS
	<li><a href="/[!Systeme::CurrentMenu::Url!]#proposal">__HOME_PROPOSALS__</a></li>
	[/IF]

	//LAST CONTRACTS
	<li><a href="/[!Systeme::CurrentMenu::Url!]#contracts">__HOME_CONTRACTS__</a></li>
</ul>
