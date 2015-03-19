            <legend>__PROPOSAL__ [!Prop::Reference!]</legend>
            
		<!- PANEL CONTACT -->
      	<div class="well">
	        <div class="row-fluid">
	            <div class="span5"> 
		            <div class="control-group last [IF [!E_ContactId!]]error[/IF]">
		                <label class="control-label" for="ContactId">__BUYER_CONTACT__</label>
		                <div class="controls">             
		                    <select id="ContactId" name="ContactId" class="input-medium">
		                        <option value="">__PLEASE_SELECT__</option>
		                        [STORPROC Murphy/Third/[!Th::Id!]/Contact|Item|0|100000]
		                            <option value="[!Item::Id!]" [IF [!Item::CAContact!]&&[!ContactId!]=]selected="selected"[/IF][IF [!Item::Id!]=[!ContactId!]]selected="selected"[/IF]>[!Item::Surname!] [!Item::FirstName!]</option>
		                        [/STORPROC]
		                    </select>   
		                </div>
		            </div>
	            </div>
	            <div class="span5"> 
		            <div class="control-group last [IF [!E_SupplierRef!]]error[/IF]">
		                <label class="control-label" for="SupplierRef">__SUPPLIER_REF__</label>
		                <div class="controls">
		                         <input id="BuyerRef" name="SupplierRef" type="text" value="[IF [!SupplierRef!]][!SupplierRef!][ELSE][!Prop::SupplierRef!][/IF]" class="input-medium">
		                </div>
		            </div>
	            </div>
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
		                            [STORPROC Murphy/Varietal|Item|0|100000]
		                                <option value="[!Item::Id!]" [IF [!Item::Id!]=[!Varietal!]||[!Item::Id!]=[!Prop::Varietal!]]selected="selected"[/IF]>[!Item::Varietal!]</option>
		                            [/STORPROC]
		                        </select>                    
		                    </div>
		                </div>                 
		            </div>
		            <div class="span5">
		                <div class="control-group [IF [!E_Appelation!]]error[/IF]">
		                    <label class="control-label" for="Appellation">__APPELLATION__</label>
		                    <div class="controls">                
		                        <select id="Appellation" name="Appellation" class="input-large">
		                            <option value="">__PLEASE_SELECT__</option>
		                            [STORPROC Murphy/Appellation|Item|0|100000]
		                                <option value="[!Item::Id!]" [IF [!Item::Id!]=[!Appellation!]||[!Item::Id!]=[!Appellation!]]selected="selected"[/IF]>[!Item::Appellation!]</option>
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
		                        <input type="text" id="Vintage" name="Vintage" value="[IF [!Vintage!]][!Vintage!][ELSE][!Prop::Vintage!][/IF]" class="input-medium">
		                    </div>
		                </div>
		            </div>  
		            <div class="span5">
		                <div class="control-group last [IF [!E_Filtration!]]error[/IF]">
		                    <label class="control-label" for="Filtration">__FILTRATION__</label>
		                    <div class="controls">                
		                        <select id="Filtration" name="Filtration" class="input-large">
		                            <option value="">__PLEASE_SELECT__</option>
		                            [STORPROC Murphy/Filtration|Item|0|100000]
		                                <option value="[!Item::Id!]" [IF [!Item::Id!]=[!Filtration!]||[!Item::Id!]=[!Filtration!]]selected="selected"[/IF]>[!Item::Filtration!]</option>
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
	                            <input id="Volume" name="Volume" type="text" value="[IF [!Volume!]][!Volume!][ELSE][!Prop::Volume!][/IF]" class="input-medium">
	                            <span class="add-on">L</span>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="span5">  
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
	            <div class="span5">  
	                <div class="control-group last [IF [!E_Unit!]]error[/IF]">
                        <label class="control-label" for="Unit">__UNIT__</label>
                        <div class="controls">                     
                            <select id="Unit" name="Unit">
                                <option value="">__PLEASE_SELECT__</option>
                                [STORPROC Murphy/Unit|Item|0|100000]
                                    <option value="[!Item::Id!]" [IF [!Item::Id!]=[!Prop::Unit!]] selected="selected"[/IF]>[!Item::Unit!]</option>
                                [/STORPROC]
                            </select>
                        </div>
	                </div>
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
	            <div class="span5">  
	                <div class="control-group last">
                        <label class="control-label" for="Total">__TOTAL__</label>
                        <div class="controls">
                            <input id="Total" name="Total" type="text" readonly="readonly" />
                        </div>
	                </div>
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
	                        <div class="input-prepend input-append">
	                            <input id="ShippingDate" name="ShippingDate" type="text" placeholder="jj/mm/aaaa" value="[IF [!ShippingDate!]!=][!ShippingDate!][ELSE][!Prop::ShippingDate!][/IF]" class="input-medium">
	                            <span class="add-on"><img src="/Skins/[!Systeme::Skin!]/img/icone_calendrier.gif" width="20px"></span>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="span5">  
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
	            <div class="span5">  
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
	    
	    <!-- FOURTH PANEL -->
	    <div class="well">
	        <div class="row-fluid">
	            <div class="span10"> 
	                <div class="control-group [IF [!E_Comments!]]error[/IF]">
	                    <label class="control-label" for="Comments">__COMMENTAIRES__</label>
	                    <div class="controls">
	                        <textarea class="span12" name="Comments" id="Comments" cols="80" rows="8">[IF [!Comments!]][!Comments!][ELSE][!Prop::Comments!][/IF]</textarea>
	                    </div>
	                </div>        
	            </div>
	        </div>
	    </div>
	    
	    <!-- CONFIRM PANEL -->
		<div class="well darker">
	        <div class="row-fluid">
            <input type="hidden" name="submitForm" value="1">
	            <div class="span5 offset1"> 
				  <button type="submit" class="btn btn-block btn-success">__SUBMIT__</button>
				</div>
	            <div class="span5 "> 
				  <a href="/[!Systeme::CurrentMenu::Url!]" class="btn btn-block btn-danger">__CANCEL__</a>
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
