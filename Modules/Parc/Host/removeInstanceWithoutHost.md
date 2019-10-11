[STORPROC Parc/Instance|I|0|10000]
    [COUNT Parc/Instance/[!I::Id!]/Host|NbHost]
    [IF [!NbHost!]=0]
<li>[!I::Id!] - [!I::getFirstSearchOrder()!] - [!I::getSecondSearchOrder()!] - <b style="color:red">Suppression [!I::Delete()!]</b></li>
    [/IF]
[/STORPROC]