[!Page:=!]
[!Edit:=!]

[OBJ Flipbook|Book|Q]
	[METHOD Q|setQuery|Result]
		[PARAM][!Query!][/PARAM]
	[/METHOD]
	
[OBJ Flipbook|Book|P]
	[METHOD P|getPage|pageNb]
		[PARAM][!Query!][/PARAM]
	[/METHOD]

[!countPages:=1!]
[STORPROC [!Result!]/Page|Pages|0|[!pageNb!]|Image|ASC]
	[IF [!countPages!]=[!pageNb!]]
//		[!Page:=[!Pages::Image!].limit.1654x2339.jpg;!]
		[!Page:=[!Pages::Image!];!]
		[!Edit:=[!Pages::tmsEdit!];!]
	[/IF]
	[!countPages+=1!]
[/STORPROC]

[STORPROC [!Result!]|Book][/STORPROC]


[OBJ Flipbook|Book|Pdf]
	[METHOD Pdf|saveToPdf]
		[PARAM][!Page!][/PARAM]
		[PARAM][!Edit!][/PARAM]
		[PARAM][!Book::Titre!][/PARAM]
		[PARAM]false[/PARAM]
		[PARAM][!pageNb!][/PARAM]
	[/METHOD]