// Ficher d'un contrat

[STORPROC [!Query!]|Co][/STORPROC]

[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1]
        [STORPROC Murphy/Third/[!Th::Id!]/Contract.ContractBuyerId/[!Co::Id!]|Co|0|1]
            [!Role:=Buyer!]
        [/STORPROC]
    
        [STORPROC Murphy/Third/[!Th::Id!]/Contract.ContractSupplierId/[!Co::Id!]|Co|0|1]
            [!Role:=Supplier!]
        [/STORPROC]        


	[IF [!Role!]=Buyer||[!Role!]=Supplier]
		// ACCEPTATION
		[IF [!AcceptContract!]]
			[METHOD Co|[!Role!]Acceptance][/METHOD]
			[REDIRECT][!Lien!][/REDIRECT]
		[/IF]
	
		// REFUS
		[IF [!RefuseContract!]]
			[METHOD Co|[!Role!]Refusal][/METHOD]
			[REDIRECT][!Lien!][/REDIRECT]
		[/IF]
		<div class="well">
			<h3 style="">__CONTRACT__ [!Co::Reference!] ([!Role!])</h3>
		</div>

		[MODULE Murphy/Contract/EtatDetail?Id=[!Co::Id!]]
		[IF [!Co::[!Role!]Status!]=[!CONF::MODULE::MURPHY::STC_SENT!]]
			<div class="well">
					<div class="row-fluid" style="margin-top:10px;">
						<div class="span6">
							<form method="post" action="/[!Lien!]">
								<button name="accept"  onclick="this.form.submitForm.value=1;this.form.submit()" class="btn btn-success btn-block">__ACCEPT__</button>
								<!-- FONCTION AcceptContract -->
								<input type="hidden" name="AcceptContract" value="1" />
							</form>
						</div>
						<div class="span6">
							<form method="post" action="/[!Lien!]">
								<button name="refuse" onclick="this.form.submitForm.value=2;this.form.submit()" class="btn btn-danger btn-block">__REFUSER__</button>
								<!-- FONCTION RefuseContract -->
								<input type="hidden" name="RefuseContract" value="1" />
							</form>
						</div>
					</div>
			</div>
		[/IF]
		<div class="well">
			<div class="row-fluid" style="margin-top:10px;">
				<div class="span12">
					<a href="/Home/Murphy/Contract_[!Co::Reference!].pdf"  onclick="this.form.submitForm.value=1;this.form.submit()" class="btn btn-murphy btn-block" target="_blank">__PRINT__</a>
				</div>
			</div>
		</div>

		//**************************************
		// SHIPMENTS
		//**************************************
		[IF [!Co::Status!]>=[!CONF::MODULE::MURPHY::STC_CONFIRMED!]&&[!Co::Status!]<[!CONF::MODULE::MURPHY::STC_CANCELLED!]]
			<h3>__SHIPMENT_LIST__</h3>
			//recuperation des shipments
			[STORPROC Murphy/Contract/[!Co::Id!]/Shipment/StatusId>=[!CONF::MODULE::MURPHY::SHP_PLANNED!]|SH|0|100|LoadingDate|DESC]
				[MODULE Murphy/Shipment/EtatDetail?Id=[!SH::Id!]]
			[/STORPROC]
		[/IF]
	[ELSE]
        	<p>__ERR_NO_AUTH__</p>
        [/IF]    
    [/STORPROC]
    [NORESULT]
        <p>__ERR_NO_THIRD__</p>
    [NORESULT]
[/STORPROC]
