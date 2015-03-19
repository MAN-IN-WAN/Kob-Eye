<h3>__LISTE_DEMANDES__</h3>

[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1]
    [STORPROC Murphy/Third/[!Th::Id!]/Enquiry|Enq|0|100|Date|ASC] 
        <div class="well">
            <div class="row-fluid">
                <div class="span3 EnqDate">
                    <i class="icon-share-alt"></i>
                    <strong>[!Utils::getDate(d/m/Y,[!Enq::ShippingDate!])!]</strong>
                </div>
                <div class="span2 offset7">
                    <a href="/[!Systeme::CurrentMenu::Url!]/[!Enq::Id!]" class="btn btn-inverse btn-block">__DETAILS__</a>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span3">
                    <span class="showTooltip" title="__VARIETEE__">
                        <i class="icon-glass"></i>
                        [IF [!Enq::Varietal!]][!Enq::Varietal!][ELSE]__N/P__[/IF]
                    </span>
                </div>
                <div class="span3">
                    <span class="showTooltip" title="__APPELLATION__">
                        <i class="icon-question-sign"></i>
                        [IF [!Enq::Appellation!]][!Enq::Appellation!][ELSE]__N/P__[/IF]
                    </span>
                </div>
                <div class="span2">
                    <span class="showTooltip" title="__QUANTITY__">
                        <i class="icon-tasks"></i>
                        [IF [!Enq::Quantity!]][!Enq::Quantity!][ELSE]__N/P__[/IF]
                    </span>
                </div>
                <div class="span4">
                    <span class="showTooltip" title="__FILTRATION__">
                        <i class="icon-filter"></i>
                        [IF [!Enq::Filtration!]][!Enq::Filtration!][ELSE]__N/P__[/IF]
                    </span>
                </div>
            </div>
            <div class="well darker">
                [STORPROC Murphy/Enquiry/[!Enq::Id!]/Proposal|Pro|0|100|tmsEdit|DESC]
                    <div class="Proposal [IF [!Pos!]=[!NbResult!]]LastProposal[/IF]">
                        <strong>__PROPOSAL__ [!Pro::Reference!]</strong>
                        [IF [!Pro::StatusId!]=3||[!Pro::StatusId!]=6||[!Pro::StatusId!]=7]
                            // 3: Answered | 6: Rejected | 7: Confirmed
                            <div class="row-fluid">
                                <div class="span3">
                                    <span class="showTooltip" title="__VARIETEE__">
                                        <i class="icon-glass"></i>
                                        [IF [!Pro::Varietal!]][!Pro::Varietal!][ELSE]__N/P__[/IF]
                                    </span>
                                </div>
                                <div class="span3">
                                    <span class="showTooltip" title="__APPELLATION__">
                                        <i class="icon-question-sign"></i>
                                        [IF [!Pro::Appellation!]][!Pro::Appellation!][ELSE]__N/P__[/IF]
                                    </span>
                                </div>
                                <div class="span2">
                                    <span class="showTooltip" title="__QUANTITY__">
                                        <i class="icon-tasks"></i>
                                        [IF [!Pro::Quantity!]][!Pro::Quantity!][ELSE]__N/P__[/IF]
                                    </span>
                                </div>
                                <div class="span4">
                                    <span class="showTooltip" title="__FILTRATION__">
                                        <i class="icon-filter"></i>
                                        [IF [!Pro::Filtration!]][!Pro::Filtration!][ELSE]__N/P__[/IF]
                                    </span>
                                </div>
                            </div>                     
                            <div class="row-fluid">
                                <div class="span3">
                                    <span class="showTooltip" title="__MILLESIME__">
                                        <i class="icon-question-sign"></i>
                                        [IF [!Pro::Vintage!]][!Pro::Vintage!][ELSE]__N/P__[/IF]
                                    </span>
                                </div>
                                <div class="span3">
                                    <span class="showTooltip" title="__UNIT_PRICE__">
                                        <i class="icon-shopping-cart"></i>
                                        [IF [!Pro::UnitPrice!]=||[!Pro::Currency!]=]__N/P__[ELSE][!Pro::UnitPrice!] [!Pro::Currency!][/IF]
                                    </span>
                                </div>
                                <div class="span2">
                                    <span class="showTooltip" title="__VOLUME__">
                                        <i class="icon-tint"></i>
                                        [IF [!Pro::Volume!]][!Pro::Volume!] L[ELSE]__N/P__[/IF]
                                    </span>
                                </div>
                                <div class="span4">
                                    <span class="showTooltip" title="__UNIT__">
                                        <i class="icon-download-alt"></i>
                                        [IF [!Pro::Unit!]][!Pro::Unit!][ELSE]__N/P__[/IF]
                                    </span>
                                </div>
                            </div>
                        [/IF]
                        <div class="row-fluid">
                            <div class="span3">
                                [IF [!Pro::StatusId!]=3||[!Pro::StatusId!]=6||[!Pro::StatusId!]=7]
                                    // 3: Answered | 6: Rejected | 7: Confirmed
                                    <span class="btn btn-info">
                                        __TOTAL__ :
                                        [!Total:=[!Pro::UnitPrice!]!]
                                        [!Total*=[!Pro::Volume!]!]
                                        [IF [!Pro::UnitPrice!]=||[!Pro::Currency!]=]__N/P__[ELSE][!Total!] [!Pro::Currency!][/IF]
                                    </span>
                                [/IF]
                            </div>
                            [SWITCH [!Pro::StatusId!]|=]
                                [CASE 3]
                                    // 3: Answered
                                    <div class="span3">
                                        <a class="btn btn-inverse" href="/Murphy/Proposal/[!Pro::Id!]/Revise">__REVISE__</a>
                                    </div>
                                    <div class="span2">
                                        <a class="btn btn-success" href="/Murphy/Proposal/[!Pro::Id!]/Confirm">__ACCEPT__</a>
                                    </div>
                                    <div class="span3">
                                        <a class="btn btn-danger" href="/Murphy/Proposal/[!Pro::Id!]/Reject">__REFUSER__</a>
                                    </div>
                                [/CASE]
                                [DEFAULT]
                                    <div class="span9">
                                        <div class="pull-right warning" style="padding-top:5px;color:red">__PROPOSAL__ [!Pro::Status!]</div>
                                    </div>
                                [/DEFAULT]
                            [/SWITCH]
                        </div>
                        [IF [!Pro::StatusId!]=7]
                            [STORPROC Murphy/Enquiry/[!Enq::Id!]/Contract|Contract]
                                [LIMIT 0|1]
                                    <div class="Contract">
                                        [MODULE Murphy/Contract/Summary?Co=[!Contract!]]
                                    </div>
                                [/LIMIT]
                            [/STORPROC]
                        [/IF]
                    </div>
                    [NORESULT]
                        __NO_PROPOSAL__
                    [/NORESULT]
                [/STORPROC]
            </div>
        </div>
    [/STORPROC]    
    [NORESULT]
        <p>__ERR_NO_THIRD__</p>
    [/NORESULT]
[/STORPROC]