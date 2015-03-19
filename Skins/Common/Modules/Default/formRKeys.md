// formRKeys.md
//
[!nom:=[!P::name!]!]
[IF [!item!]>0],[/IF]
{"type":"FormItem","percentLabel":35,"label":"[IF [!P::parentDescription!]][!P::parentDescription!][ELSE][IF [!P::description!]][!P::description!][ELSE][!nom!][/IF][/IF]","percentWidth":100,
"components":[
[IF [!P::childrenGrid!]]
	[!T:=[![!P::childrenGrid!]:/::!]!]
	[OBJ [!P::objectModule!]|[!P::objectName!]|O2]
	{"type":"ChildrenGrid","dataField":"[!P::objectName!].[!nom!]","percentWidth":100,"height":[IF [!P::height!]][!P::height!][ELSE]300[/IF],
	[IF [!P::confirm!]]"confirmText":"[!P::confirm!]",[/IF]
	"kobeyeClass":{"dirtyParent":1,"module":"[!P::objectModule!]","objectClass":"[!P::objectName!]","keyName":"[!nom!]"},
	[IF [!T::1!]]
		"otherKobeyeClass":{"module":"[!P::objectModule!]","parentClass":"[!T::1!]","objectClass":"[!T::2!]","extra":"[!T::1!].[!T::3!]","urlType":"children"},
	[/IF]
	//"stateGroup":"savedIdle",
	"columns":[
		{"type":"column","dataField":"Id","headerText":"ID","visible":0,"editable":0}
		[STORPROC [!O2::getElements()!]|categ]
			[STORPROC [!categ!]|media]
				[STORPROC [!media!]|E]
					[IF [!E::hidden!]!=1]
						[SWITCH [!E::type!]|=]
							[CASE boolean]
								[!cf:=checkbox!]
							[/CASE]
							[DEFAULT]
								[!cf:=!]
							[/DEFAULT]
						[/SWITCH]
						,{"type":"column","dataField":"[!E::name!]","format":"[!cf!]",
						"headerText":[IF [!E::listDescr!]]"[!E::listDescr!]"[ELSE][IF [!E::description!]]"[!E::description!]"[ELSE]"[!P::name!]"[/IF][/IF]
						[IF [!E::listWidth!]],"width":"[!E::listWidth!]"[/IF]
						}
					[/IF]
				[/STORPROC]
			[/STORPROC]
		[/STORPROC]
		//,{"type":"column","width":0}
	],
	"events":[
		{"type":"start","action":"loadValues","params":{"needsParentId":1}}
	]}
[ELSE]
	{"type":"KeyList","dataField":"[!P::objectName!].[!nom!]","percentWidth":100,"height":80,
	"kobeyeClass":{"dirtyParent":1,"urlType":"children","objectClass":"[!P::objectName!]","keyName":"[!nom!]","form":"PopupList.json"
	//,"columns":[{"type":"varchar","field":"Designation"}]
	},
	"events":[
		{"type":"start","action":"loadValues"}
	]}
[/IF]
]}
