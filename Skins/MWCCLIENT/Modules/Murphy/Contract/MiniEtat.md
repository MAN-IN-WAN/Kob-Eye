[STORPROC Murphy/Contract/Id=[!Co::Id!]|Co|0|1][/STORPROC]
<div class="well">
	<!-- Reference et status -->
	<div class="row-fluid">
		<div class="span6">
			<i class="icon-info-sign"></i>
			<strong>__PROPOSAL__ [!Co::Reference!]  [!Co::StatusId!]</strong>
		</div>
		<div class="span4">
			<i class="icon-share-alt"></i>
			<strong>__DATE_LIMITE__ : [!Utils::getDate(d/m/Y,[!Co::ShippingDate!])!]</strong>
		</div>
	</div>
	
	<!-- buyer and supplier -->
	[STORPROC Murphy/Third/Contract.ContractBuyerId/[!Co::Id!]|Buyer|0|1][/STORPROC]
	[STORPROC Murphy/Third/Contract.ContractSupplierId/[!Co::Id!]|Supplier|0|1][/STORPROC]
	<div class="row-fluid">
		<div class="span5">
			<h4>__SUPPLIER__</h4>
			
		</div>
		<div class="span5">
			<h4>__BUYER__</h4>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span6">
			<span class="showTooltip" title="__COUNTRY__,__VARIETEE__,__COLOUR__, __APPELLATION__, __MILLESIME__">
			<i class="icon-glass"></i>
			[!Co::Varietal!]
			[IF [!Co::Varietal!]!=&&[!Co::Appellation!]!=],[/IF]
			[!Co::Appellation!]
			[IF [!Co::Vintage!]!=],[/IF]
			[!Co::Vintage!]
			</span>
		</div>
		<div class="span2">
			<span class="showTooltip" title="__VOLUME__">
			<i class="icon-tint"></i>
			[IF [!Co::Volume!]][!Co::Volume!] L[ELSE]__N/P__[/IF]
			</span>
		</div>
		<div class="span4">
			<span class="showTooltip" title="__FILTRATION__">
			<i class="icon-filter"></i>
			[IF [!Co::Filtration!]][!Co::Filtration!][ELSE]__N/P__[/IF]
			</span>
		</div>
	</div>
</div>






