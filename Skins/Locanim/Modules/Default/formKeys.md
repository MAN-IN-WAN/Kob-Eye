// formKeys.md
//
[!nom:=[!P::name!]!]
[!requrd:=!]
[!selreq:=!]
[!frmlbl:=!]
[!defval:=!]
[!maxchr:=!]
[OBJ [!P::objectModule!]|[!P::objectName!]|O2]
[!O2::setView()!]
[!fields:=[!O2::getElementsByAttribute(displayOrder,1,1)!]!]
[IF [!fields!]=][!fields:=[!O2::getSearchOrder()!]!][/IF]
[IF [!K!]]
	[!requrd:=,"editable":0,"setStyle":{"contentBackgroundColor":"silver"}!]
[ELSE]
	[IF [!P::obligatoire!]=1][!requrd:=,"required":1!][!selreq:=,"requireSelection":1!][/IF]
	[IF [!P::formLabel!]=1][!frmlbl:=,"formLabel":1!][/IF]
[/IF]
[IF [!P::defaultValue!]]
	[!defval:=,"defaultValue":"[!P::defaultValue!]"!]
[ELSE]
	[IF [!P::default!]]
		[!defval:=,"defaultValue":"[!P::default!]"!]
	[/IF]
[/IF]
[IF [!P::length!]][!maxchr:=,"maxChars":[!P::length!]!][/IF]
[IF [!P::parentDescription!]][!descr:=[!P::parentDescription!]!][ELSE][IF [!P::description!]][!descr:=[!P::description!]!][ELSE][!descr:=[!nom!]!][/IF][/IF]
[IF [!item!]>0],[/IF]
{"type":"FormItem","percentLabel":28,"label":"[!descr!]","percentWidth":100,"components":[
[SWITCH [!P::type!]|=]
	[CASE fkey]
		[IF [!P::card!]=long]
			[IF [!P::recursive!]=1]
				{"type":"Tree","dataField":"[!P::objectName!].[!nom!]","id":"CB:[!nom!]","checkBoxes":1,"percentWidth":100,"height":[IF [!P::height!]][!P::height!][ELSE]300[/IF],
					"kobeyeClass":{
						"module":"[!P::objectModule!]",
						"objectClass":"[!P::objectName!]",
						"children":["[!P::objectName!]"],
						[STORPROC [!O2::getElementsByAttribute(iconField,1)!]|Ic]
							[STORPROC [!Ic::elements!]|Id]
								"iconField":"[!Id::name!]"
							[/STORPROC]
							[NORESULT]
								"icon":"[!O2::getIcon!]"
							[/NORESULT]
						[/STORPROC]
					},
					"checkKobeyeClass":{
						"module":"[!P::objectModule!]",
						"parentClass":"[!P::objectName!]",
						"dirtyChild":1,
						[STORPROC [!O2::getElementsByAttribute(iconField,1)!]|Ic]
							[STORPROC [!Ic::elements!]|Id]
								"iconField":"[!Id::name!]"
							[/STORPROC]
							[NORESULT]
								"icon":"[!O2::getIcon!]"
							[/NORESULT]
						[/STORPROC]
					},
					"events":[
						{"type":"init", "action":"loadData"}
						,{"type":"start","action":"invoke","method":"loadCheckData"}
						//,{"type":"start","action":"loadValues","params":{"needsId":1}}
					]
				}
			[ELSE]
				{"type":"KeyList","dataField":"[!P::objectName!].[!nom!]","percentWidth":100,"height":80,
				"kobeyeClass":{"dirtyChild":1,"urlType":"parents","parentClass":"[!P::objectName!]","keyName":"[!P::name!]","form":"PopupList.json"
				//,"columns":[{"type":"varchar","field":"Designation"}]
				},
				"events":[
					{"type":"start","action":"loadValues","params":{"needsId":1}}
				]}
//				{"type":"HBox","percentWidth":100,"setStyle":{"horizontalGap":2},"localProxy":1,
//				"components":[
//					{"type":"List","dataField":"[!P::objectName!].[!nom!]","id":"LB:[!nom!]","percentWidth":100,"height":80,
//					"kobeyeClass":{"dirtyChild":1,"parentClass":"[!P::objectName!]","keyName":"[!P::name!]","form":"PopupList.json"},
//					"events":[
//						{"type":"start","action":"loadValues","params":{"needsId":1}},
//						{"type":"proxy", "triggers":[
//							{"trigger":"link[!P::objectName!]","action":"invoke","method":"linkParent"},
//							{"trigger":"unlink[!P::objectName!]","action":"invoke","method":"unlinkParent"}
//						]}
//					]},
//					{"type":"VGroup","percentWidth":"auto","gap":2,"components":[
//						{"type":"Button","label":"$__Link__$","id":"link[!P::objectName!]","width":70},
//						{"type":"Button","label":"$__Unlink__$","id":"unlink[!P::objectName!]","width":70}
//					]}
//				]}
			[/IF]
		[ELSE]
			{"type":"DataItem","percentWidth":100,"displayFields":[
				[STORPROC [!fields!]|P2|0|4]
					[IF [!Pos!]>1],[/IF]
					{"name":"[!P2::name!]","description":"[IF [!P2::description!]][!P2::description!][ELSE][!P2::name!][/IF]"}
				[/STORPROC]]
				,"keyType":"[!P::card!]","keyMandatory":true,"dataField":"[!P::objectName!].[!P::name!]",
				[IF [!P::dataFilter!]]"dataFilter":"[!P::dataFilter!]",[/IF]
				[IF [!P::exoFields!]]"exoFields":{
					[STORPROC [![!P::exoFields!]:/,!]|Z]
						[IF [!Pos!]>1],[/IF]
						[!Z2:=[![!Z!]:/::!]!]
						"[!Z2::0!]":"[!Z2::1!]"
					[/STORPROC]
					},
				[/IF]
				"kobeyeClass":{"dirtyChild":1,"module":"[!P::objectModule!]","parentClass":"[!P::objectName!]","keyName":"[!P::name!]",
				"select":["Id"
				[STORPROC [!fields!]|P2]
					,"[!P2::name!]"
				[/STORPROC]],
				[STORPROC [!O2::getElementsByAttribute(iconField,1)!]|Ic]
					[STORPROC [!Ic::elements!]|Id]
						"iconField":"[!Id::name!]"
					[/STORPROC]
					[NORESULT]
					"icon":"[!O2::getIcon!]"
					[/NORESULT]
				[/STORPROC]
					,"form":"PopupList.json"
				},
				"actions":[
					{"type":"start", "action":"loadValues"},
					{"type":"goto", "action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav","label":"[!descr!]","form":"FormBase.json"}},
					{"type":"proxy", "triggers":[
						{"trigger":"link[!P::objectName!]","action":"invoke","method":"linkParent"},
						{"trigger":"unlink[!P::objectName!]","action":"invoke","method":"unlinkParent"}
					]}
				]
			}
		[/IF]
	[/CASE]
	[CASE rkey]
	[/CASE]
[/SWITCH]
]}



