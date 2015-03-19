//XMLREAD

[STORPROC [!A!]|U]
    [STORPROC [!U::#!]|T]
	[STORPROC [!T!]|V]
	<div style="background:white;[IF [!Pos!]>1]margin-top:10px;[/IF]-moz-border-radius:3px 3px;padding:3px;;">
		<div class="BigTitle">[!Z:=[!V::@!]!]
		[!Z::title!]</div>
		<ul>
	    [STORPROC [!V::@!]|W]
		<li> <span style="font-weight:bold;">
			[!Key!]
		    </span>
		    &nbsp;-->&nbsp;
		    [!W!]
		</li>
	    [/STORPROC]
	    [STORPROC [!V::#!]|W]
		<li>
		    <span style="font-weight:bold;">
			[!Key!]
		    </span>
		    <ul>
			[STORPROC [!W!]|C]
			    <li> [!C::#!]
				{
				[STORPROC [!C::@!]|D]
				    [!Key!] : "[!D!]"
				    [IF [!Pos!]!=[!NbResult!]]
					,&nbsp;
				    [/IF]
				[/STORPROC]
				}
			[/STORPROC]
		    </ul>
		</li>
	    [/STORPROC]
	    </ul>
	    </div>
	[/STORPROC]
    [/STORPROC]
    [MODULE Systeme/Configuration/Infos/Schema?A=[!U::@!]]
[/STORPROC]
