//Tiers en cours
[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1][/STORPROC]
//Enquiry
[STORPROC Murphy/Enquiry/[!Id!]|Enq][/STORPROC]
//Buyer
[STORPROC Murphy/Third.EnquiryBuyerId/Enquiry/[!Enq::Id!]|B|0|1][/STORPROC]

<div class="well form-horizontal">
	
    <div class="row-fluid">
        <div class="span5 EnqDate">
            <span class="label">Ref: [!Enq::Reference!]</span>
        	[STORPROC Murphy/Status/[!Enq::Status!]|Item]
        	<span class="label" style="background-color:[!Item::Color!]" class="pull-right">[!Item::Status!]</span>
        	[/STORPROC]
        </div>
	<div class="span6 offset1">
	    [IF [!CONTROL!]]
		<a href="/[!Systeme::getMenu(Murphy/Enquiry)!]/[!Enq::Id!]" class="btn btn-murphy pull-right">__DETAILS__</a>
		[COUNT Murphy/Enquiry/[!Enq::Id!]/Proposal/StatusId=[!CONF::MODULE::MURPHY::STP_ANSWERED!]+StatusId=[!CONF::MODULE::MURPHY::STP_VALIDATED!]&Third.ProposalBuyerId([!Th::Id!])|NbArch]
		[IF [!NbArch!]]
			//EN ATTENTE
			<a href="/[!Systeme::getMenu(Murphy/Enquiry)!]/[!Enq::Id!]" class="btn btn-danger pull-right" style="margin-right:10px;">__WAITING_PROPOSAL__<span class="badge pull-right" style="margin:1px 0px 0px 10px;background-color: white;color:black">[!NbArch!]</span></a></li>
		[/IF]
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
	    
		[IF [!Prop::ShowBuyer!]]
			<div class="span6">
				<div class="control-group" style="margin-bottom:0">
				<label class="control-label" style="padding-top:0">__NOM_ACHETEUR__</label>
				<div class="controls">
					<strong>[!B::Company!]</strong>
				</div>
				</div>
			</div>
		[/IF]
		[IF [!Th::Id!]!=[!B::Id!]]
			<div class="span6">
				<div class="control-group" style="margin-bottom:0">
				<label class="control-label" style="padding-top:0">__BUYER_COUNTRY__</label>
					<div class="controls">
						<strong>
							[IF [!B::Country!]>0]
								[STORPROC Murphy/Country/[!B::Country!]|Item]
									[!Item::Country!]
								[/STORPROC]
							[ELSE]__N/P__[/IF]
						</strong>
					</div>
				</div>
			</div>
		[/IF]
	</div>
	<div class="row-fluid">
	    <div class="span6">
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__PAYS__</label>
	            <div class="controls">
	                <strong>
	                [IF [!Enq::CountryWine!]>0]
	                	[STORPROC Murphy/Country/[!Enq::CountryWine!]|Item]
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
	                [IF [!Enq::Varietal!]!=]
	                	[STORPROC Murphy/Varietal/[!Enq::Varietal!]|Item|0|1]
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
	                [IF [!Enq::Colour!]>0]
	                	[STORPROC Murphy/Colour/[!Enq::Colour!]|Item]
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
	                [IF [!Enq::Appellation!]>0]
	                	[STORPROC Murphy/Appellation/[!Enq::Appellation!]|Item]
	                		[!Item::Appellation!]
	                	[/STORPROC]
	                [ELSE]__N/P__[/IF]
	               </strong>
	            </div>
	        </div>
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__MILLESIME__</label>
	            <div class="controls">
	                <strong>[IF [!Enq::Vintage!]!=][!Enq::Vintage!][ELSE]__N/P__[/IF]</strong>
	            </div>
	        </div>
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__FILTRATION__</label>
	            <div class="controls">
	                <strong>
	                [IF [!Enq::Filtration!]>0]
	                	[STORPROC Murphy/Filtration/[!Enq::Filtration!]|Item]
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
	                <strong>[IF [!Enq::Volume!]>0][UTIL NUMBER][!Enq::Volume!][/UTIL]
	                	[STORPROC Murphy/Quantity/[!Enq::Quantity!]|Item]
	                		[!Item::Quantity!]
	                	[/STORPROC]

			[ELSE]__N/P__[/IF]</strong>
	            </div>
	        </div>
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__TRANSPORTATION__</label>
	            <div class="controls">
	                <strong>
	                [IF [!Enq::Transportation!]>0]
	                	[STORPROC Murphy/Transportation/[!Enq::Transportation!]|Item]
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
	                [IF [!Enq::Inco!]>0]
	                	[STORPROC Murphy/IncoTerm/[!Enq::Inco!]|Item]
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
	                [IF [!Enq::Payment!]>0]
	                	[STORPROC Murphy/PaymentTerm/[!Enq::Payment!]|Item]
	                		[!Item::Payment!]
	                	[/STORPROC]
	                [ELSE]__N/P__[/IF]
	                </strong>
	            </div>
	        </div>
	        [IF [!B::Id!]!=[!Th::Id!]&&[!Enq::BuyerBilled!]!=1]
	        //On n'affiche la commission pour le vendeur
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__COMMISSION__</label>
	            <div class="controls">
	                <strong>
	                	[!Enq::TotalCom!] %
	                </strong>
	            </div>
	     </div>
	     [/IF]
	        [IF [!B::Id!]=[!Th::Id!]&&[!Enq::BuyerBilled!]]
	        //On n'affiche la commission pour l'acheteur
-	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__COMMISSION__</label>
	            <div class="controls">
	                <strong>
	                	[!Enq::TotalCom!] %
	                </strong>
	            </div>
	     </div>
	     [/IF]
	        <div class="control-group" style="margin-bottom:0">
	            <label class="control-label" style="padding-top:0">__DATE_LIMITE__</label>
	            <div class="controls">
	                <strong>[IF [!Enq::Date!]!=][!Utils::getDate(d/m/Y,[!Enq::ShippingDate!])!][ELSE]__N/P__[/IF]</strong>
	            </div>
	        </div>
	    </div>
	</div>
	<div class="row-fluid">
	    <div class="span10">
            <h5 style="margin-bottom:0;"><em>__COMMENTAIRES__</em></h5>
            <em>
            	[!Enq::PublicNotes!]
            </em>
	    </div>
	</div>
</div>
