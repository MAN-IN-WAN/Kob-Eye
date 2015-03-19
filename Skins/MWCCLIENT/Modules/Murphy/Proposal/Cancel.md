// Annulation par le fournisseur

<h3>__CANCEL_PROPOSAL__</h3>

[STORPROC [!Query!]|Proposal][/STORPROC]
[STORPROC Systeme/User/[!Systeme::User::Id!]/Third|Th|0|1]
    [STORPROC Murphy/Third/[!Th::Id!]/Proposal/[!Proposal::Id!]|Prop]
        [METHOD Prop|RefuseProposal][/METHOD]
        [REDIRECT][!Systeme::getMenu(Murphy/Proposal)!][/REDIRECT]
        [NORESULT]
            <p>__ERR_NO_AUTH__</p>
        [/NORESULT]
    [/STORPROC]
    [NORESULT]
        <p>__ERR_NO_THIRD__</p>
    [/NORESULT]
[/STORPROC]