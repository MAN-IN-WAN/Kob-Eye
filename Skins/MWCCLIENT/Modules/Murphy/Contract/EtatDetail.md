[STORPROC Murphy/Contract/[!Id!]|Con][/STORPROC]
	<!-- buyer and supplier -->
<div class="form-horizontal">
	<div class="alert alert-info"><strong>__BUYER__</strong></div>
	<div class="row-fluid ">
		[STORPROC Murphy/Third/Contract.ContractBuyerId/[!Con::Id!]|Buyer|0|1][/STORPROC]
		<div class="span12 well" >
			[MODULE Murphy/Third/MiniEtat?Id=[!Buyer::Id!]&OneCol=1]
		</div>
	</div>
	<div class="alert alert-info"><strong>__SUPPLIER__</strong></div>
	<div class="row-fluid ">
		[STORPROC Murphy/Third/Contract.ContractSupplierId/[!Con::Id!]|Supplier|0|1][/STORPROC]
		<div class="span12 well" >
			[MODULE Murphy/Third/MiniEtat?Id=[!Supplier::Id!]&OneCol=1]
		</div>
	</div>
</div>

<div class="alert alert-info"><strong>__CONTRACT_DETAIL__</strong></div>
<div class="well form-horizontal">
	<div class="row-fluid">
		<div class="span5 ConDate">
			<span class="label">Ref: [!Con::Reference!]</span>
			[STORPROC Murphy/Status/[!Con::Status!]|Item]
				<span class="label" style="background-color:[!Item::Color!]" class="pull-right">[!Item::Status!]</span>
			[/STORPROC]
		</div>
		<div class="span6 offset1">
		</div>
	</div>
	[IF [!Prop::ShowBuyer!]]
		[STORPROC Murphy/Third/Contract/[!Con::Id!]|B|0|1][/STORPROC]
	<div class="row-fluid">
		<div class="span4">
			<div class="control-group" style="margin-bottom:0">
				<label class="control-label" style="padding-top:0">__NOM_ACHETEUR__</label>
				<div class="controls">
					<strong>[!B::Company!]</strong>
				</div>
			</div>
		</div>
	</div>
	[/IF]
	<div class="row-fluid">
	    <div class="span6">
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__PAYS__</label>
	            <div class="controls">
	                <strong>
	                [IF [!Con::CountryWine!]>0]
	                	[STORPROC Murphy/Country/[!Con::CountryWine!]|Item]
	                		[!Item::Country!]
	                	[/STORPROC]
	                [ELSE]__N/P__[/IF]
	                </strong>
	            </div>
	        </div>
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label pull-left" style="padding-top:0">__VARIETEE__</label>
	            <div class="controls">
	                <strong>
	                [IF [!Con::Varietal!]>0]
	                	[STORPROC Murphy/Varietal/[!Con::Varietal!]|Item]
	                		[!Item::Varietal!]
	                	[/STORPROC]
	                [ELSE]__N/P__[/IF]
	                </strong>
	            </div>
	        </div>
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__COLOR__</label>
	            <div class="controls">
	                <strong>
	                [IF [!Con::Colour!]>0]
	                	[STORPROC Murphy/Colour/[!Con::Colour!]|Item]
	                		[!Item::Colour!]
	                	[/STORPROC]
	                [ELSE]__N/P__[/IF]
	                </strong>
	            </div>
	        </div>
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__APPELLATION__</label>
	            <div class="controls">
	                <strong>
	                [IF [!Con::Appellation!]>0]
	                	[STORPROC Murphy/Appellation/[!Con::Appellation!]|Item]
	                		[!Item::Appellation!]
	                	[/STORPROC]
	                [ELSE]__N/P__[/IF]
	               </strong>
	            </div>
	        </div>
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__MILLESIME__</label>
	            <div class="controls">
	                <strong>[IF [!Con::Vintage!]!=][!Con::Vintage!][ELSE]__N/P__[/IF]</strong>
	            </div>
	        </div>
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__FILTRATION__</label>
	            <div class="controls">
	                <strong>
	                [IF [!Con::Filtration!]>0]
	                	[STORPROC Murphy/Filtration/[!Con::Filtration!]|Item]
	                		[!Item::Filtration!]
	                	[/STORPROC]
	                [ELSE]__N/P__[/IF]
	                </strong>
	            </div>
	        </div>
	    </div>
	    <div class="span6">
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__VOLUME__</label>
	            <div class="controls">
	                <strong>[IF [!Con::Volume!]!=][UTIL NUMBER][!Con::Volume!][/UTIL]
	                	[STORPROC Murphy/Quantity/[!Con::Quantity!]|Item]
	                		[!Item::Quantity!]
	                	[/STORPROC]

			[ELSE]__N/P__[/IF]</strong>
	            </div>
	        </div>
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__TRANSPORTATION__</label>
	            <div class="controls">
	                <strong>
	                [IF [!Con::Transportation!]>0]
	                	[STORPROC Murphy/Transportation/[!Con::Transportation!]|Item]
	                		[!Item::Transportation!]
	                	[/STORPROC]
	                [ELSE]__N/P__[/IF]
	                </strong>
	            </div>
	        </div>
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__INCO__</label>
	            <div class="controls">
	                <strong>
	                [IF [!Con::Inco!]!=]
	                	[STORPROC Murphy/IncoTerm/[!Con::Inco!]|Item]
	                		[!Item::Inco!]
	                	[/STORPROC]
	                [ELSE]__N/P__[/IF]
	                </strong>
	            </div>
	        </div>
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__PAYMENT__</label>
	            <div class="controls">
	                <strong>
	                [IF [!Con::Payment!]>0]
	                	[STORPROC Murphy/PaymentTerm/[!Con::Payment!]|Item]
	                		[!Item::Payment!]
	                	[/STORPROC]
	                [ELSE]__N/P__[/IF]
	                </strong>
	            </div>
	        </div>
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__DATE_LIMITE__</label>
	            <div class="controls">
	                <strong>[IF [!Con::Date!]!=][!Utils::getDate(d/m/Y,[!Con::ShippingDate!])!][ELSE]__N/P__[/IF]</strong>
	            </div>
	        </div>
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__UNIT_PRICE__</label>
	            <div class="controls">
	                <strong>
	                	[!Con::UnitPrice!][STORPROC Murphy/Currency/[!Con::Currency!]|C|0|1] [!C::Currency!][/STORPROC] / [IF [!Con::Quantity!]>0][STORPROC Murphy/Quantity/[!Con::Quantity!]|Item][!Item::Quantity!][/STORPROC][/IF]
			</strong>
	            </div>
	     </div>
