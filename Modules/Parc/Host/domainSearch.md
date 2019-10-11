<ul>
    [STORPROC Parc/Apache/ApacheServerName~%[!domain!]%+ApacheServerAlias~%[!domain!]%|AP|0|10]
    [STORPROC Parc/Host/Apache/[!AP::Id!]|H|0|1]
    [STORPROC Parc/Instance/Host/[!H::Id!]|I|0|1]
    [/STORPROC]
    [/STORPROC]
    <li>Instance: [!I::Id!] - [!I::getFirstSearchOrder()!]<br />
    Host: [!H::Id!] - [!H::getFirstSearchOrder()!]</li>
[/STORPROC]</ul>