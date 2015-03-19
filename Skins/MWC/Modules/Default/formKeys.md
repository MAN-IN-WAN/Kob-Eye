// formProperty.md
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
[IF [!P::notInLayout!]]
	[!edit:=,"includeInLayout":0,"visible":0!]
[ELSE]
	{"type":"FormItem","percentLabel":28,"label":"[!descr!]","percentWidth":100,"components":[
[/IF]
[SWITCH [!P::type!]|=]
	[CASE fkey]
		[IF [!P::recursive!]=1]
			{"type":"Tree","dataField":"[!P::objectName!].[!nom!]","id":"CB:[!nom!]","checkBoxes":1,"percentWidth":100,"height":300,
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
			{"type":"DataItem","percentWidth":100 [!edit!],"displayFields":[
				[STORPROC [!fields!]|P2|0|6]
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
				[IF [!P::noControl!]]"noControl":1,[/IF]
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
						{"trigger":"cancel","action":"invoke","method":"cancelEdit"},
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
[IF [!P::notInLayout!]!=1]
	]}
[/IF]



