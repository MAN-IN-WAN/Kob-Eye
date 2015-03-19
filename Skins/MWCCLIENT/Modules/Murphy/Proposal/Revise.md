// Demande de révision

<h3>__REVISE_PROPOSAL__</h3>

[IF [!Commentaire!]=]
    <form action="/[!Lien!]" method="post">        
        <div class="row-fluid">
            <div class="span10">
                <div class="control-group">
                    <label class="control-label" for="Commentaire">__REVISE_REASON__</label>
                    <div class="controls">                     
                        <textarea class="span12" name="Commentaire" id="Commentaire" cols="80" rows="8"></textarea>
                    </div>
                </div>
            </div>    
        </div>  
        <div class="row-fluid">
            <div class="span10">
                <div class="control-group">
                    <button type="submit" class="btn btn-inverse pull-right">__REVISE__</button>
                </div>
            </div>
        </div>
    </form>
[ELSE]
    [STORPROC [!Query!]|Proposal]
        [STORPROC Murphy/Enquiry/Proposal/[!Proposal::Id!]|Enquiry]
            [STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1]
                [STORPROC Murphy/Third/[!Th::Id!]/Enquiry/[!Enquiry::Id!]/Proposal/[!Proposal::Id!]|Prop]
                    [METHOD Prop|ReviseProposal]
                        [PARAM][!Commentaire!][/PARAM]
                    [/METHOD]
                    [REDIRECT][!Systeme::getMenu(Murphy/Enquiry)!][/REDIRECT]
                    [NORESULT]
                        <p>__ERR_NO_AUTH__</p>
                    [/NORESULT]
                [/STORPROC]
                [NORESULT]
                    <p>__ERR_NO_THIRD__</p>
                [/NORESULT]
            [/STORPROC]
            [NORESULT]
                <p>__ERR_NO_ENQ_PROPOSAL__</p>
            [/NORESULT]
        [/STORPROC]
        [NORESULT]
            <p>__ERR_NO_PROPOSAL__</p>
        [/NORESULT]
    [/STORPROC]    
[/IF]