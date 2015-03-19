// Ficher d'un contrat

[STORPROC [!Query!]|Co][/STORPROC]

[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1]

        [STORPROC Murphy/Third/[!Th::Id!]/Invoice.InvoiceBuyerId/[!Co::Id!]|Co|0|1]
            [!Role:=Buyer!]
        [/STORPROC]
    
        [STORPROC Murphy/Third/[!Th::Id!]/Invoice.InvoiceSupplierId/[!Co::Id!]|Co|0|1]
            [!Role:=Supplier!]
        [/STORPROC]        
        
	[IF [!Role!]=Buyer||[!Role!]=Supplier]
		<h3>__Invoice__ [!Co::Reference!]([!Role!])</h3>
		[IF [!submitForm!]!=]
			// Changement de statut
			[IF [!submitForm!]=1][!Type:=Acceptance!][/IF]
			[IF [!submitForm!]=2][!Type:=Refusal!][/IF]
			[METHOD Co|[!Role!][!Type!]][/METHOD]
		[ELSE]
			[MODULE Murphy/Invoice/EtatDetail?Id=[!Co::Id!]]
			[IF [!Co::Status!]=[!CONF::MODULE::MURPHY::STC_VALIDATED!]&&[!Co::[!Role!]Status!]=0]
				<div class="well">
					<form method="post" action="/[!Lien!]">
						<div class="row-fluid" style="margin-top:10px;">
							<div class="span6">
								<button name="accept"  onclick="this.form.submitForm.value=1;this.form.submit()" class="btn btn-success btn-block">__ACCEPT__</button>
								<!-- FONCTION AcceptInvoice -->
							</div>
							<div class="span6">
								<button name="refuse" onclick="this.form.submitForm.value=2;this.form.submit()" class="btn btn-danger btn-block">__REFUSER__</button>
								<!-- FONCTION RefuseInvoice -->
							</div>
						</div>
						<input type="hidden" name="submitForm" value="" />
					</form>
				</div>
			[ELSE]
				[IF [!Co::Status!]=[!CONF::MODULE::MURPHY::STC_CONFIRMED!]||[!Co::Status!]=[!CONF::MODULE::MURPHY::STC_COMPLETED!]]
					<div class="well">
						<form method="post" action="/[!Lien!]">
							<div class="row-fluid" style="margin-top:10px;">
								<div class="span12">
									<button name="accept"  onclick="this.form.submitForm.value=1;this.form.submit()" class="btn btn-murphy btn-block">__PRINT__</button>
									<!-- FONCTION PrintInvoice -->
								</div>
							</div>
							<input type="hidden" name="submitForm" value="" />
						</form>
					</div>
				[/IF]
			[/IF]
		[/IF]
	[/IF]
			<div class="row-fluid" style="margin-top:10px;">
				<div class="span2 pull-right">
					<button name="accept" value="1" class="btn btn-success btn-block btn-murphy">__PRINT__</button>
					<!-- FONCTION AcceptProposal -->
				</div>
//				<div class="span2 offset1">
//					<button name="accept" value="1" class="btn btn-success btn-block">__ACCEPT__</button>
//					<!-- FONCTION AcceptProposal -->
//				</div>
        		</div>
        
        [IF [!Role!]!=Buyer&&[!Role!]!=Supplier]
            <p>__ERR_NO_AUTH__</p>
        [/IF]    
    [/STORPROC]
    [NORESULT]
        <p>__ERR_NO_THIRD__</p>
    [NORESULT]
[/STORPROC]
