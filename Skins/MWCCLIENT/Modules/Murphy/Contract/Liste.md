// Liste des contrats


[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1]
	<h3>__CURRENT_CONTRACT__</h3>
	//Current contracts
	[!Flag:=!]
	[STORPROC Murphy/Third/[!Th::Id!]/Contract.ContractBuyerId/StatusId=[!CONF::MODULE::MURPHY::STC_VALIDATED!]&BuyerStatus=[!CONF::MODULE::MURPHY::STC_SENT!]|Co]
		[!Flag:::=[!Co!]!]
	[/STORPROC]
	[STORPROC Murphy/Third/[!Th::Id!]/Contract.ContractSupplierId/StatusId=[!CONF::MODULE::MURPHY::STC_VALIDATED!]&SupplierStatus=[!CONF::MODULE::MURPHY::STC_SENT!]|Co]
		[!Flag:::=[!Co!]!]
	[/STORPROC]

	[STORPROC [!Flag!]|Co|0|10|Date|DESC]
		[MODULE Murphy/Contract/EtatList?Con=[!Co!]]
		[NORESULT]
			//<div class="alert alert-info">
				__NO_RESULT__
			//</div>
		[/NORESULT]
	[/STORPROC]

	<h3>__ACCEPTED_CONTRACT__</h3>
	//Accepted contracts
	[!Flag:=!]
	[STORPROC Murphy/Third/[!Th::Id!]/Contract.ContractBuyerId/StatusId=[!CONF::MODULE::MURPHY::STC_CONFIRMED!]+(!StatusId=[!CONF::MODULE::MURPHY::STC_VALIDATED!]&BuyerStatus=[!CONF::MODULE::MURPHY::STC_ACCEPTED!]!)|Co]
		[!Flag:::=[!Co!]!]
	[/STORPROC]
	[STORPROC Murphy/Third/[!Th::Id!]/Contract.ContractSupplierId/StatusId=[!CONF::MODULE::MURPHY::STC_CONFIRMED!]+(!StatusId=[!CONF::MODULE::MURPHY::STC_VALIDATED!]&SupplierStatus=[!CONF::MODULE::MURPHY::STC_ACCEPTED!]!)|Co]
		[!Flag:::=[!Co!]!]
	[/STORPROC]
	[STORPROC [!Flag!]|Co|0|10|Date|DESC]
		[MODULE Murphy/Contract/EtatList?Con=[!Co!]]
		[NORESULT]
			//<div class="alert alert-info">
				__NO_RESULT__
			//</div>
		[/NORESULT]
	[/STORPROC]

//	<h3>__COMPLETED_CONTRACT__</h3>
//	//Completed contracts
//	[!Flag:=!]
//	[STORPROC Murphy/Third/[!Th::Id!]/Contract.ContractBuyerId/StatusId=[!CONF::MODULE::MURPHY::STC_COMPLETED!]|Co]
//		[!Flag:::=[!Co!]!]
//	[/STORPROC]
//	[STORPROC Murphy/Third/[!Th::Id!]/Contract.ContractSupplierId/StatusId=[!CONF::MODULE::MURPHY::STC_COMPLETED!]|Co]
//		[!Flag:::=[!Co!]!]
//	[/STORPROC]
//	[STORPROC [!Flag!]|Co|0|10|Date|DESC]
//		[MODULE Murphy/Contract/EtatList?Con=[!Co!]]
//		[NORESULT]
//			//<div class="alert alert-info">
//				__NO_RESULT__
//			//</div>
//		[/NORESULT]
//	[/STORPROC]

	<h3>__CLOSED_CONTRACT__</h3>
	//Closed contracts
	[!Flag:=!]
	[STORPROC Murphy/Third/[!Th::Id!]/Contract.ContractBuyerId/StatusId=[!CONF::MODULE::MURPHY::STC_CANCELLED!]+StatusId=[!CONF::MODULE::MURPHY::STC_COMPLETED!]|Co]
		[!Flag:::=[!Co!]!]
	[/STORPROC]
	[STORPROC Murphy/Third/[!Th::Id!]/Contract.ContractSupplierId/StatusId=[!CONF::MODULE::MURPHY::STC_CANCELLED!]+StatusId=[!CONF::MODULE::MURPHY::STC_COMPLETED!]|Co]
		[!Flag:::=[!Co!]!]
	[/STORPROC]
	[STORPROC [!Flag!]|Co|0|10|Date|DESC]
		[MODULE Murphy/Contract/EtatList?Con=[!Co!]]
		[NORESULT]
			//<div class="alert alert-info">
				__NO_RESULT__
			//</div>
		[/NORESULT]
	[/STORPROC]

	[NORESULT]
		<p>__ERR_NO_THIRD__</p>
	[/NORESULT]
[/STORPROC]