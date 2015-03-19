[OBJ [!P::objectModule!]|[!P::objectName!]|O2]

[IF [!item!]>0],[/IF]
[!item2:=0!]
{"type":"EditContainer","id":"[!P::name!]","percentWidth":100,"parentData":1,
"kobeyeClass":{"copyParent":1},
//"module":"[!P::objectModule!]","parentClass":"[!P::objectName!]"},
"components":[
	{"type":"Form","setStyle":{"percentLabel":35,"verticalGap":1,"paddingLeft":1,"paddingRight":1,"paddingTop":0,"paddingBottom":1},"percentWidth":100,
	"components":[
		[STORPROC [!O2::getElementsByAttribute(parentData,1)!]|categ]
			[STORPROC [!categ!]|media]
				[STORPROC [!media!]|element]
					[MODULE Systeme/formProperty?P=[!element!]&O=[!O2!]&item=[!item2!]&K=1]
					[!item2+=1!]
				[/STORPROC]
			[/STORPROC]
			[NORESULT]
				[!mashin:=0!]
				[STORPROC [!O2::getElementsByAttribute(searchOrder)!]|categ]
					[STORPROC [!categ!]|media]
						[STORPROC [!media!]|element]
							[IF [!mashin!]<2]
								[MODULE Systeme/formProperty?P=[!element!]&O=[!O2!]&item=[!item2!]&K=1]
								[!item2+=1!]
							[/IF]
							[!mashin+=1!]
						[/STORPROC]
					[/STORPROC]
				[/STORPROC]
			[/NORESULT]
		[/STORPROC]
	]}
],
"events":[
	{"type":"start","action":"loadValues"}
]}

