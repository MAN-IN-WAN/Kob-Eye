[STORPROC Murphy/Third/Contract.ContractSupplierId/[!Con::Id!]|Supplier|0|1][/STORPROC]
[STORPROC Murphy/Third/Contract.ContractBuyerId/[!Con::Id!]|Buyer|0|1][/STORPROC]
[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th][/STORPROC]
        <div class="form-horizontal well">
            <div class="row-fluid">
                <div class="span5 ConDate">
                    <span class="label [!filter!]">Ref: [!Con::Reference!]</span>
                    <span class="label" style="background-color:[!Con::StatusColour!]" class="pull-right">[!Con::Status!]</span>
                </div>
                <div class="span6 offset1">
	    [IF [!Th::Id!]=[!Buyer::Id!]&&[!Con::BuyerStatusId!]=[!CONF::MODULE::MURPHY::STC_SENT!]]
                    <a style="margin-left:10px" href="/[!Systeme::getMenu(Murphy/Contract)!]/[!Con::Id!]" class="btn btn-success pull-right">__CONFIRM_DETAILS__</a>
	    [/IF]
	    [IF [!Th::Id!]=[!Supplier::Id!]&&[!Con::SupplierStatusId!]=[!CONF::MODULE::MURPHY::STC_SENT!]]
                    <a style="margin-left:10px" href="/[!Systeme::getMenu(Murphy/Contract)!]/[!Con::Id!]" class="btn btn-success pull-right">__CONFIRM_DETAILS__</a>
	    [/IF]
                    <a style="margin-left:10px" href="/[!Systeme::getMenu(Murphy/Contract)!]/[!Con::Id!]" class="btn btn-murphy pull-right">__DETAILS__</a>
			<a style="margin-left:10px" href="/Home/Murphy/Contract_[!Con::Reference!].pdf"  class="btn btn-warning pull-right" target="_blank">__PRINT__</a>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <i class="icon-info-sign"></i>
                    <strong>__DATE_CREATION__  [DATE d/m/Y][!Con::Date!][/DATE]</strong>
                </div>
            </div>
	    <!-- PROPERTIES -->
            <div class="row-fluid">
		<!-- FIRST COLUMN -->
                <div class="span6">
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__TOTAL__</label>
			    <div class="controls">
				    <strong>[UTIL NUMBER][!Con::Total!][/UTIL][STORPROC Murphy/Currency/[!Con::Currency!]|C|0|1] [!C::Currency!][/STORPROC]</strong>
			    </div>
		    </div>
		    
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__VOLUME__</label>
			    <div class="controls">
				    <strong>
					[IF [!Con::Volume!]!=][UTIL NUMBER][!Con::Volume!][/UTIL]
					[IF [!Con::Quantity!]>0]
						[STORPROC Murphy/Quantity/[!Con::Quantity!]|Item]
							[!Item::Quantity!]
						[/STORPROC]
					[/IF]
		
					[ELSE]__N/P__[/IF]
				    </strong>
			    </div>
		    </div>
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__BUYER__</label>
			    <div class="controls">
				    <strong>
					[!Buyer::Company!]
				    </strong>
			    </div>
		    </div>
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__SUPPLIER__</label>
			    <div class="controls">
				    <strong>
					[!Supplier::Company!]
				    </strong>
			    </div>
		    </div>
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__DATE_LIMITE__</label>
			    <div class="controls">
				    <strong>
					[!Utils::getDate(d/m/Y,[!Con::ShippingDate!])!]
				    </strong>
			    </div>
		    </div>
                </div>

		<!-- SECOND COLUMN -->
                <div class="span6">
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__PAYS__</label>
			    <div class="controls">
				    <strong>
					[!Con::Country!]
				    </strong>
			    </div>
		    </div>
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__VARIETEE__</label>
			    <div class="controls">
				    <strong>
					[!Con::Varietal!]
				    </strong>
			    </div>
		    </div>
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__COLOR__</label>
			    <div class="controls">
				    <strong>
					[!Con::Colour!]
				    </strong>
			    </div>
		    </div>
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__APPELLATION__</label>
			    <div class="controls">
				    <strong>
					[!Con::Appellation!]
				    </strong>
			    </div>
		    </div>
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__MILLESIME__</label>
			    <div class="controls">
				    <strong>
					 [!Con::Vintage!]
				    </strong>
			    </div>
		    </div>
                </div>
            </div>
	    
	    <!-- PROGRESSION BAR -->
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














