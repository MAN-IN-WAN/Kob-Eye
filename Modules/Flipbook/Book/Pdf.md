[!Page:=!]
[!Edit:=!]

[STORPROC [!Result!]/Page|Pages|0|10000|Image|ASC]
//	[!Page+=[!Pages::Image!].limit.1654x2339.jpg;!]
	[!Page+=[!Pages::Image!];!]
	[!Edit+=[!Pages::tmsEdit!];!]
[/STORPROC]


[STORPROC [!Query!]|Book][/STORPROC]

[OBJ Flipbook|Book|Pdf]
	[METHOD Pdf|saveToPdf]
		[PARAM][!Pages!][/PARAM]
		[PARAM][!Page!][/PARAM]
		[PARAM][!Edit!][/PARAM]
		[PARAM][!Book::Titre!][/PARAM]
		[PARAM]false[/PARAM]
		[PARAM][/PARAM]
	[/METHOD]