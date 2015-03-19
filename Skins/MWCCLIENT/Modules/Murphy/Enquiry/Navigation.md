[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1][/STORPROC]

<p>
	<a href="/[!Systeme::CurrentMenu::Url!]?action=new" class="btn btn-block btn-large btn-warning">__NOUVELLE_DEMANDE__</a>
</p>
<ul class="nav nav-tabs nav-stacked">
	//EN NEGOCATION
	[COUNT Murphy/Third/[!Th::Id!]/Enquiry/StatusId=[!CONF::MODULE::MURPHY::STE_DRAFT!]+StatusId=[!CONF::MODULE::MURPHY::STE_HAGGLING!]|NbNoArch]
	<li><a href="/[!Systeme::CurrentMenu::Url!]#top">__ACTIVE_ENQUIRY__<span class="badge pull-right badge-important" style="margin:0 0px 0px 10px;">[!NbNoArch!]</span></a></li>
	
	//EN COURS
	[COUNT Murphy/Third/[!Th::Id!]/Enquiry/StatusId=[!CONF::MODULE::MURPHY::STE_CONFIRMED!]|NbArch]
	<li><a href="/[!Systeme::CurrentMenu::Url!]#current">__CURRENT_ENQUIRY__<span class="badge badge-warning pull-right" style="margin:0 0px 0px 10px;">[!NbArch!]</span></a></li>
	
	//COMPLETE
//	[COUNT Murphy/Third/[!Th::Id!]/Enquiry/StatusId=[!CONF::MODULE::MURPHY::STE_COMPLETED!]|NbArch]
//	<li><a href="/[!Systeme::CurrentMenu::Url!]#completed">__COMPLETED_ENQUIRY__<span class="badge badge-success pull-right" style="margin:0 0px 0px 10px;">[!NbArch!]</span></a></li>

	//CLOSED
	[COUNT Murphy/Third/[!Th::Id!]/Enquiry/StatusId=[!CONF::MODULE::MURPHY::STE_CANCELLED!]+StatusId=[!CONF::MODULE::MURPHY::STE_COMPLETED!]|NbArch]
	<li><a href="/[!Systeme::CurrentMenu::Url!]#closed">__ABANDONNED_ENQUIRY__<span class="badge pull-right" style="margin:0 0px 0px 10px;">[!NbArch!]</span></a></li>

</ul>
