[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1][/STORPROC]
        <div class="well">
            <div class="row-fluid">
                <div class="span5 EnqDate">
                    <span class="label [!filter!]">Ref: [!Enq::Reference!]</span>
                	<span class="label" style="background-color:[!Enq::StatusColour!]" class="pull-right">[!Enq::Status!]</span>
                </div>
                <div class="span6 offset1">
                    <a href="/[!Systeme::getMenu(Murphy/Enquiry)!]/[!Enq::Id!]" class="btn btn-murphy pull-right">__DETAILS__</a>
		    [COUNT Murphy/Enquiry/[!Enq::Id!]/Proposal/StatusId=[!CONF::MODULE::MURPHY::STP_ANSWERED!]+StatusId=[!CONF::MODULE::MURPHY::STP_VALIDATED!]&Third.ProposalBuyerId([!Th::Id!])|NbArch]
		    [IF [!NbArch!]]
			    //EN ATTENTE
			    <a href="/[!Systeme::getMenu(Murphy/Enquiry)!]/[!Enq::Id!]" class="btn btn-danger pull-right" style="margin-right:10px;">__WAITING_PROPOSAL__<span class="badge pull-right" style="margin:1px 0px 0px 10px;background-color: white;color:black">[!NbArch!]</span></a></li>
		    [/IF]
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <i class="icon-info-sign"></i>
                    <strong>__DATE_CREATION__  [DATE d/m/Y][!Enq::Date!][/DATE]</strong>
                </div>
                <div class="span4">
                </div>
            </div>
            
            <div class="row-fluid">
                <div class="span6">
                    <span class="showTooltip" title="__PAYS__,__COLOR__,__VARIETEE__, __APPELLATION__, __MILLESIME__">
                        <i class="icon-glass"></i>
                        [IF [!Enq::Country!]][!Enq::Country!], [/IF]
                        [!Enq::Varietal!]
                        [IF [!Enq::Varietal!]!=&&[!Enq::Appellation!]!=],[/IF]
                        [IF [!Enq::Colour!]][!Enq::Colour!], [/IF]
                        [!Enq::Appellation!]
                        [IF [!Enq::Vintage!]!=],[/IF]
                        [!Enq::Vintage!]
                    </span>
                </div>
                <div class="span2">
                    <span class="showTooltip" title="__VOLUME__">
                        <i class="icon-tint"></i>
                        [IF [!Enq::Volume!]][UTIL NUMBER][!Enq::Volume!][/UTIL] [STORPROC Murphy/Quantity/[!Enq::Quantity!]|Q|0|1] [!Q::Quantity!][/STORPROC][ELSE]__N/P__[/IF]
                    </span>
                </div>
                <div class="span4">
                    <i class="icon-share-alt"></i>
                    <strong>__DATE_LIMITE__ : [!Utils::getDate(d/m/Y,[!Enq::ShippingDate!])!]</strong>
                    <!--<span class="showTooltip" title="__FILTRATION__">
                        <i class="icon-filter"></i>
                        [IF [!Enq::Filtration!]][!Enq::Filtration!][ELSE]__N/P__[/IF]
                    </span>-->
                </div>
            </div>
        </div>














