       <div class="well">
            <div class="row-fluid">
                <div class="span6 EnqDate">
                    <span class="label [!filter!]">Ref: [!Prop::Reference!]</span>
                	<span class="label" style="background-color:[!Prop::StatusColor!]" class="pull-right">[!Prop::Status!]</span>
                </div>
                <div class="span6 ">
                    [SWITCH [!Prop::StatusId!]|=]
                        [CASE [!CONF::MODULE::MURPHY::STP_DRAFT!]]
                            <a style="margin-left:10px" onclick="return confirm('__CONFIRM_REFUS__')" class="btn btn-grey pull-right" href="/Murphy/Proposal/[!Prop::Id!]/Cancel">__REFUSER__</a>
                            <a style="margin-left:10px" class="btn btn-warning pull-right" href="/[!Systeme::getMenu(Murphy/Proposal)!]/[!Prop::Id!]">__MAKE_PROPOSAL__</a>
                        [/CASE]
                        [CASE [!CONF::MODULE::MURPHY::STP_REVISE!]]
                            <a style="margin-left:10px" onclick="return confirm('__CONFIRM_REFUS__')" class="btn btn-grey pull-right" href="/Murphy/Proposal/[!Prop::Id!]/Cancel">__REFUSER__</a>
                            <a style="margin-left:10px" class="btn btn-inverse pull-right" href="/[!Systeme::getMenu(Murphy/Proposal)!]/[!Prop::Id!]">__REMAKE_PROPOSAL__</a>
                        [/CASE]
                        [DEFAULT]
                        [/DEFAULT]
                    [/SWITCH]
                    <a href="/[!Systeme::getMenu(Murphy/Proposal)!]/[!Prop::Id!]" class="btn btn-murphy pull-right">__DETAILS__</a>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <i class="icon-info-sign"></i>
                    <strong>__DATE_CREATION__  [DATE d/m/Y][!Prop::Date!][/DATE]</strong>
                </div>
                <div class="span6">
                [IF [!Prop::UnitPrice!]>0]
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
                    [/IF]
                </div>
            </div>
            
            <div class="row-fluid">
                <div class="span6">
                    <span class="showTooltip" title="__PAYS__,__COLOR__,__VARIETEE__, __APPELLATION__, __MILLESIME__">
                        <i class="icon-glass"></i>
                        [IF [!Prop::Country!]][!Prop::Country!], [/IF]
                        [!Prop::Varietal!]
                        [IF [!Prop::Varietal!]!=&&[!Prop::Appellation!]!=],[/IF]
                        [IF [!Prop::Colour!]][!Prop::Colour!], [/IF]
                        [!Prop::Appellation!]
                        [IF [!Prop::Vintage!]!=],[/IF]
                        [!Prop::Vintage!]
                    </span>
                </div>
                <div class="span2">
                    <span class="showTooltip" title="__VOLUME__">
                        <i class="icon-tint"></i>
                        [IF [!Prop::Volume!]][UTIL NUMBER][!Prop::Volume!][/UTIL] [STORPROC Murphy/Quantity/[!Prop::Quantity!]|Q|0|1] [!Q::Quantity!][/STORPROC][ELSE]__N/P__[/IF]
                    </span>
                </div>
                <div class="span4">
                    <i class="icon-share-alt"></i>
                    <strong>__DATE_LIMITE__ : [!Utils::getDate(d/m/Y,[!Prop::ShippingDate!])!]</strong>
                    <!--<span class="showTooltip" title="__FILTRATION__">
                        <i class="icon-filter"></i>
                        [IF [!Prop::Filtration!]][!Prop::Filtration!][ELSE]__N/P__[/IF]
                    </span>-->
                </div>
            </div>
            [IF [!Prop::StatusId!]=7]
                [STORPROC Murphy/Enquiry/[!Enq::Id!]/Contract|Contract]
                    [LIMIT 0|1]
                        <div class="Contract">
                            [MODULE Murphy/Contract/Summary?Co=[!Contract!]]
                        </div>
                    [/LIMIT]
                [/STORPROC]
            [/IF]
            <!--
            <div class="row-fluid">
                <div class="span2"></div>
                <div class="span6 offset4">
                    [SWITCH [!Prop::StatusId!]|=]
                        [CASE [!CONF::MODULE::MURPHY::STP_DRAFT!]]
                            <a style="margin-left:10px" onclick="return confirm('__CONFIRM_REFUS__')" class="btn btn-danger pull-right" href="/Murphy/Proposal/[!Prop::Id!]/Cancel">__REFUSER__</a>
                            <a class="btn btn-inverse pull-right" href="/[!Systeme::getMenu(Murphy/Proposal)!]/[!Prop::Id!]">__MAKE_PROPOSAL__</a>
                        [/CASE]
                        [CASE [!CONF::MODULE::MURPHY::STP_REVISE!]]
                            <a style="margin-left:10px" onclick="return confirm('__CONFIRM_REFUS__')" class="btn btn-danger pull-right" href="/Murphy/Proposal/[!Prop::Id!]/Cancel">__REFUSER__</a>
                            <a class="btn btn-inverse pull-right" href="/[!Systeme::getMenu(Murphy/Proposal)!]/[!Prop::Id!]">__REMAKE_PROPOSAL__</a>
                        [/CASE]
                        [DEFAULT]
		                	<span class="label" style="background-color:[!Prop::StatusColor!]" class="pull-right">[!Prop::Status!]</span>
		                    <a href="/[!Systeme::getMenu(Murphy/Proposal)!]/[!Prop::Id!]" class="btn btn-inverse pull-right">__DETAILS__</a>
                        [/DEFAULT]
                    [/SWITCH]
                </div>
            </div>
            -->
     </div>
