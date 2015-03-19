<div class="form-horizontal">
[STORPROC Murphy/Proposal/[!Id!]|Prop][/STORPROC]
            <div class="row-fluid">
                <div class="span6 EnqDate">
                    <span class="label [!filter!]">Ref: [!Prop::Reference!]</span>
                   [STORPROC Murphy/Status/[!Prop::Status!]|Item]
		        <span class="label" style="background-color:[!Item::Color!]">[!Item::Status!]</span>
                   [/STORPROC]
                </div>
                <div class="span6 ">
			[IF [!NODETAIL!]][ELSE]
// MODIF PAR MYRIAM 23/09/2013 BUG DE LIEN
//			<a style="margin-left:10px" href="/[!Systeme::CurrentMenu::Url!]/[!Enq::Id!]" class="btn btn-murphy pull-right">__DETAILS__</a>
			<a href="/[!Systeme::getMenu(Murphy/Proposal)!]/[!Prop::Id!]" class="btn btn-murphy pull-right">__DETAILS__</a>
				
			[/IF]
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <i class="icon-info-sign"></i>
                    <strong>__DATE_CREATION__  [DATE d/m/Y][!Prop::Date!][/DATE]</strong>
		</div>
	    </div>
            <div class="row-fluid">
                <div class="span6">
		    [IF [!Prop::ShowSupplier!]]
			    [STORPROC Murphy/Third.ProposalSupplierId/Proposal/[!Prop::Id!]|Sup]
			    <div class="control-group" style="margin-bottom:0">
				    <label class="control-label" style="padding-top:0">__SUPPLIER__</label>
				    <div class="controls">
					    <strong>[!Sup::Company!]</strong>
				    </div>
			    </div>
			    [/STORPROC]
		    [/IF]
		    [IF [!Prop::CountryWine!]]
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__PAYS__</label>
			    <div class="controls">
				    <strong>
				    [STORPROC Murphy/Country/[!Prop::CountryWine!]|Item|0|1]
					    [!Item::Country!]
				    [/STORPROC]
				    </strong>
			    </div>
		    </div>
		    [/IF]
		    [IF [!Prop::Varietal!]]
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__VARIETEE__</label>
			    <div class="controls">
				    <strong>
				    [STORPROC Murphy/Varietal/[!Prop::Varietal!]|Item|0|1]
					    [!Item::Varietal!]
				    [/STORPROC]
				    </strong>
			    </div>
		    </div>
		    [/IF]
		    [IF [!Prop::Colour!]]
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__COLOR__</label>
			    <div class="controls">
				    <strong>
				    [STORPROC Murphy/Colour/[!Prop::Colour!]|Item|0|1]
					    [!Item::Colour!]
				    [/STORPROC]
				    </strong>
			    </div>
		    </div>
		    [/IF]
		    [IF [!Prop::Appellation!]]
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__APPELLATION__</label>
			    <div class="controls">
				    <strong>
				    [STORPROC Murphy/Appellation/[!Prop::Appellation!]|Item|0|1]
					    [!Item::Appellation!]
				    [/STORPROC]
				    </strong>
			    </div>
		    </div>
		    [/IF]
			[IF [!Prop::Vintage!]]
			<div class="control-group" style="margin-bottom:0">
				<label class="control-label" style="padding-top:0">__MILLESIME__</label>
				<div class="controls">
    					<strong>
		                        [!Prop::Vintage!]
					</strong>
				</div>
			</div>
			[/IF]
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__VOLUME__</label>
			    <div class="controls">
				    <strong>
				    [IF [!Prop::Volume!]][UTIL NUMBER][!Prop::Volume!][/UTIL]
					    [IF [!Prop::Quantity!]]
						    [STORPROC Murphy/Quantity/[!Prop::Quantity!]|Item]
							    [!Item::Quantity!]
						    [/STORPROC]
					    [/IF]
				    [ELSE]__N/P__[/IF]
				    </strong>
			    </div>
		    </div>
                </div>
                <div class="span6">
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__DATE_LIMITE__</label>
			    <div class="controls">
				    <strong>[!Utils::getDate(d/m/Y,[!Prop::ShippingDate!])!]</strong>
			    </div>
		    </div>
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__FILTRATION__</label>
			    <div class="controls">
				    <strong>
				    [STORPROC Murphy/Filtration/[!Prop::Filtration!]|Item|0|1]
					    [!Item::Filtration!]
					    [NORESULT]
						    __N/P__
					    [/NORESULT]
				    [/STORPROC]
				    </strong>
			    </div>
		    </div>
		    [IF [!Prop::Inco!]>0]
			<div class="control-group" style="margin-bottom:0">
				<label class="control-label" style="padding-top:0">__INCO__</label>
				<div class="controls">
					<strong>
					[STORPROC Murphy/IncoTerm/[!Prop::Inco!]|Item]
						[!Item::Inco!]
					[/STORPROC]
					</strong>
				</div>
			</div>
		    [/IF]
		    <div class="control-group" style="margin-bottom:0">
			    <label class="control-label" style="padding-top:0">__UNIT_PRICE__</label>
			    <div class="controls">
				    <strong>
	                            [UTIL PRICE][!Prop::UnitPrice!][/UTIL]
				    </strong>
			    </div>
		    </div>
		    [IF [!Prop::Currency!]]
			<div class="control-group" style="margin-bottom:0">
				<label class="control-label" style="padding-top:0">__CURRENCY__</label>
				<div class="controls">
					<strong>
						[STORPROC Murphy/Currency/[!Prop::Currency!]|Item]
							[!Item::Currency!]
						[/STORPROC]
					</strong>
				</div>
			</div>
		    [/IF]
		    [IF [!Prop::Payment!]>0]
			<div class="control-group" style="margin-bottom:0">
				<label class="control-label" style="padding-top:0">__PAYMENT__</label>
				<div class="controls">
					<strong>
					[STORPROC Murphy/PaymentTerm/[!Prop::Payment!]|Item]
						[!Item::Payment!]
					[/STORPROC]
					</strong>
				</div>
			</div>
		    [/IF]
		    [IF [!Prop::Transportation!]>0]
			<div class="control-group" style="margin-bottom:0">
				<label class="control-label" style="padding-top:0">__TRANSPORTATION__</label>
				<div class="controls">
					<strong>
					[STORPROC Murphy/Transportation/[!Prop::Transportation!]|Item]
						[!Item::Transportation!]
					[/STORPROC]
					</strong>
				</div>
			</div>
		    [/IF]
            [IF [!Prop::Trucks!]]
			<div class="control-group" style="margin-bottom:0">
				<label class="control-label" style="padding-top:0">__TRUCKS_COUNT__</label>
				<div class="controls">
					<strong>
					[!Prop::Trucks!]
					</strong>
				</div>
			</div>
	    [/IF]
		</div>
	    </div>
            [IF [!Prop::StatusId!]=7]
                [STORPROC Murphy/Enquiry/[!Enq::Id!]/Contract|Contract]
                    [LIMIT 0|1]
                        <div class="Contract">
                            [MODULE Murphy/Contract/EtatList?Con=[!Contract!]]
                        </div>
                    [/LIMIT]
                [/STORPROC]
            [/IF]
            [IF [!CONTROL!]]
            	<!-- AFFICHAGE CONTROL -->
                    [SWITCH [!Prop::StatusId!]|=]
                        [CASE [!CONF::MODULE::MURPHY::STP_DRAFT!]]
				<div class="row-fluid">
					<div class="span2"></div>
					<div class="span6 offset4">
						<a style="margin-left:10px" onclick="return confirm('__CONFIRM_REFUS__')" class="btn btn-danger pull-right" href="/Murphy/Proposal/[!Prop::Id!]/Cancel">__REFUSER__</a>
						<a class="btn btn-inverse pull-right" href="/[!Systeme::CurrentMenu::Url!]/[!Prop::Id!]">__MAKE_PROPOSAL__</a>
					</div>
				</div>
                        [/CASE]
                        [CASE [!CONF::MODULE::MURPHY::STP_REVISE!]]
				<div class="row-fluid">
					<div class="span2"></div>
					<div class="span6 offset4">
						<a style="margin-left:10px" onclick="return confirm('__CONFIRM_REFUS__')" class="btn btn-danger pull-right" href="/Murphy/Proposal/[!Prop::Id!]/Cancel">__REFUSER__</a>
						<a class="btn btn-inverse pull-right" href="/[!Systeme::CurrentMenu::Url!]/[!Prop::Id!]">__REMAKE_PROPOSAL__</a>
					</div>
				</div>
                        [/CASE]
                    [/SWITCH]
            [ELSE]
			    <!-- COMMENTAIRE -->
			        <div class="row-fluid">
			            <div class="span10"> 
		                    <h5><em>__COMMENTAIRES__</em></h5>
		                    <em>
		                    	[!Prop::Comments!]
		                    </em>
			            </div>
			        </div>
			    [IF [!Prop::SupplierComments!]]
			        <div class="row-fluid">
			            <div class="span10"> 
		                    <h5><em>__COMMENTAIRES_BUYER__</em></h5>
		                    <em>
		                    	[!Prop::SupplierComments!]
		                    </em>
			            </div>
			        </div>
			    [/IF]
            [/IF]
</div>
