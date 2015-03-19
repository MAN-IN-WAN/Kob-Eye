[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1][/STORPROC]

<ul class="nav nav-tabs nav-stacked">
	//TERMINER
	[COUNT Murphy/Third/[!Th::Id!]/Invoice.InvoiceSupplierId/Valid=1|Total2]
	[!Total+=[!Total2!]!]
	<li><a href="/[!Systeme::CurrentMenu::Url!]#terminated">__COMPLETED_INVOICE__<span class="badge badge-success pull-right" style="margin:0 0px 0px 10px;">[IF [!Total!]][!Total!][ELSE]0[/IF]</span></a></li>
</ul>
