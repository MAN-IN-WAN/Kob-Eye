[STORPROC [!Query!]|Art][/STORPROC]
[IF [!SHOWCREDS!]!=]
    [!creds:=1!]
[ELSE]
    [!creds:=0!]
[/IF]
[!Art::generateHeader([!creds!])!]