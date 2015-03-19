

[STORPROC [!Query!]|Enq|0|1]
<div class="alert alert-info">
	<strong>__MY_ENQUIRY__</strong>
</div>

<form action="/[!Lien!]" method="post" class="form-horizontal" enctype="multipart/form-data">
	[MODULE Murphy/Enquiry/EtatDetail?Id=[!Enq::Id!]]
</form>
<div class="alert alert-info">
	<strong>__MY_PROPOSALS__</strong>
</div>

	<!-- AFFICHAGE DES PROPOSITIONS VALIDES -->
	[STORPROC Murphy/Enquiry/[!Enq::Id!]/Proposal/StatusId>=[!CONF::MODULE::MURPHY::STP_VALIDATED!]+StatusId=[!CONF::MODULE::MURPHY::STP_REFUSED!]|Prop|0|100|Date|DESC]
		//Changement de status
		[IF [!propid!]=[!Prop::Id!]]
			[IF [!accept!]]
				[IF [!approval!]]
					[METHOD Prop|AcceptWithApproval][/METHOD]
					<div class="alert alert-success">__PROPOSAL_ACCEPTED_WITH_APPROVAL__</div>
				[ELSE]
					[METHOD Prop|AcceptProposal][/METHOD]
					<div class="alert alert-success">__PROPOSAL_ACCEPTED__</div>
				[/IF]
			[/IF]
			[IF [!requestsample!]]
				<div class="alert alert-success">__SAMPLE_REQUESTED__</div>
				[METHOD Prop|CreateSampleRequest][/METHOD]
			[/IF]
			[IF [!refuse!]]
				[METHOD Prop|RefuseProposal][/METHOD]
				<div class="alert alert-danger">__PROPOSAL_REFUSED__</div>
			[/IF]
			[IF [!reviseconfirm!]]
				[METHOD Prop|ReviseProposal][PARAM][!revisecomment!][/PARAM][/METHOD]
				<div class="alert alert-warning">__PROPOSAL_REVISED__</div>
			[/IF]
			[STORPROC Murphy/Proposal/Id=[!Prop::Id!]|Prop][/STORPROC]
		[/IF]




<form action="/[!Lien!]" method="post" class="form-horizontal" enctype="multipart/form-data">
	<input type="hidden" name="propid" value="[!Prop::Id!]"/>
		<div class="well" style="margin-left:50px;">
			[MODULE Murphy/Proposal/EtatDetail?Id=[!Prop::Id!]&NODETAIL=1]
			[IF [!Prop::StatusId!]=[!CONF::MODULE::MURPHY::STP_VALIDATED!]]
			<div class="control-group">
			<input type="checkbox" class="pull-left" style="margin-right:10px;" name="approval" value="1" checked><label><strong><em> (*) __SUBJECT_TO_SAMPLE_APPROVAL__</em></strong></label>
			</div>
			<div class="row-fluid" style="margin-top:10px;">
				[IF [!revise!]]
					<div class="span6">
						<div class="control-group [IF [!E_Appellation!]]error[/IF]">
							<label class="control-label" for="revisecomment">__COMMENT_REVISE__</label>
							<div class="controls">                
								<textarea name="revisecomment"></textarea>          
							</div>
						</div>
					</div>
					<div class="span6">
						<button name="reviseconfirm" value="1" class="btn btn-murphy btn-block">__SEND_COMMENT_REVISER__</button>
						<a href="/[!Lien!]" class="btn btn-warning btn-block">__CANCEL__</a>
					</div>
				[ELSE]
					<div class="span2">
						<button name="accept" value="1" class="btn btn-success btn-block">__ACCEPT__<em> *</em></button>
						<!-- FONCTION AcceptProposal -->
					</div>
					<div class="span2">
						<button name="refuse" value="1"  class="btn btn-danger btn-block">__REFUSER__</button>
						<!-- FONCTION RefuseProposal -->
					</div>
					<div class="span2">
						<button name="requestsample" value="1"  class="btn btn-danger btn-murphy btn-medium" style="width:200px;">__REQUEST_SAMPLE__</button>
						<!-- FONCTION RefuseProposal -->
					</div>
<!--					<div class="span2 offset1">
						<button name="revise" value="1" class="btn btn-warning btn-block">__REVISER__</button>
					</div>
-->
				[/IF]
			</div>
			[/IF]
		</div>
	    //Affichage sample requests
	    [STORPROC Murphy/Proposal/[!Prop::Id!]/SampleRequest|SR]
		<div class="alert alert-warning"  style="margin-left:50px;">
			<div class="row-fluid" >
			    <div class="span12">
			    <span class="label">__SAMPLE_REQUEST__</span>
		            <span class="label">Ref: [!SR::Reference!]</span>
		        	[STORPROC Murphy/Status/[!SR::StatusId!]|Item]
			        	<span class="label" style="background-color:[!Item::Color!]" class="pull-right">[!Item::Status!]</span>
		        	[/STORPROC]
				<i class="icon-info-sign"></i>
				<strong>__DATE_CREATION__  [DATE d/m/Y][!SR::Date!][/DATE]</strong>
			    </div>
			</div>
		</div>
	    [/STORPROC]
</form>
		<!-- PREVOIR DISCUSSION -->
	
	[/STORPROC]
	[NORESULT]
		<div class="alert alert-error">__NO_ENQUIRY__</div>
		<div class="well">
			<a href="/[!Systeme::CurrentMenu::Url!]" class="btn btn-danger btn-block">__LISTE_DEMANDES__</a>
		</div>
	[/NORESULT]
[/STORPROC]
