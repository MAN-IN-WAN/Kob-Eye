// formProperty.md
//
[!nom:=[!P::name!]!]
[!requrd:=!]
[!selreq:=!]
[!frmlbl:=!]
[!defval:=!]
[!maxchr:=!]
[!exofld:=!]
[!exoftr:=!]
[IF [!K!]]
	[!requrd:=,"editable":0,"setStyle":{"contentBackgroundColor":"silver"}!]
[ELSE]
	[IF [!P::obligatoire!]=1][!requrd:=,"required":1!][!selreq:=,"requireSelection":1!][/IF]
	[IF [!P::formLabel!]=1][!frmlbl:=,"formLabel":1!][/IF]
	[IF [!P::formToolTip!]=1][!frmlbl+=,"formToolTip":1!][/IF]
[/IF]
[IF [!P::defaultValue!]]
	[!defval:=,"defaultValue":"[!P::defaultValue!]"!]
[ELSE]
	[IF [!P::default!]]
		[!defval:=,"defaultValue":"[!P::default!]"!]
	[/IF]
[/IF]
[IF [!P::width!]][!width:="width":[!P::width!]!][ELSE][!width:="percentWidth":100!][/IF]
[IF [!P::readOnly!]||[!P::auto!]][!edit:=,"editable":0,"setStyle":{"contentBackgroundColor":"0xe8e8e8"}!][/IF]
[IF [!P::length!]][!maxchr:=,"maxChars":[!P::length!]!][/IF]
//[IF [!P::exoFields!]][!exofld:=,"exoFields":{[!P::exoFields!]}!][/IF]
[IF [!P::exoFields!]][!exofld:="exoFields":{!]
	[STORPROC [![!P::exoFields!]:/,!]|Z]
		[IF [!Pos!]>1][!exofld+=,!][/IF]
		[!Z2:=[![!Z!]:/::!]!]
		[!exofld+="[!Z2::0!]":"[!Z2::1!]"!]
	[/STORPROC]
	[!exofld+=},!]
[/IF]
[IF [!P::exoFilters!]][!exoftr:="exoFilters":{!]
	[STORPROC [![!P::exoFilters!]:/,!]|Z]
		[IF [!Pos!]>1][!exoftr+=,!][/IF]
		[!Z2:=[![!Z!]:/::!]!]
		[!exoftr+="[!Z2::0!]":"[!Z2::1!]"!]
	[/STORPROC]
	[!exoftr+=},!]
[/IF]
[IF [!P::forceEvent!]][!event:=,"forceEvent":[!P::forceEvent!]!][/IF]
[IF [!item!]>0],[/IF]
[IF [!P::notInLayout!]]
	[!edit:=,"includeInLayout":0,"visible":0,"editable":0!]
