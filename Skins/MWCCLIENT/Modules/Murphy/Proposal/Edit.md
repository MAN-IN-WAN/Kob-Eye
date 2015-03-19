[STORPROC [!Query!]|Pr][/STORPROC]
[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1]
    [NORESULT]
        <p>__ERR_NO_THIRD__</p>
    [/NORESULT]
[/STORPROC]

[STORPROC Murphy/Third/[!Th::Id!]/Proposal.ProposalSupplierId/[!Pr::Id!]|Prop]
	    [STORPROC Murphy/Enquiry/Proposal/[!Prop::Id!]|Enq][/STORPROC]
	    [STORPROC Murphy/Enquiry/Id=[!Enq::Id!]|Enq][/STORPROC]
	    [IF [!submitForm!]]
		    [!Fields:=[!Utils::Explode(|,SupplierRef|Varietal|Appellation|Quantity|Filtration|Volume|Vintage|Volume|UnitPrice|Currency|Comments|ValidUntil|Inco|Payment)!]!]
		    [STORPROC [!Fields!]|Field]
			[METHOD Prop|Set]
			    [PARAM][!Field!][/PARAM]
			    [PARAM][![!Field!]!][/PARAM]
			[/METHOD]
		    [/STORPROC]        
			[IF [!Prop::Verify!]&&[!Prop::UnitPrice!]>0]
			    [METHOD Prop|AnswerProposal][/METHOD]
			    [IF [!another!]]
				//duplication
				[!Prop2:=[!Prop::getClone()!]!]
			        [REDIRECT][!Systeme::CurrentMenu::Url!]/[!Prop2::Id!][/REDIRECT]
			    [ELSE]
				    [REDIRECT][!Lien!][/REDIRECT]
			    [/IF]
			[ELSE]
				    <div class="alert alert-error">
					<ul>
				[IF [!Prop::UnitPrice!]=0]
						<li>__PRICE_NOT_FILLED__</li>
						[!E_UnitPrice:=1!]
				[/IF]
				[STORPROC [!Prop::Error!]|E]
						<li>[!E::Message!]</li>
						[!E_[!E::Prop!]:=1!]
						
				[/STORPROC]
					</ul>
				    </div>
			[/IF]
	    
	    [/IF]
		    
		        <form action="/[!Lien!]" method="post" class="form-horizontal" enctype="multipart/form-data">
		                    
		            //// DONNEES DE LA DEMANDE
		<div class="alert alert-info">
			<strong>__THE_ENQUIRY__</strong>
		</div>
		            [MODULE Murphy/Enquiry/EtatDetail?Id=[!Enq::Id!]]
		    

		<div class="alert alert-info">
			<strong>__MY_PROPOSAL__</strong>
		</div>
		//AUTRES OFFRES
		[STORPROC Murphy/Third/[!Th::Id!]/Proposal.ProposalSupplierId/Id!=[!Prop::Id!]&Enquiry.ProposalEnquiryId([!Enq::Id!])|Pro|0|100|Id|ASC]
		    <div class="well" style="margin-left:50px;">
			    [MODULE Murphy/Proposal/EtatDetail?Id=[!Pro::Id!]&&NODETAIL=1]
		    </div>
		[/STORPROC]

		            //// DONNEES MODIFIABLES
		[IF [!Enq::StatusId!]<=[!CONF::MODULE::MURPHY::STE_CONFIRMED!]]

			[IF [!Prop::Status!]<[!CONF::MODULE::MURPHY::STP_ANSWERED!]&&[!Prop::Status!]<[!CONF::MODULE::MURPHY::STP_REFUSED!]]
				<!- PANEL CONTACT -->
		     <div class="row-fluid" style="margin-top: 20px;">
		        <div class="span5 EnqDate">
		            <span class="label">Ref: [!Prop::Reference!]</span>
		        	[STORPROC Murphy/Status/[!Prop::Status!]|Item]
		//        	<span class="label" style="background-color:[!Item::Color!]" class="pull-right">[!Item::Status!]</span>
		        	[/STORPROC]
		        </div>
		        <div class="span6 offset1">
		        </div>
		    </div>
		
		    	<!-- FIRST PANEL -->
		      	<div class="well">
			        <div class="row-fluid">
			            <div class="span12">
				            <div class="span5">
				                <div class="control-group [IF [!E_Varietal!]]error[/IF]">
				                    <label class="control-label" for="Varietal">__VARIETEE__</label>
				                    <div class="controls">                
				                        <select id="Varietal" name="Varietal" class="input-large">
				                            <option value="">__PLEASE_SELECT__</option>
				                            [STORPROC Murphy/Varietal/Inactive=0|Item|0|100000]
				                                <option value="[!Item::Id!]" [IF [!Item::Id!]=[!Varietal!]||[!Item::Id!]=[!Prop::Varietal!]]selected="selected"[/IF]>[!Item::Varietal!]</option>
				                            [/STORPROC]
				                        </select>                    
				                    </div>
				                </div>                 
				            </div>
				            <div class="span5 offset1">
				                <div class="control-group [IF [!E_Appellation!]]error[/IF]">
				                    <label class="control-label" for="Appellation">__APPELLATION__</label>
				                    <div class="controls">                
				                        <select id="Appellation" name="Appellation" class="input-large">
				                            <option value="">__PLEASE_SELECT__</option>
				                            [STORPROC Murphy/Appellation|Item|0|100000]
				                                <option value="[!Item::Id!]" [IF [!Item::Id!]=[!Appellation!]||[!Item::Id!]=[!Prop::Appellation!]]selected="selected"[/IF]>[!Item::Appellation!]</option>
				                            [/STORPROC]
				                        </select>                    
				                    </div>
				                </div>
				            </div>
			            </div>
			        </div>
			       <div class="row-fluid">
			            <div class="span12">
				            <div class="span5">
				                <div class="control-group last [IF [!E_Vintage!]]error[/IF]">
				                    <label class="control-label" for="Vintage">__MILLESIME__</label>
				                    <div class="controls">
				                        <input type="text" id="Vintage" name="Vintage" value="[IF [!Vintage!]][!Vintage!][ELSE][!Prop::Vintage!][/IF]" class="input-large">
				                    </div>
				                </div>
				            </div>  
				            <div class="span5 offset1">
				                <div class="control-group last [IF [!E_Filtration!]]error[/IF]">
				                    <label class="control-label" for="Filtration">__FILTRATION__</label>
				                    <div class="controls">                
				                        <select id="Filtration" name="Filtration" class="input-large">
				                            <option value="">__PLEASE_SELECT__</option>
				                            [STORPROC Murphy/Filtration|Item|0|100000]
				                                <option value="[!Item::Id!]" [IF [!Item::Id!]=[!Filtration!]||[!Item::Id!]=[!Prop::Filtration!]]selected="selected"[/IF]>[!Item::Filtration!]</option>
				                            [/STORPROC]
				                        </select>                    
				                    </div>
				                </div>
				            </div>
				        </div>
		            </div>
		        </div>
		        
		        
		    	<!-- SECOND PANEL -->
		      	<div class="well">
			        <div class="row-fluid">
			            <div class="span5">
			                <div class="control-group last [IF [!E_Volume!]]error[/IF]">
			                    <label class="control-label" for="Volume">__VOLUME__</label>
			                    <div class="controls">
			                        <div class="input-prepend input-append">
			                            <input id="Volume" name="Volume" type="text" value="[IF [!Volume!]][!Volume!][ELSE][!Prop::Volume!][/IF]" class="input-large">
			                        </div>
			                    </div>
			                </div>
			            </div>
			            <div class="span5 offset1">  
			                <div class="control-group last [IF [!E_Quantity!]]error[/IF]">
			                    <label class="control-label" for="Quantity">__QUANTITY__</label>
			                    <div class="controls">                
			                        <select id="Quantity" name="Quantity" class="input-large">
			                            <option value="">__PLEASE_SELECT__</option>
			                            [STORPROC Murphy/Quantity|Item|0|100000]
			                                <option value="[!Item::Id!]" [IF [!Item::Default!]&&[!Quantity!]=]selected="selected"[/IF][IF [!Item::Id!]=[!Quantity!]||[!Item::Id!]=[!Prop::Quantity!]]selected="selected"[/IF]>[!Item::Quantity!]</option>
			                            [/STORPROC]
			                        </select>                    
			                    </div>
			                </div>
			            </div>
			        </div>  
			        <div class="row-fluid">
			            <div class="span5">
			                <div class="control-group last [IF [!E_UnitPrice!]]error[/IF]">
		                        <label class="control-label" for="UnitPrice">__UNIT_PRICE__</label>
		                        <div class="controls">
		                            <input id="UnitPrice" name="UnitPrice" type="text" value="[!Prop::UnitPrice!]" onkeyup="updateTotal()" />
		                        </div>
			                </div>
			            </div>
			            <div class="span5 offset1">  
		// 	                <div class="control-group last [IF [!E_Unit!]]error[/IF]">
		//                         <label class="control-label" for="Unit">__UNIT__</label>
		//                         <div class="controls">                     
		//                             <select id="Unit" name="Unit">
		//                                 <option value="">__PLEASE_SELECT__</option>
		//                                 [STORPROC Murphy/Unit|Item|0|100000]
		//                                     <option value="[!Item::Id!]" [IF [!Item::Id!]=[!Prop::Unit!]] selected="selected"[/IF]>[!Item::Unit!]</option>
		//                                 [/STORPROC]
		//                             </select>
		//                         </div>
		// 	                </div>
			            </div>
			        </div>  
			        <div class="row-fluid">
			            <div class="span5">
			                <div class="control-group last [IF [!E_Currency!]]error[/IF]">
		                        <label class="control-label" for="Currency">__CURRENCY__</label>
		                        <div class="controls">                     
		                            <select id="Currency" name="Currency">
		                                <option value="">__PLEASE_SELECT__</option>
		                                [STORPROC Murphy/Currency|Item|0|100000]
		                                    <option value="[!Item::Id!]" [IF [!Item::Id!]=[!Prop::Currency!]] selected="selected"[/IF]>[!Item::Currency!]</option>
		                                [/STORPROC]
		                            </select>
		                        </div>
			                </div>
			            </div>
			            <div class="span5 offset1">  
		// 	                <div class="control-group last">
		//                         <label class="control-label" for="Total">__TOTAL__</label>
		//                         <div class="controls">
		//                             <input id="Total" name="Total" type="text" readonly="readonly" />
		//                         </div>
		// 	                </div>
			            </div>
			        </div>  
			    </div>
			    
			    <!-- THIRD PANEL -->
		      	<div class="well">
			        <div class="row-fluid">
			            <div class="span5">  
			                <div class="control-group [IF [!E_ShippingDate!]]error[/IF]">
			                    <label class="control-label" for="ShippingDate">__DATE_LIMITE__</label>
			                    <div class="controls">
			                        <div class="input-prepend input-append datetimepicker">
			                            <input id="ShippingDate" name="ShippingDate" type="text" placeholder="jj/mm/aaaa" data-format="dd/MM/yyyy" value="[IF [!ShippingDate!]!=][!ShippingDate!][ELSE][DATE d/m/Y][!Prop::ShippingDate!][/DATE][/IF]" class="input-extension-image">
			                            <span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
			                     </div>
			                    </div>
			                </div>
			            </div>
			            <div class="span5 offset1">  
			                <div class="control-group [IF [!E_Transportation!]]error[/IF]">
			                    <label class="control-label" for="Transportation">__TRANSPORTATION__</label>
			                    <div class="controls">
			                        <select id="Transportation" name="Transportation" class="input-large">
			                            <option value="">__PLEASE_SELECT__</option>
			                            [STORPROC Murphy/Transportation|Item|0|100000]
			                                <option value="[!Item::Id!]" [IF [!Item::Default!]&&[!Transportation!]=]selected="selected"[/IF][IF [!Item::Id!]=[!Transportation!]||[!Item::Id!]=[!Prop::Transportation!]]selected="selected"[/IF]>[!Item::Transportation!]</option>
			                            [/STORPROC]
			                        </select>   
			                    </div>
			                </div>
			            </div>
			        </div>
			        <div class="row-fluid">
			            <div class="span5">  
			                <div class="control-group last [IF [!E_Inco!]]error[/IF]">
			                    <label class="control-label" for="Inco">__INCO_TERMS__</label>
			                    <div class="controls">             
			                        <select id="Inco" name="Inco" class="input-large">
			                            <option value="">__PLEASE_SELECT__</option>
			                            [STORPROC Murphy/IncoTerm|Item|0|100000]
			                                <option value="[!Item::Id!]" [IF [!Item::Default!]&&[!Inco!]=]selected="selected"[/IF][IF [!Item::Id!]=[!Inco!]||[!Item::Id!]=[!Prop::Inco!]]selected="selected"[/IF]>[!Item::Inco!]</option>
			                            [/STORPROC]
			                        </select>   
			                    </div>
			                </div>
			            </div>
			            <div class="span5 offset1">  
			                <div class="control-group last [IF [!E_Payment!]]error[/IF]">
			                    <label class="control-label" for="Payment">__PAYMENT__</label>
			                    <div class="controls">
			                        <select id="Payment" name="Payment" class="input-large">
			                            <option value="">__PLEASE_SELECT__</option>
			                            [STORPROC Murphy/PaymentTerm|Item|0|100000]
			                                <option value="[!Item::Id!]" [IF [!Item::Default!]&&[!Payment!]=]selected="selected"[/IF][IF [!Item::Id!]=[!Payment!]||[!Item::Id!]=[!Prop::Payment!]]selected="selected"[/IF]>[!Item::Payment!]</option>
			                            [/STORPROC]
			                        </select>   
			                    </div>
			                </div>
			            </div>
			        </div>
			    </div>
			    
		      	<div class="well">
			        <div class="row-fluid">
			            <div class="span5"> 
				            <div class="control-group last [IF [!E_ContactId!]]error[/IF]">
				                <label class="control-label" for="ContactId">__CA_CONTACT__</label>
				                <div class="controls">             
				                    <select id="ContactId" name="ContactId" class="input-large">
				                        <option value="">__PLEASE_SELECT__</option>
				                        [STORPROC Murphy/Third/[!Th::Id!]/Contact|Item|0|100000]
				                            <option value="[!Item::Id!]" [IF [!Item::CAContact!]&&[!ContactId!]=]selected="selected"[/IF][IF [!Item::Id!]=[!ContactId!]]selected="selected"[/IF]>[!Item::Surname!] [!Item::FirstName!]</option>
				                        [/STORPROC]
				                    </select>   
				                </div>
				            </div>
			            </div>
			            <div class="span5 offset1"> 
				            <div class="control-group last [IF [!E_SupplierRef!]]error[/IF]">
				                <label class="control-label" for="SupplierRef">__SUPPLIER_REF__</label>
				                <div class="controls">
				                         <input id="BuyerRef" name="SupplierRef" type="text" value="[IF [!SupplierRef!]][!SupplierRef!][ELSE][!Prop::SupplierRef!][/IF]" class="input-large">
				                </div>
				            </div>
			            </div>
			        </div>
			        <div class="row-fluid">
			            <div class="span5"> 
						<!-- EMPTY -->
			            </div>
			            <div class="span5 offset1"> 
				            <div class="control-group last [IF [!E_ValidUntil!]]error[/IF]">
				                <label class="control-label" for="SupplierRef">__VALID_UNTIL__</label>
				                <div class="controls">
							<div class="input-prepend input-append datetimepicker">
								<input id="ValidUntil" name="ValidUntil" type="text" placeholder="jj/mm/aaaa" data-format="dd/MM/yyyy" value="[IF [!ValidUntil!]!=][!ValidUntil!][ELSE][IF [!Prop::ValidUntil!]][DATE d/m/Y][!Prop::ValidUntil!][/DATE][ELSE][DATE d/m/Y][!TMS::Now:+2592000!][/DATE][/IF][/IF]" class="input-extension-image">
								<span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
							</div>
				                </div>
				            </div>
			            </div>
			        </div>
		        </div>
			    <!-- FOURTH PANEL -->
			    <div class="well">
			        <div class="row-fluid">
			            <div class="span10"> 
			                <div class="control-group [IF [!E_Comments!]]error[/IF]">
			                    <label class="control-label" for="Comments">__COMMENTAIRES__</label>
			                    <div class="controls">
			                        <textarea class="span12" style="height:82px;width:654px;" name="Comments" id="Comments" cols="80" rows="8">[IF [!Comments!]][!Comments!][ELSE][!Prop::Comments!][/IF]</textarea>
			                    </div>
			                </div>        
			            </div>
			        </div>
			    </div>
			    <!-- FIFTH PANEL -->
			    <div class="well">
			        <div class="row-fluid">
			            <div class="span1 offset2"> 
			                <input type="checkbox" name="another" id="another" value="1" />
				    </div>        
			            <div class="span9"> 
			                <label class="control-label" style="text-align:left">__CREATE_ANOTHER__</label>
				    </div>        
			        </div>
			    </div>
			    
			    <!-- CONFIRM PANEL -->
				<div class="well darker">
			        <div class="row-fluid">
		            <input type="hidden" name="submitForm" value="1">
			            <div class="span5 offset1"> 
						  <button type="submit" class="btn btn-block btn-murphy">__SUBMIT_AND_SEND_TO_BUYER__</button>
						</div>
			            <div class="span5 "> 
						  <a href="/[!Systeme::CurrentMenu::Url!]" class="btn btn-block btn-warning">__CANCEL__</a>
						</div>
					</div>
		        </div>
		    </form>
		        <script type="text/javascript">
		            function updateTotal() {
		                $('#Total').val($('#Volume').val()*$('#UnitPrice').val());
		            }
		            $(document).ready(function() {
		                updateTotal();
		            });
		        </script>
		    [ELSE]
			<div class="well" style="margin-left:50px;">
		    	[MODULE Murphy/Proposal/EtatDetail?Id=[!Prop::Id!]&&NODETAIL=1]
			</div>
			[/IF]
		[ELSE]
		    <div class="well" style="margin-left:50px;">
		    [MODULE Murphy/Proposal/EtatDetail?Id=[!Prop::Id!]&&NODETAIL=1]
		    </div>
		[/IF]       
    [NORESULT]
        <div class="alert alert-danger"> Murphy/Third/[!Th::Id!]/Proposal.ProposalSupplierId/[!Prop::Id!] __ERR_NO_AUTH__</div>
    [/NORESULT]
[/STORPROC]   


    <script type="text/javascript">
  $(function() {
    $('.datetimepicker').datetimepicker({
      pickTime: false
    });
  });
</script>