[STORPROC Murphy/Enquiry/Id=[!Id!]|Enq][/STORPROC]
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
	            <span class="showTooltip" title="__VARIETEE__">
	            <i class="icon-glass"></i>
	               <strong>
	                [IF [!Enq::Varietal!]>0]
		                [STORPROC Murphy/Varietal/[!Enq::Varietal!]|Item]
		                    [!Item::Varietal!]
		                [/STORPROC]
		            [ELSE]
		            	__N/P__
		            [/IF]
	                [IF [!Enq::Appellation!]>0]
		                [STORPROC Murphy/Appellation/[!Enq::Appellation!]|Item]
		                    , [!Item::Appellation!]
		                [/STORPROC]
		            [/IF]
	               
	               [IF [!Enq::Millesime!]], [!Enq::Millesime!][/IF]
	               </strong>
	            </span>
	        </div>
	        <div class="span4">
	            <span class="showTooltip" title="__FILTRATION__">
	                <i class="icon-filter"></i>
	                 __FILTRATION__: <strong>
	                [IF [!Enq::Filtration!]>0]
		                [STORPROC Murphy/Filtration/[!Enq::Filtration!]|Item]
		                    [!Item::Filtration!]
		                [/STORPROC]
		            [ELSE]
		            	__N/P__
		            [/IF]
	                </strong>
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
	                [IF [!Enq::Volume!]][!Enq::Volume!][ELSE]__N/P__[/IF]&nbsp;
	                [IF [!Enq::Quantity!]>0]
		                [STORPROC Murphy/Quantity/[!Enq::Quantity!]|Item]
		                    [!Item::Quantity!]
		                [/STORPROC]
		            [/IF]
	            </span>
	        </div>
            <div class="span2 offset7">
                [IF [!Enq::Status!]>0]
	                [STORPROC Murphy/Status/[!Enq::Status!]|Item]
		            	<span class="label" style="background-color:[!Item::Color!]">[!Item::Status!]</span>
	                [/STORPROC]
	            [ELSE]
	            	__N/P__
	            [/IF]
            </div>
	    </div>
	</div>
	<div class="well">
	    <div class="row-fluid">
	        <div class="span12">
	            <span class="showTooltip" title="__COMMENTAIRES__">
	                <i class="icon-file"></i><strong> __COMMENTAIRES__</strong><br />
	                [!Utils::nl2br([!Enq::PublicNotes!])!]
	            </span>
	        </div>
	    </div>
	</div>