<!--	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__TOTAL__</label>
	            <div class="controls">
	                <strong>[UTIL NUMBER][!Con::Total!][/UTIL][STORPROC Murphy/Currency/[!Con::Currency!]|C|0|1] [!C::Currency!][/STORPROC]</strong>
	            </div>
	     </div>-->
	    </div>
	</div>
	<div class="row-fluid">
	    <div class="span10">
            <h5 style="margin-bottom:0;"><em>__COMMENTAIRES__</em></h5>
            <em>
            	[!Con::PublicNotes!]
            </em>
	    </div>
	</div>
</div>
<div class="alert alert-info"><strong>__SHIPMENT_STATUS__</strong></div>

<div class="well">
	<div class="row-fluid">
		<div class="span12">
			<div class="progress ">
				[!R:=[!Con::Remains:/[!Con::Volume!]!]!]
				[!P:=[!1:-[!R!]!]!]
				<div class="bar bar-success" style="width: [!P:*100!]%;">
					__LOADED__: [UTIL NUMBER][!Con::Volume:-[!Con::Remains!]!][/UTIL] &nbsp;
					//[IF [!Con::Quantity!]>0][STORPROC Murphy/Quantity/[!Con::Quantity!]|Item][!Item::Quantity!][/STORPROC][/IF]
				</div>
				<div class="bar" style="width:[!R:*100!]%;">
					__REMAINS__: [UTIL NUMBER][!Con::Remains!][/UTIL] &nbsp;
					//[IF [!Con::Quantity!]>0][STORPROC Murphy/Quantity/[!Con::Quantity!]|Item][!Item::Quantity!][/STORPROC][/IF]
				</div>
			</div>
		</div>
	</div>
</div>

