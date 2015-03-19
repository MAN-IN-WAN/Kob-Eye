<h3>__NOUVELLE_DEMANDE__</h3>

[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1][/STORPROC]
		[OBJ Murphy|Enquiry|Dem]

[IF [!submitForm!]]
	[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1]
		[!Fields:=[!Utils::Explode(|,Varietal|Appellation|Vintage|Quantity|Filtration|Volume|PublicNotes|ContactId|Payment|Transportation|Inco|BuyerRef|Colour|CountryWine|ShippingDate|OfferDeadline)!]!]
		[STORPROC [!Fields!]|Field]
			[METHOD Dem|Set]
				[PARAM][!Field!][/PARAM]
				[PARAM][![!Field!]!][/PARAM]
			[/METHOD]
		[/STORPROC]
		[METHOD Dem|AddParent]
			[PARAM]Murphy/Third.EnquiryBuyerId/[!Th::Id!][/PARAM]
		[/METHOD]
		[IF [!Dem::Verify()!]]
			[METHOD Dem|Save][/METHOD]
			[REDIRECT][!Systeme::getMenu(Murphy/Enquiry)!][/REDIRECT]
		[ELSE]
		<div class="alert alert-error">
			<ul>
				[STORPROC [!Dem::Error!]|E]
				<li>
					[!E::Message!]
				</li>
				[!E_[!E::Prop!]:=1!]
		
				[/STORPROC]
			</ul>
		</div>
		[/IF]
	
		[NORESULT]
		<p>
			__ERR_NO_THIRD__
		</p>
		[/NORESULT]
	[/STORPROC]

