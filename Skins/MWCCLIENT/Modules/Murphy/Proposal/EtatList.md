[STORPROC Murphy/Proposal/[!Id!]|Prop][/STORPROC]
            <div class="row-fluid">
                <div class="span6 EnqDate">
                    <span class="label [!filter!]">Ref: [!Prop::Reference!]</span>
                   [STORPROC Murphy/Status/[!Prop::Status!]|Item]
		        <span class="label" style="background-color:[!Item::Color!]">[!Item::Status!]</span>
                   [/STORPROC]
                </div>
                <div class="span6 ">
                    <i class="icon-share-alt"></i>
                    <strong>__DATE_LIMITE__ : [!Utils::getDate(d/m/Y,[!Prop::ShippingDate!])!]</strong>
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
                <div class="span6">
                	[IF [!Prop::ShowSupplier!]]
                		[STORPROC Murphy/Third.ProposalSupplierId/Proposal/[!Prop::Id!]|Sup]
                		<strong>__SUPPLIER__ :[!Sup::Company!]</strong>
                		[/STORPROC]
                	[/IF]
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <span class="showTooltip" title="__PAYS__,__VARIETEE__,__COLOUR__, __APPELLATION__, __MILLESIME__">
                        <i class="icon-glass"></i>
			[!Virgule:=0!]
			[IF [!Prop::CountryWine!]]
				[STORPROC Murphy/Country/[!Prop::CountryWine!]|Item|0|1]
					[!Item::CountryWine!]
					[!Virgule:=1!]
				[/STORPROC]
			[/IF]
			[IF [!Prop::Varietal!]]
				[IF [!Virgule!]],[/IF]
				[STORPROC Murphy/Varietal/[!Prop::Varietal!]|Item|0|1]
					[!Item::Varietal!]
					[!Virgule:=1!]
				[/STORPROC]
			[/IF]
			[IF [!Prop::Colour!]]
				[IF [!Virgule!]],[/IF]
				[STORPROC Murphy/Colour/[!Prop::Colour!]|Item|0|1]
					[!Item::Colour!]
					[!Virgule:=1!]
				[/STORPROC]
			[/IF]
			[IF [!Prop::Appellation!]]
				[IF [!Virgule!]],[/IF]
				[STORPROC Murphy/Appellation/[!Prop::Appellation!]|Item|0|1]
					[!Item::Appellation!]
					[!Virgule:=1!]
				[/STORPROC]
			[/IF]
                        [IF [!Virgule!]],[/IF]
                        [!Prop::Vintage!]
                    </span>
                </div>
                <div class="span2">
                    <span class="showTooltip" title="__VOLUME__">
                        <i class="icon-tint"></i>
                        [IF [!Prop::Volume!]][UTIL NUMBER][!Prop::Volume!][/UTIL]
				[IF [!Prop::Quantity!]]
					[STORPROC Murphy/Quantity/[!Prop::Quantity!]|Item]
						[!Item::Quantity!]
					[/STORPROC]
				[/IF]
			[ELSE]__N/P__[/IF]
                    </span>
                </div>
                <div class="span4">
                    <span class="showTooltip" title="__FILTRATION__">
                        <i class="icon-filter"></i>
                        [STORPROC Murphy/Filtration/[!Prop::Filtration!]|Item|0|1]
                        	[!Item::Filtration!]
				[NORESULT]
					__N/P__
				[/NORESULT]
                        [/STORPROC]
                    </span>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span4">
                    <span class="showTooltip" title="__UNIT_PRICE__, __QUANTITY__, __CURRENCY__">
                        <i class="icon-barcode"></i>
                        [UTIL PRICE][!Prop::UnitPrice!][/UTIL]
			[IF [!Prop::Currency!]]
				[STORPROC Murphy/Currency/[!Prop::Currency!]|Item]
					[!Item::Currency!]
				[/STORPROC]
			[/IF]
			[IF [!Prop::Unit!]]
				[STORPROC Murphy/Unit/[!Prop::Unit!]|Item]
					/ [!Item::Unit!]
				[/STORPROC]
			[/IF]
                    </span>
                </div>
                <div class="span2">
		[IF [!Prop::Transportation!]>0]
                    <span class="showTooltip" title="__TRANSPORTATION__">
			<i class="icon-road"></i>
			[IF [!Prop::Transportation!]]
				[STORPROC Murphy/Transportation/[!Prop::Transportation!]|Item]
					[!Item::Transportation!]
				[/STORPROC]
			[/IF]
                    </span>
		[/IF]
		</div>
		<div class="span2">
		[IF [!Prop::Inco!]>0]
                    <span class="showTooltip" title="__INCO__">
			<i class="icon-flag"></i>
			[IF [!Prop::Inco!]]
				[STORPROC Murphy/IncoTerm/[!Prop::Inco!]|Item]
					[!Item::Inco!]
				[/STORPROC]
			[/IF]
                    </span>
		[/IF]
		</div>
                <div class="span4">
		[IF [!Prop::Payment!]>0]
                    <span class="showTooltip" title="__PAYMENT__">
			<i class="icon-lock"></i>
			[IF [!Prop::Payment!]]
				[STORPROC Murphy/PaymentTerm/[!Prop::Payment!]|Item]
					[!Item::Payment!]
				[/STORPROC]
			[/IF]
                    </span>
		[/IF]
                </div>
            </div>
        [IF [!Prop::Trucks!]]
            <div class="row-fluid">
                <div class="span4">
			
                    <span>
			__TRUCKS_COUNT__: <strong>[!Prop::Trucks!]</strong>
                    </span>
                </div>
	    </div>
        [/IF]
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
