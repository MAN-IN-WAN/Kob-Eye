[STORPROC [!Query!]|Site][/STORPROC]

[!CptFound:=0!]
[STORPROC Systeme/Page/Valid=1&&Publier=1|Fo]
	[IF [!Fo::Url!]~http://[!Site::Domaine!]][!CptFound+=1!][/IF]
[/STORPROC]

[!CptIgnore:=0!]
[STORPROC Systeme/Page/Valid=1&&Publier=0|Ig]
	[IF [!Ig::Url!]~http://[!Site::Domaine!]][!CptIgnore+=1!][/IF]
[/STORPROC]

[!CptOld:=0!]
[STORPROC Systeme/Page/Valid=1&&Publier=0|Ol]
	[IF [!Ol::Url!]~http://[!Site::Domaine!]][!CptOld+=1!][/IF]
[/STORPROC]



{
	"Found" : "[!CptFound!]",
	"Ignore" : "[!CptIgnore!]",
	"Old" : "[!CptOld!]",
	"Tab" : [
		[STORPROC Systeme/Page|P|0|10000|Id|ASC]
			[IF [!P::Url!]~http://[!Site::Domaine!]]
				{
					"Id" : "[!P::Id!]",
					"Url" : "[!P::Url!]",
					"Valid" : "[!P::Valid!]",
					"Publier" : "[!P::Publier!]"
				},
			[/IF]
		[/STORPROC]
	]
}