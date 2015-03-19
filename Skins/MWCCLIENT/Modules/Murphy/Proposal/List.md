
[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1]

	//CONFIG
	[IF [!page!]=][!page:=1!][/IF]
	[!NbLigneParPage:=5!]
	
	//REQUETE ET FILTRES
	[!REQUETE:=Murphy/Third/[!Th::Id!]/Proposal.ProposalSupplierId!]
	[SWITCH [!filter!]|=]
		[CASE waiting]
			[COUNT Murphy/Third/[!Th::Id!]/Proposal.ProposalSupplierId/StatusId=[!CONF::MODULE::MURPHY::STP_ANSWERED!]+StatusId=[!CONF::MODULE::MURPHY::STP_VALIDATED!]|NbArch]
			<section id="waiting">
			<div class="row-fluid">
				<div class="span10">
					<h3>__LISTE_PROPOSALS_WAITING__<span class="badge badge-warning" style="margin:0 0px 0px 10px; position:relative;top:-2px;">[IF [!NbArch!]][!NbArch!][ELSE]0[/IF]</span></h3>
					
				</div>
				<div class="span2">
				</div>
			</div>
			[!REQUETE+=/StatusId=[!CONF::MODULE::MURPHY::STP_ANSWERED!]+StatusId=[!CONF::MODULE::MURPHY::STP_VALIDATED!]!]
        		[!MESG_NO_RESULT:=__NO_RESULT__!]
		[/CASE]
		[CASE accepted]
			[COUNT Murphy/Third/[!Th::Id!]/Proposal.ProposalSupplierId/StatusId=[!CONF::MODULE::MURPHY::STP_ACCEPTED!]+StatusId=[!CONF::MODULE::MURPHY::STP_REFUSED!]+StatusId=[!CONF::MODULE::MURPHY::STP_REJECTED!]+StatusId=[!CONF::MODULE::MURPHY::STP_REVISED!]+StatusId=[!CONF::MODULE::MURPHY::STP_CANCELLED!]|NbArch]
			<section id="accepted">
			<h3>__LISTE_PROPOSALS_ACCEPTED__<span class="badge badge-success" style="margin:0 0px 0px 10px; position:relative;top:-2px;">[IF [!NbArch!]][!NbArch!][ELSE]0[/IF]</span></h3>
			[!REQUETE+=/StatusId=[!CONF::MODULE::MURPHY::STP_ACCEPTED!]+StatusId=[!CONF::MODULE::MURPHY::STP_REFUSED!]+StatusId=[!CONF::MODULE::MURPHY::STP_REJECTED!]+StatusId=[!CONF::MODULE::MURPHY::STP_REVISED!]+StatusId=[!CONF::MODULE::MURPHY::STP_CANCELLED!]!]
        		[!MESG_NO_RESULT:=__NO_RESULT__!]
		[/CASE]
		[DEFAULT]
			[COUNT Murphy/Third/[!Th::Id!]/Proposal.ProposalSupplierId/StatusId=[!CONF::MODULE::MURPHY::STP_DRAFT!]|NbArch]
			<section>
			<h3>__LISTE_PROPOSALS_ACTIVE__<span class="badge badge-important" style="margin:0 0px 0px 10px; position:relative;top:-2px;">[IF [!NbArch!]][!NbArch!][ELSE]0[/IF]</span></h3>
			[!REQUETE+=/StatusId=[!CONF::MODULE::MURPHY::STP_DRAFT!]!]
        		[!MESG_NO_RESULT:=__NO_RESULT__!]
		[/DEFAULT]
	[/SWITCH]
	
	//CALCUL PAGINATION
	[COUNT [!REQUETE!]|NB]
	[!NbPage:=[!NB:/[!NbLigneParPage!]!]!]
	[IF [!Math::Floor([!NbPage!])!]!=[!NbPage!]]
		[!NbPage:=[![!Math::Floor([!NbPage!])!]:+1!]!]
	[/IF]


    [STORPROC [!REQUETE!]|Prop|0|100|Date|DESC]
        [STORPROC Murphy/Enquiry/Proposal/[!Prop::Id!]|Enq][/STORPROC]
//        [MODULE Murphy/Proposal/MiniEtat?Prop=[!Prop!]&Enq=[!Enq!]]
<div class="well">
        [MODULE Murphy/Proposal/EtatDetail?Id=[!Prop::Id!]]
</div>
       [NORESULT]
        	//<div class="well">
        		[!MESG_NO_RESULT!]
        	//</div>
        [/NORESULT]
    [/STORPROC]    
    [IF [!NbPage!]>1]
        <div class="pagination  pagination-centered">
		    <ul>
			    <li class="[IF [!page:-1!]<1]disabled[/IF]"><a href="[IF [!page:-1!]>=1]?page=[!page:-1!][ELSE]#nogo[/IF]">Prev</a></li>
			    [STORPROC [!NbPage!]|P]
			    <li class="[IF [!P:+1!]=[!page!]]active[/IF]"><a href="?page=[!P:+1!]">[!P:+1!]</a></li>
			    [/STORPROC]
			    <li class="[IF [!page:+1!]>[!NbPage!]]disabled[/IF]"><a href="[IF [!page:+1!]<=[!NbPage!]]?page=[!page:+1!][ELSE]#nogo[/IF]">Next</a></li>
		    </ul>
	    </div>
	[/IF]
    [NORESULT]
        <p>__ERR_NO_THIRD__</p>
    [/NORESULT]
[/STORPROC]
</section>
