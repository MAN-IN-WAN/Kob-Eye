[STORPROC Parc/Domain|D|0|10000]
    <ul>
    [COUNT Parc/Client/Domain/[!D::Id!]|NB]
    [IF [!NB!]=0]
        <li>[!D::Url!]</li>
    [/IF]
    </ul>
[/STORPROC]