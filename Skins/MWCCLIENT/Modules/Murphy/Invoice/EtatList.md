[STORPROC Murphy/Invoice/[!Inv::Id!]|Inv][/STORPROC]
	<div class="well form-horizontal">
		<div class="row-fluid">
			<div class="span5 ConDate">
				<span class="label [!filter!]">Ref: [!Inv::Reference!]</span>
				[IF [!Inv::Paid!]]
					<span class="label label-success" class="pull-right">__PAID__</span>
				[ELSE]
					<span class="label label-important" class="pull-right">__NOT_PAID__</span>
				[/IF]
			</div>
			<div class="span6 offset1">
				<a href="/Home/Murphy/Invoice_[!Inv::Reference!].pdf" class="btn btn-murphy pull-right" target="_blank">__PRINT__</a>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<i class="icon-info-sign"></i>
				<strong>__DATE_CREATION__  [DATE d/m/Y][!Inv::Date!][/DATE]</strong>
			</div>
			<div class="span4">
			</div>
		</div>
		
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group" style="margin-bottom:0">
					<label class="control-label" style="padding-top:0">__TOTAL_TE__</label>
					<div class="controls">
						<strong>
						   [UTIL NUMBER][!Inv::TotalTE!][/UTIL] 
							[IF [!Inv::Currency!]>0]
								[STORPROC Murphy/Currency/[!Inv::Currency!]|Item]
									[!Item::Currency!]
								[/STORPROC]
							[/IF]
						</strong>
					</div>
				</div>
				<div class="control-group" style="margin-bottom:0">
					<label class="control-label" style="padding-top:0">__VAT_RATE__</label>
					<div class="controls">
						<strong>
						   [!Inv::VATRate!] %
						</strong>
					</div>
				</div>
				<div class="control-group" style="margin-bottom:0">
					<label class="control-label" style="padding-top:0">__VAT_AMOUNT__</label>
					<div class="controls">
						<strong>
						  [!Inv::VATAmount!] [IF [!Inv::Currency!]>0]
							[STORPROC Murphy/Currency/[!Inv::Currency!]|Item]
								[!Item::Currency!]
							[/STORPROC]
						[/IF]
						</strong>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group" style="margin-bottom:0">
					<label class="control-label" style="padding-top:0">__TOTAL_TI__</label>
					<div class="controls">
						<strong>
						  [UTIL NUMBER][!Inv::TotalTI!][/UTIL] [IF [!Inv::Currency!]>0]
							[STORPROC Murphy/Currency/[!Inv::Currency!]|Item]
								[!Item::Currency!]
							[/STORPROC]
						[/IF]
						</strong>
					</div>
				</div>
				
				[IF [!Inv::Paid!]]
				<div class="control-group" style="margin-bottom:0">
					<label class="control-label" style="padding-top:0">__DATE_PAYMENT__</label>
					<div class="controls">
						<strong>
						  [DATE d/m/Y][!Inv::PaymentDate!][/DATE]
						</strong>
					</div>
				</div>
				[/IF]
			</div>
		</div>
	</div>