[ELSE]
	{"type":"FormItem","percentLabel":28,"label":"[IF [!P::description!]][!P::description!][ELSE][!nom!][/IF]","percentWidth":100,"components":[
[/IF]
[SWITCH [!P::type!]|=]
	[CASE fkey]
		[IF [!P::card!]=long]
			[IF [!P::recursive!]=1]
				{"type":"Tree","dataField":"[!nom!]","id":"CB:[!nom!]","checkBoxes":1,[!width!],
				"kobeyeClass":{"dirtyChild":1,"module":"[!P::objectModule!]","parentClass":"[!P::objectName!]","children":["[!P::objectName!]"],"select":"Id"},
				"otherKobeyeClass":{
					"[!P::objectName!]":{"module":"[!P::objectModule!]","objectClass":"[!P::objectName!]","children":["[!P::objectName!]"]}
				},
				"events":[
					{"type":"init","action":"invoke","method":"loadParents"},
					{"type":"start","action":"loadValues","params":{"needsId":1}}
				]}
			[ELSE]
				{"type":"HBox",[!width!],"setStyle":{"horizontalGap":2},"localProxy":1,
				"components":[
					{"type":"List","dataField":"[!nom!]","id":"LB:[!nom!]",[!width!],"height":80,
					"kobeyeClass":{"dirtyChild":1,"parentClass":"[!P::objectName!]","form":"PopupList.json"},
					"events":[
						{"type":"start","action":"loadValues","params":{"needsId":1}},
						{"type":"proxy", "triggers":[
							{"trigger":"link[!P::objectName!]","action":"invoke","method":"linkParent"},
							{"trigger":"unlink[!P::objectName!]","action":"invoke","method":"unlinkParent"}
						]}
					]},
					{"type":"VGroup","percentWidth":"auto","gap":2,"components":[
						{"type":"Button","label":"$__Link__$","id":"link[!P::objectName!]"},
						{"type":"Button","label":"$__Unlink__$","id":"unlink[!P::objectName!]"}
					]}
				]}
			[/IF]
		[ELSE]
			{"type":"TextInput",[!width!],"editable":0,
			"kobeyeClass":{"copyParent":1},
			"actions":[
				{"type":"start","action":"loadData"}
			]}
		[/IF]
	[/CASE]
	[CASE rkey]
	[/CASE]
	[CASE file]
		{"type":"Upload","dataField":"[!nom!]",[!width!][!event!]}
	[/CASE]
	[CASE text]
		{"type":"TextArea","dataField":"[!nom!]",[!width!],"height":[IF [!dico!]]120[ELSE]70[/IF],"validType":"string" [!requrd!][!frmlbl!][!defval!][!edit!][!event!]}
	[/CASE]
	[CASE txt]
		{"type":"TextArea","dataField":"[!nom!]",[!width!],"height":[IF [!dico!]]120[ELSE]70[/IF],"validType":"string" [!requrd!][!frmlbl!][!defval!][!edit!][!event!]}
	[/CASE]
	[CASE bbcode]
		{"type":"TextArea","dataField":"[!nom!]",[!width!],"height":[IF [!dico!]]120[ELSE]70[/IF],"validType":"string" [!requrd!][!frmlbl!][!defval!][!edit!][!event!]}
	[/CASE]
	[CASE html]
		{"type":"RichTextEditor","dataField":"[!nom!]",[!width!],"height":150,"validType":"string" [!requrd!][!frmlbl!][!defval!][!edit!][!event!],"setStyle":{"headerHeight":0,"dropShadowVisible":0,"borderVisible":0}}
	[/CASE]
	[CASE bbcode]
	[/CASE]
	[CASE int]
		[IF [!Utils::isArray([!P::Values!])!]]
//			{"type":"ComboBox","dataField":"[!nom!]",[!width!][!defval!][!event!] [!selreq!],"dataProvider":[
			{"type":"ComboBox","dataField":"[!nom!]",[!width!][!defval!][!event!] [!requrd!][!edit!],"dataProvider":[
				[STORPROC [!P::Values!]|Val]
					[LIMIT 0|100]
						[IF [!Pos!]>1],[/IF]
						[!T:=[![!Val!]:/::!]!]	
						[COUNT [!T!]|S]
						[IF [!S!]>1]
							{"data":"[!T::0!]","label":"[!T::1!]"}
						[ELSE]
							{"data":"[!Val!]","label":"[!Val!]"}
						[/IF]
					[/LIMIT]
					[NORESULT]
					[/NORESULT]
				[/STORPROC]
			]}
		[ELSE]
			[IF [!P::query!]]
				[!q0:=[![!P::query!]:/::!]!]
				[INFO [!P::query!]|Q]
//				{"type":"ComboBox","dataField":"[!nom!]",[!width!][!event!] [!selreq!],[IF [!P::stateGroup!]]"stateGroup":"[!P::stateGroup!]",[/IF]
				{"type":"ComboBox","dataField":"[!nom!]",[!width!][!defval!][!event!] [!requrd!][!edit!],[IF [!P::stateGroup!]]"stateGroup":"[!P::stateGroup!]",[/IF]
//				[IF [!P::dataFilter!]][!T:=[![!P::dataFilter!]:/::!]!]"dataFilter":{"field":"[!T::0!]","equals":"[!T::1!]","value":"[!T::2!]"},[/IF]
				[IF [!P::dataFilter!]]"dataFilter":"[!P::dataFilter!]",[/IF][!exofld!][!exoftr!]
				"kobeyeClass":{"module":"[!Q::Module!]","objectClass":"[!Q::TypeChild!]"[IF [!Q::Identifier!]],"identifier":"[!Q::Identifier!]"[/IF][IF [!Q::Label!]],"label":"[!Q::Label!]"[/IF]
				,"limit":500[IF [!P::extra!]],"extra":"[!P::extra!]"[/IF]
				[IF [!P::masterField!]]
					[IF [!P::masterField!]=Id]
					,"dirtyParent":1},
					"actions":[
						{"type":"init","action":"loadData"}
					]
					[ELSE]
					,"query":"[!q0::0!]"
					[IF [!P::masterObject!]],"parentClass":"[!P::masterObject!]"[/IF]},
					"masterField":"[!P::masterField!]"
					[/IF]				
				[ELSE]
					,"query":"[!q0::0!]"},
					"actions":[
						{"type":"init","action":"loadData"}
					]
				[/IF]
				}
			[ELSE]
				{"type":"TextInput","dataField":"[!nom!]","width":100,"maxChars":11,"validType":"int","setStyle":{"textAlign":"end"} [!maxchr!][!requrd!][!frmlbl!][!defval!][!edit!][!event!]}
			[/IF]
		[/IF]
	[/CASE]
	[CASE order]
			{"type":"TextInput","dataField":"[!nom!]",[!width!],"maxChars":11,"validType":"int" [!maxchr!][!requrd!][!frmlbl!][!defval!][!edit!][!event!]}
	[/CASE]
	[CASE date]
			{"type":"DateField","dataField":"[!nom!]","validType":"date" [!requrd!][!frmlbl!][!defval!][!edit!][!event!]}
	[/CASE]
	[CASE float]
		{"type":"TextInput","dataField":"[!nom!]","width":100,"maxChars":11,"validType":"float","setStyle":{"textAlign":"end"} [!requrd!][!frmlbl!][!defval!][!edit!][!event!]}
	[/CASE]
	[CASE price]
		{"type":"TextInput","dataField":"[!nom!]","width":100,"maxChars":11,"validType":"float","setStyle":{"textAlign":"end"} [!requrd!][!frmlbl!][!defval!][!edit!][!event!]}
	[/CASE]
	[CASE boolean]
		{"type":"CheckBox","dataField":"[!nom!]",[!width!] [!defval!][!edit!][!event!]}
	[/CASE]
	[CASE mail]
		{"type":"TextInput","dataField":"[!nom!]",[!width!],"validType":"email" [!maxchr!][!requrd!][!frmlbl!][!defval!][!edit!][!event!]}
	[/CASE]
	[CASE password]
		{"type":"TextInput","dataField":"[!nom!]",[!width!],"displayAsPassword":1 [!requrd!][!edit!][!event!]}
	[/CASE]
	[CASE image]
		{"type":"ImageUpload","dataField":"[!nom!]",[!width!][!event!]}
	[/CASE]
	[DEFAULT]
		[IF [!Utils::isArray([!P::Values!])!]]