[/IF]
<form id="FormNouvelleDemande" action="/[!Lien!]?action=new" method="post" class="form-horizontal">

	<!- PANEL CONTACT -->
	<div class="well">
		<div class="row-fluid">
			<div class="span5">
				<div class="control-group last [IF [!E_ContactId!]]error[/IF]">
					<label class="control-label" for="ContactId">__BUYER_CONTACT__</label>
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
			<div class="span5">
				<div class="control-group last [IF [!E_BuyerRef!]]error[/IF]">
					<label class="control-label" for="BuyerRef">__BUYER_REFERENCE__</label>
					<div class="controls">
						<input id="BuyerRef" name="BuyerRef" type="text" value="[!BuyerRef!]" class="input-medium">
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- FIRST PANEL -->
	<div class="well">
		<div class="row-fluid">
			<div class="span5">
				<div class="control-group last [IF [!E_CountryWine!]]error[/IF]">
					<label class="control-label" for="CountryWine">__PAYS__</label>
					<div class="controls">
						<select id="Country" name="CountryWine" class="input-large">
							<option value="">__PLEASE_SELECT__</option>
							[STORPROC Murphy/Country/WineProducer=1|Item|0|100000]
							<option value="[!Item::Id!]" [IF [!Item::Id!]=[!CountryWine!]]selected="selected"[/IF]>[!Item::Country!]</option>
							[/STORPROC]
							<option value="" >__OTHER_COUNTRY__</option>
						</select>
					</div>
				</div>
			</div>
			<div class="span5">
				<div class="control-group [IF [!E_Varietal!]]error[/IF]">
					<label class="control-label" for="Varietal">__VARIETEE__</label>
					<div class="controls">
						<select id="Varietal" name="Varietal" class="input-large">
							<option value="">__PLEASE_SELECT__</option>
							[STORPROC Murphy/Varietal/Inactive=0|Item|0|100000]
							<option supp="[!Item::Colours!]" value="[!Item::Id!]" [IF [!Item::Id!]=[!Varietal!]]selected="selected"[/IF]>[!Item::Varietal!]</option>
							[/STORPROC]
						</select>
					</div>
				</div>
			</div>
			<script type="text/javascript">
				$(function() {
				        $("#Varietal").change(function(){
				            var element = $(this);
				            var option = $('option:selected', element).attr('supp');
				            var cl = option.split(',');
				            //reset
				            var couleur = $('#Colour');
				            $(couleur).prop('selectedIndex',0);
				            $('option',couleur).attr('disabled','disabled'); 
				            //activation de certaines options sur le champ couleur
				            $(cl).each(function (index,item){
					            $('option[value='+item+']',couleur).removeAttr('disabled'); 
				            })
				        });
				        $("#Country").change(function(){
				            var element = $(this);
				            var option = $('option:selected', element).attr('value');
				            //reset
				            var appellation = $('#Appellation');
				            $(appellation).prop('selectedIndex',0);
				            $('option',appellation).attr('disabled','disabled'); 
//				            $('option',appellation).css('display','none'); 
				            //activation de certaines options sur le champ appellation
				            $('option[supp='+option+']',appellation).removeAttr('disabled'); 
				            $('option[supp='+option+']',appellation).css('display','block'); 
				            $('option:first',appellation).css('display','block'); 
				        });
			    });
			</script>
		</div>
		<div class="row-fluid">
			<div class="span5">
				<div class="control-group last [IF [!E_Colour!]]error[/IF]">
					<label class="control-label" for="Colour">__COLOR__</label>
					<div class="controls">
						<select id="Colour" name="Colour" class="input-large">
							<option value="">__PLEASE_SELECT__</option>
							[STORPROC Murphy/Colour|Item|0|100000]
							<option value="[!Item::Id!]" [IF [!Item::Id!]=[!Colour!]]selected="selected"[/IF]>[!Item::Colour!]</option>
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
							<option value="[!Item::Id!]" [IF [!Item::Id!]=[!Appellation!]]selected="selected"[/IF] supp="[!Item::Country!]" >[!Item::Appellation!]</option>
							[/STORPROC]
						</select>
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
							<input type="text" id="Vintage" name="Vintage" value="[!Vintage!]" class="input-medium">
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
								<option value="[!Item::Id!]" [IF [!Item::Id!]=[!Filtration!]]selected="selected"[/IF]>[!Item::Filtration!]</option>
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
							<input id="Volume" name="Volume" type="text" value="[!Volume!]" class="input-medium">
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
							<option value="[!Item::Id!]" [IF [!Item::Default!]&&[!Quantity!]=]selected="selected"[/IF][IF [!Item::Id!]=[!Quantity!]]selected="selected"[/IF]>[!Item::Quantity!]</option>
							[/STORPROC]
						</select>
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
						<div class="input-prepend input-append datetimepicker">
							<input id="ShippingDate" name="ShippingDate" type="text" placeholder="jj/mm/aaaa" data-format="dd/MM/yyyy" value="[!ShippingDate!]" class="input-medium-icon">
							<span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
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
							<option value="[!Item::Id!]" [IF [!Item::Default!]&&[!Transportation!]=]selected="selected"[/IF][IF [!Item::Id!]=[!Transportation!]]selected="selected"[/IF]>[!Item::Transportation!]</option>
							[/STORPROC]
						</select>
					</div>
				</div>
			</div>
		</div>
		<!-- FOURTH PANEL -->
		<div class="row-fluid">
			<!--<div class="span5">
			<div class="control-group last [IF [!E_Inco!]]error[/IF]">
			<label class="control-label" for="Inco">__INCO_TERMS__</label>
			<div class="controls">
			<select id="Inco" name="Inco" class="input-large">
			<option value="">__PLEASE_SELECT__</option>
			[STORPROC Murphy/IncoTerm|Item|0|100000]
			<option value="[!Item::Id!]" [IF [!Item::Default!]&&[!Inco!]=]selected="selected"[/IF][IF [!Item::Id!]=[!Inco!]]selected="selected"[/IF]>[!Item::Inco!]</option>
			[/STORPROC]
			</select>
			</div>
			</div>
			</div>-->
			<div class="span5">
				<div class="control-group [IF [!E_ShippingDate!]]error[/IF]">
					<label class="control-label" for="OfferDeadline">__OFFER_DEADLINE__</label>
					<div class="controls">
						<div class="input-prepend input-append datetimepicker">
							<input id="OfferDeadline" name="OfferDeadline" type="text" placeholder="jj/mm/aaaa" data-format="dd/MM/yyyy" value="[IF [!OfferDeadline!]][!OfferDeadline!][ELSE][DATE d/m/Y][!TMS::Now:+2592000!][/DATE][/IF]" class="input-medium-icon">
							<span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
						</div>
					</div>
				</div>
			</div>
			<div class="span5">
				<div class="control-group last [IF [!E_Payment!]]error[/IF]">
					<label class="control-label" for="Payment">__PAYMENT__</label>
					<div class="controls">
						<select id="Payment" name="Payment" class="input-large">
							<option value="">__PLEASE_SELECT__</option>
							//	                            [STORPROC Murphy/PaymentTerm|Item|0|100000]
							// <option value="[!Item::Id!]" [IF [!Item::Default!]&&[!Payment!]=]selected="selected"[/IF][IF [!Item::Id!]=[!Payment!]]selected="selected"[/IF]>[!Item::Payment!]</option>
							//	                            [/STORPROC]
							<option value="7" >__30_DAYS__</option>
							<option value="2" >__60_DAYS__</option>
							<option value="">__PLEASE_SPECIFY_IN_COMMENT__</option>
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
				<div class="control-group [IF [!E_PublicNotes!]]error[/IF]">
					<label class="control-label" for="PublicNotes">__COMMENTAIRES__</label>
					<div class="controls">
						<textarea id="PublicNotes" name="PublicNotes" cols="40" rows="5" class="span12">[!PublicNotes!]</textarea>
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
				<button type="submit" class="btn btn-block btn-murphy">
					__SUBMIT__
				</button>
			</div>
			<div class="span5 ">
				<a href="/[!Systeme::CurrentMenu::Url!]" class="btn btn-block btn-warning">__CANCEL__</a>
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">
	$(function() {
		$('.datetimepicker').datetimepicker({
			pickTime : false
		});
	}); 
</script>