// Liste des contrats


[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1]
	<h3>__COMPLETED_INVOICE__</h3>
	//Completed Invoices
	[STORPROC Murphy/Third/[!Th::Id!]/Invoice.InvoiceSupplierId/Valid=1|Inv]
		[!Flag:::=[!Inv!]!]
	[/STORPROC]
	[STORPROC [!Flag!]|Inv|0|10|Date|DESC]
		[MODULE Murphy/Invoice/EtatList?Inv=[!Inv!]]
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