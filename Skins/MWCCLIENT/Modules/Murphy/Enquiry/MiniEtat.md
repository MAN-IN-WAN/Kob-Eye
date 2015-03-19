        <div class="well">
            <div class="row-fluid">
                <div class="span3 EnqDate">
                    <i class="icon-share-alt"></i>
                    Date creation: <strong>[!Utils::getDate(d/m/Y,[!Enq::tmsCreate!])!]</strong>
                </div>
                <div class="span3 offset6">
                    <span class="label label-warning btn-block">Ref: <strong>[!Enq::Reference!]</strong></span>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span5">
                    <span class="showTooltip" title="__PAYS__,__VARIETEE__,__APPELLATION__,__MILLESIME__">
                    <i class="icon-glass"></i>
                       <strong>[IF [!Enq::CountryWine!]][!Enq::CountryWine!],[/IF] [IF [!Enq::Colour!]][!Enq::Colour!],[/IF] [IF [!Enq::Varietal!]][!Enq::Varietal!][ELSE]__N/P__[/IF] [IF [!Enq::Appellation!]],[!Enq::Appellation!][/IF] [IF [!Enq::Millesime!]][!Enq::Millesime!][/IF]</strong>
                    </span>
                </div>
                <div class="span4">
                    <span class="showTooltip" title="__FILTRATION__">
                        <i class="icon-filter"></i>
                        __FILTRATION__: <strong>[IF [!Enq::Filtration!]][!Enq::Filtration!][ELSE]__N/P__[/IF]</strong>
                    </span>
                </div>
                <div class="span3 EnqDate">
                    <i class="icon-share-alt"></i>
                    Date: <strong>[!Utils::getDate(d/m/Y,[!Enq::ShippingDate!])!]</strong>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span3">
                    <span class="showTooltip" title="__VOLUME__">
                        <i class="icon-tasks"></i>
                       [IF [!Enq::Volume!]][!Enq::Volume!][ELSE]__N/P__[/IF]&nbsp;[IF [!Enq::Quantity!]][!Enq::Quantity!][/IF]
                    </span>
                </div>
                <div class="span2 offset5">
                	<span class="label" style="background-color:[!Enq::StatusColor!]">[!Enq::Status!]</span>
                </div>
                <div class="span2">
                    <a href="/[!Systeme::CurrentMenu::Url!]/[!Enq::Id!]" class="btn btn-inverse btn-block">__DETAILS__</a>
                </div>
            </div>
        </div>
