[STORPROC Murphy/Shipment/[!Id!]|Ship][/STORPROC]
<div class="well form-horizontal">
	<div class="row-fluid">
		<div class="span5 ConDate">
			<span class="label">__LOADING_DATE__: [DATE d/m/Y][!Ship::LoadingDate!][/DATE]</span>
			[STORPROC Murphy/Status/[!Ship::Status!]|Item]
				<span class="label" style="background-color:[!Item::Color!]" class="pull-right">[!Item::Status!]</span>
			[/STORPROC]
		</div>
		<div class="span6 offset1">
			<div class="control-group" style="margin-bottom:0">
				<label class="control-label" style="padding-top:0">__VOLUME__</label>
				<div class="controls">
					<strong>
					[IF [!Ship::Volume!]!=][UTIL NUMBER][!Ship::Volume!][/UTIL][ELSE]__N/P__[/IF]
					</strong>
				</div>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span5 ConDate">
			<div class="control-group" style="margin-bottom:0">
				<label class="control-label" style="padding-top:0">__PURCHASE_ORDER__</label>
				<div class="controls">
					<strong>
					[IF [!Ship::PurchaseOrder!]][!Ship::PurchaseOrder!][ELSE]__N/P__[/IF]
					</strong>
				</div>
			</div>
		</div>
	</div>
</div>
