[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
[!elements:=[!O::getElements()!]!]
[!O::setView()!]
[!select:=Id!]
[!columns:={"type":"column","dataField":"Id","headerText":"ID","visible":0}!]
[!EL:=[!O::getElementsByAttribute(list,,1)!]!]
[IF [!EL!]=][!EL:=[!O::getSearchOrder()!]!][/IF]
[STORPROC [!EL!]|P]
	[ORDER list|ASC]
		[!select+=,[!P::name!]!]
		[!columns+=,{"type":"column","dataField":"[!P::name!]","headerText":!]
		[IF [!P::listDescr!]][!columns+="[!P::listDescr!]"!][ELSE][IF [!P::description!]][!columns+="[!P::description!]"!][ELSE][!columns+="[!P::name!]"!][/IF][/IF]
		[IF [!P::listExtra!]]
			[!cptext:=0!]
			[!columns+=,"extra":[ !]
			[!exts:=[![!P::listExtra!]:/,!]!]
			[STORPROC [!exts!]|ext]
				[IF [!cptext!]>0][!columns+=, !][/IF][!cptext+=1!]
				[!columns+="[!ext!]"!]
			[/STORPROC]
			[!columns+= ]!]
		[/IF]
		[IF [!P::asImage!]][!type:=image!][ELSE][!type:=[!P::type!]!][/IF]
		[SWITCH [!type!]|=]
			[CASE date]
				[!cf:=date!][!cw:=60!]
			[/CASE]
			[CASE time]
				[!cf:=time!][!cw:=95!]
			[/CASE]
			[CASE boolean]
				[!cf:=boolean!][!cw:=24!][!columns+=,"setStyle":{"paddingLeft":1,"paddingRight":1}!]
			[/CASE]
			[CASE image]
				[!cf:=image!][!cw:=60!]
			[/CASE]
			[CASE price]
				[!cf:=2dec!][!cw:=80!]
			[/CASE]
			[CASE pourcent]
				[!cf:=progress!][!cw:=80!]
			[/CASE]
			[CASE color]
				[!cf:=color!][!cw:=80!]
			[/CASE]
			[CASE float]
				[!cw:=80!]
			[/CASE]
			[CASE int]
				[!cw:=50!]
			[/CASE]
			[CASE text]
				[!cw:=300!]
			[/CASE]
			[DEFAULT]
				[!cf:=!][!cw:=150!]
			[/DEFAULT]
		[/SWITCH]
		[IF [!P::format!]][!cf:=[!P::format!]!][/IF]
		[!columns+=,"format":"[!cf!]"!]
		[IF [!P::listWidth!]][!columns+=,"width":[!P::listWidth!]!][ELSE][!columns+=,"width":[!cw!]!][/IF]
		[!columns+=}!]
	[/ORDER]
[/STORPROC]
[!conf:=[!O::getConfiguration!]!][!conf:=[!conf::Interface!]!][!conf:=[!conf::FormDico!]!]


{"form":{"type":"TitleWindow","id":"DC:[!I::Module!]/[!I::TypeChild!]","title":"[!O::getDescription()!]","width":800,"height":600,
//"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},
"components":[
	{"type":"VBox","setStyle":{"paddingRight":5,"paddingTop":5},"percentWidth":100,"percentHeight":100,"minHeight":1,
	"localProxy":{
		"actions":{
			"proxy_kobeye_status":{"action":[
				{"action":"invoke","method":"groupState","params":{"group":"saved","property":"enabled","hasID":1}},
				{"action":"invoke","method":"groupState","params":{"group":"idle","property":"enabled","idle":1}},
				{"action":"invoke","method":"groupState","params":{"group":"updated","property":"enabled","updated":1}},
				{"action":"invoke","method":"groupState","params":{"group":"savedIdle","property":"enabled","hasID":1,"idle":1}},
				{"action":"invoke","method":"groupState","params":{"group":"savedAdmin","property":"enabled","hasID":1,"admin":1}}
			]}
		}
	},	
	"components":[
		{"type":"HGroup","components":[
			{"type":"Spacer"},
			{"type":"Button","id":"new","label":"$__New__$","width":100},
			{"type":"Button","id":"save","label":"$__Save__$","width":100,"enabled":0,"stateGroup":"updated"},
			[IF [!intf::hideDelete!]!=1]{"type":"Button","id":"delete","label":"$__Delete__$","width":100,"enabled":0,"stateGroup":[IF [!conf::hideDelete!]=admin]"savedAdmin"[ELSE]"saved"[/IF]},[/IF]
			{"type":"Button","id":"cancel","label":"$__Cancel__$","width":100,"enabled":0,"stateGroup":"updated"},
			{"type":"Button","id":"close","label":"$__Close__$","width":100}
		]},
		{"type":"DividedBox","direction":"horizontal","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
		"components":[
			{"type":"AdvancedDataGrid","id":"dataGrid","dataField":"dataGrid","percentHeight":100,"percentWidth":100,"variableRowHeight":1,"stateGroup":"idle", 
			"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},
			"events":[
				{"type":"start", "action":"loadValues"},
				{"type":"change","action":"invoke","method":"loadValuesWithID","params":{"targetID":"edit"}}
			],
			"columns":[
				[!columns!]
			]},
			{"type":"VGroup","percentHeight":100,"percentWidth":100,
			"components":[
				{"type":"EditContainer","id":"edit","defaultButtonID":"ok",
				"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},
				"components":[
					{"type":"CollapsiblePanel","title":"[!O::getDescription()!]","minHeight":127,"setStyle":{"dropShadowVisible":0},
					"components":[
						{"type":"Form","setStyle":{"verticalGap":3,"paddingLeft":2,"paddingRight":2,"paddingTop":4,"paddingBottom":6},
						"components":[
							[!item:=0!]
							[STORPROC [!elements!]|categ]
								[STORPROC [!categ!]|media]
									[STORPROC [!media!]/hidden!=1|element]
										[SWITCH [!element::type!]|=]
											[CASE fkey]
												[MODULE Systeme/formKeys?P=[!element!]&O=[!O!]&item=[!item!]]
												[!item+=1!]
											[/CASE]
											[CASE rkey]
												[MODULE Systeme/formRKeys?P=[!element!]&O=[!O!]&item=[!item!]]
												[!item+=1!]
											[/CASE]
											[DEFAULT]
												[MODULE Systeme/formProperty?P=[!element!]&O=[!O!]&item=[!item!]&dico=0]
												[!item+=1!]
											[/DEFAULT]
										[/SWITCH]
									[/STORPROC]
								[/STORPROC]
							[/STORPROC]
						]}
					]}
				],
				"events":[
					{"type":"proxy","triggers":[
						{"trigger":"new","action":"invoke","method":"clearData"},
						{"trigger":"save","action":"invoke","method":"saveData"},
						{"trigger":"delete","action":"invoke","method":"deleteData","params":{"closeForm":0}},
						{"trigger":"cancel","action":"invoke","method":"cancelEdit"},
						{"trigger":"close","action":"invoke","objectID":"parentForm","method":"closeForm"}
					]}
				]}
			]}		
		]}
	]}
],
"popup":"modal",
"actions":[
	{"type":"close","action":"confirmUpdate"}
]}
}
