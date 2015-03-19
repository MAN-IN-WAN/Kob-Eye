// Annulation par le client

<h3>__CANCEL_PROPOSAL__</h3>

[STORPROC [!Query!]|Proposal]
    [STORPROC Murphy/Enquiry/Proposal/[!Proposal::Id!]|Enquiry]
        [STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1]
            [STORPROC Murphy/Third/[!Th::Id!]/Enquiry/[!Enquiry::Id!]/Proposal/[!Proposal::Id!]|Prop]
                [METHOD Prop|RejectProposal][/METHOD]
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