[STORPROC [!MyArray!]|A]
    [IF [!Key!]!=SCHEMA]
    <li><span style="font-weight:bold;">[!Key!]</span> &nbsp;-->&nbsp;
	[IF [!Utils::isArray([!A!])!]]
	    <ul>
		[MODULE Systeme/Configuration/Infos/Liste?MyArray=[!A!]]
	    </ul>
	[ELSE]
	    [!A!]
	[/IF]
    </li>
    [/IF]
[/STORPROC]
