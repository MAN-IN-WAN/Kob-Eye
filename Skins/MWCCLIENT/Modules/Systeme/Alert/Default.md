[IF [!GO!]]
	[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1]
		[OBJ Systeme|AlertUser|Au]
		[METHOD Au|setAlert]
			[PARAM]Please call [!Name!] at this number [!Phone!] CALLBACK MESSAGE : [!PublicNotes!]. Please[/PARAM]
			[PARAM]TH[!Th::Id!][/PARAM]
			[PARAM][!Th::Module!][/PARAM]
			[PARAM][!Th::ObjectType!][/PARAM]
			[PARAM][!Th::Id!][/PARAM]
			[PARAM][/PARAM]
			[PARAM]MWC_BROKER[/PARAM]
			[PARAM]mwc_pic_callback[/PARAM]
		[/METHOD]
	    <div class="alert alert-success">
		Vous allez bientot être rappelé par un courtier.
		</div>

	[/STORPROC]
[ELSE]
	
	<h3>__CALLBACK_REQUEST__</h3>
	<form class="form-horizontal" method="POST">
		<div class="well">
			<div class="row-fluid" style="margin-bottom:10px;">
				<div class="span6">  
					<div class="control-group last [IF [!E_Inco!]]error[/IF]">
						<label class="control-label" for="Phone">__PHONE_NUMBER__</label>
						<div class="controls">
						<input type="text" name="Phone" value="[!Phone!]" placeholder="(+33) 4 XXXXXXXXXXX" required/>
						</div>
					</div>
				</div>
				<div class="span6">  
					<div class="control-group last [IF [!E_Inco!]]error[/IF]">
						<label class="control-label" for="Phone">__NAME__</label>
						<div class="controls">
						<input type="text" name="Name" value="[!Name!]" placeholder="Your name" required/>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12"> 
				<div class="control-group [IF [!E_PublicNotes!]]error[/IF]">
					<label class="control-label" for="PublicNotes">__COMMENTAIRES__</label>
					<div class="controls">
					<textarea id="PublicNotes" name="PublicNotes" cols="40" rows="5" class="span12" required>[!PublicNotes!]</textarea>
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
						<input type="hidden" name="GO" value="1"/>
						<button type="submit" class="btn btn-block btn-murphy">__SUBMIT__</button>
					</div>
				<div class="span5 "> 
						<a href="/[!Systeme::CurrentMenu::Url!]" class="btn btn-block btn-warning">__CANCEL__</a>
					</div>
				</div>
		</div>
	</form>
[/IF]
