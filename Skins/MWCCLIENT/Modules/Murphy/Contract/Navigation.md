[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1][/STORPROC]

<ul class="nav nav-tabs nav-stacked">
	//EN COURS
	[COUNT Murphy/Third/[!Th::Id!]/Contract.ContractBuyerId/StatusId=[!CONF::MODULE::MURPHY::STC_VALIDATED!]&BuyerStatus=[!CONF::MODULE::MURPHY::STC_SENT!]|Total]
	[COUNT Murphy/Third/[!Th::Id!]/Contract.ContractSupplierId/StatusId=[!CONF::MODULE::MURPHY::STC_VALIDATED!]&SupplierStatus=[!CONF::MODULE::MURPHY::STC_SENT!]|Total2]
	[!Total+=[!Total2!]!]
	<li><a href="/[!Systeme::CurrentMenu::Url!]#current">__CURRENT_CONTRACT__<span class="badge badge-important pull-right" style="margin:0 0px 0px 10px;">[!Total!]</span></a></li>
	
	//ACCEPTER
	[COUNT Murphy/Third/[!Th::Id!]/Contract.ContractBuyerId/StatusId=[!CONF::MODULE::MURPHY::STC_CONFIRMED!]+(!StatusId=[!CONF::MODULE::MURPHY::STC_VALIDATED!]&BuyerStatus=[!CONF::MODULE::MURPHY::STC_ACCEPTED!]!)|Total]
	[COUNT Murphy/Third/[!Th::Id!]/Contract.ContractSupplierId/StatusId=[!CONF::MODULE::MURPHY::STC_CONFIRMED!]+(!StatusId=[!CONF::MODULE::MURPHY::STC_VALIDATED!]&SupplierStatus=[!CONF::MODULE::MURPHY::STC_ACCEPTED!]!)|Total2]
	[!Total+=[!Total2!]!]
	<li><a href="/[!Systeme::CurrentMenu::Url!]#accepted">__ACCEPTED_CONTRACT__<span class="badge badge-warning pull-right" style="margin:0 0px 0px 10px;">[!Total!]</span></a></li>
	
//	//TERMINER
//	[COUNT Murphy/Third/[!Th::Id!]/Contract.ContractBuyerId/StatusId=[!CONF::MODULE::MURPHY::STC_COMPLETED!]|Total]
//	[COUNT Murphy/Third/[!Th::Id!]/Contract.ContractSupplierId/StatusId=[!CONF::MODULE::MURPHY::STC_COMPLETED!]|Total2]
//	[!Total+=[!Total2!]!]
//	<li><a href="/[!Systeme::CurrentMenu::Url!]#terminated">__COMPLETED_CONTRACT__<span class="badge badge-success pull-right" style="margin:0 0px 0px 10px;">[!Total!]</span></a></li>

//	//ABANDONNER
//	[COUNT Murphy/Third/[!Th::Id!]/Contract.ContractBuyerId/StatusId=[!CONF::MODULE::MURPHY::STC_CANCELLED!]|Total]
//	[COUNT Murphy/Third/[!Th::Id!]/Contract.ContractSupplierId/StatusId=[!CONF::MODULE::MURPHY::STC_CANCELLED!]|Total2]
//	[!Total+=[!Total2!]!]
//	<li><a href="/[!Systeme::CurrentMenu::Url!]#cancelled">__CANCELLED_CONTRACT__<span class="badge pull-right" style="margin:0 0px 0px 10px;">[!Total!]</span></a></li>

	//CLOSED
	[COUNT Murphy/Third/[!Th::Id!]/Contract.ContractBuyerId/StatusId=[!CONF::MODULE::MURPHY::STC_CANCELLED!]+StatusId=[!CONF::MODULE::MURPHY::STC_COMPLETED!]|Total]
	[COUNT Murphy/Third/[!Th::Id!]/Contract.ContractSupplierId/StatusId=[!CONF::MODULE::MURPHY::STC_CANCELLED!]+StatusId=[!CONF::MODULE::MURPHY::STC_COMPLETED!]|Total2]
	[!Total+=[!Total2!]!]
	<li><a href="/[!Systeme::CurrentMenu::Url!]#cancelled">__CLOSED_CONTRACT__<span class="badge pull-right" style="margin:0 0px 0px 10px;">[!Total!]</span></a></li>
</ul>