//			{"type":"ComboBox","dataField":"[!nom!]",[!width!][!event!] [!selreq!],"dataProvider":[
			{"type":"ComboBox","dataField":"[!nom!]",[!width!][!event!] [!requrd!][!edit!],"dataProvider":[
				[STORPROC [!P::Values!]|Val]
					[LIMIT 0|100]
						[IF [!Pos!]>1],[/IF]
						[!T:=[![!Val!]:/::!]!]	
						[COUNT [!T!]|S]
						[IF [!S!]>1]
							{"data":"[!T::0!]","label":"[!T::1!]"}
						[ELSE]
							{"data":"[!Val!]","label":"[!Val!]"}
						[/IF]
					[/LIMIT]
					[NORESULT]
					[/NORESULT]
				[/STORPROC]
			]}
		[ELSE]
			[IF [!P::query!]]
				[INFO [!P::query!]|Q]
//				{"type":"ComboBox","dataField":"[!nom!]",[!width!][!event!] [!selreq!],
				{"type":"ComboBox","dataField":"[!nom!]",[!width!][!event!] [!requrd!][!edit!],
				[SWITCH [!Q::TypeSearch!]|=]
					[CASE conf]
						"kobeyeClass":{"conf":"[!Q::Query!]"},
					[/CASE]
					[DEFAULT]
						"kobeyeClass":{"module":"[!Q::Module!]","query":"[!Q::Query!]","objectClass":"[!Q::TypeChild!]"[IF [!Q::Identifier!]],"identifier":"[!Q::Identifier!]"[/IF][IF [!Q::Label!]],"label":"[!Q::Label!]"[/IF]},
					[/DEFAULT]
				[/SWITCH]
				"actions":[
					{"type":"init","action":"loadData"}
				]}
			[ELSE]
				{"type":"TextInput","dataField":"[!nom!]",[!width!],"validType":"string" [!maxchr!][!requrd!][!frmlbl!][!defval!][!edit!][!event!]}
			[/IF]
		[/IF]
	[/DEFAULT]
[/SWITCH]
[IF [!P::notInLayout!]!=1]
	]}
[/IF]



