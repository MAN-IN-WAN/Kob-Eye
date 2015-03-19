[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
[!O::setView()!]
{"form":{"type":"TitleWindow","id":"PL:[!I::Module!]/[!I::TypeChild!]","title":"Sélection [!O::getDescription()!]",
"minWidth":550,"height":400,"minHeight":200,
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
	"localProxy":1,  //**** local proxy dans le container en dessous de from pour que formValue decleche de proxy de haut dessus
	"components":[
// formulaire de recherche
		{"type":"EditContainer","id":"searchBox", 
		"components":[
			{"type":"HBox", "percentWidth":100,
			"components":[
				{"type":"TextInput","dataField":"filterField","id":"filterField","filterMode":"generic","percentWidth":50},
				{"type":"Button","id":"clear","label":"$__Clear__$","width":80}
			]}
		],
		"events":[
			{"type":"proxy", "triggers":[
				{"trigger":"clear","action":"invoke","method":"clearData"}
			]}
		]},
		{"type":"Box", "percentWidth":100, "percentHeight":100,
		"components":[
			[IF [!O::isRecursiv!]]
				{"type":"Tree", "id":"dataGrid", "dataField":"dataGrid", "percentWidth":100, "percentHeight":100,
				"kobeyeClass":{
					"module":"[!I::Module!]",
					"objectClass":"[!I::TypeChild!]",
					"select":"[!select!]",
					"label":"Nom",
					"identifier":"Id",
					"icon":"[!O::getIcon()!]",
					"children":["[!I::TypeChild!]"]
				},
				"events":[
					{"type":"init","action":"loadData"},
					{"type":"dblclick","action":[
						{"action":"invoke","method":"formValue","params":{"property":"idValue"}},
						{"action":"invoke","objectID":"parentForm","method":"closeForm"}
					]},
					{"type":"proxy", "triggers":[
						{"trigger":"filterField","action":"invoke","method":"filterData","params":{"filter":"filterField"}},
						{"trigger":"clear","action":"invoke","method":"filterData"},
						{"trigger":"ok","action":[
							{"action":"invoke","method":"formValue","params":{"property":"idValue"}},
							{"action":"invoke","objectID":"parentForm","method":"closeForm"}
						]}
					]}
				]}
			[ELSE]
				[!select:=Id!]
				[!columns:={"type":"column","dataField":"Id","headerText":"ID","visible":0}!]
				[!EL:=[!O::getElementsByAttribute(list,,1)!]!]
				[IF [!EL!]=][!EL:=[!O::getSearchOrder()!]!][/IF]
				[STORPROC [!EL!]|P]
					[ORDER list|ASC]
						[!select+=,[!P::name!]!]
						[!columns+=,{"type":"column","dataField":"[!P::name!]","headerText":!]
						[IF [!P::listDescr!]][!columns+="[!P::listDescr!]"!][ELSE][IF [!P::description!]][!columns+="[!P::description!]"!][ELSE][!columns+="[!P::name!]"!][/IF][/IF]
						[SWITCH [!P::type!]|=]
							[CASE date]
								[!cf:=date!][!cw:=60!]
							[/CASE]
							[CASE time]
								[!cf:=time!][!cw:=90!]
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
							[CASE float]
								[!cw:=80!]
							[/CASE]
							[CASE int]
								[!cf:=0dec!][!cw:=50!]
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
				[!columns+=,{"type":"column","width":0}!]
				{"type":"AdvancedDataGrid","id":"dataGrid","dataField":"dataGrid","percentWidth":100,"percentHeight":100,"rowHeight":24,"variableRowHeight":0,
				"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},        //,"select":"[!select!]"},
				"dataFilter":"parentSetFilter",
				"events":[
					{"type":"start","action":"loadValues"},
					{"type":"dblclick","action":[
						{"action":"invoke","method":"formValue","params":{"property":"idValue"}},
						{"action":"invoke","objectID":"parentForm","method":"closeForm"}
					]},
					{"type":"proxy", "triggers":[
						{"trigger":"filterField","action":"invoke","method":"filterData","params":{"filter":"filterField"}},
						{"trigger":"clear","action":"invoke","method":"filterData"},
						{"trigger":"ok","action":[
							{"action":"invoke","method":"formValue","params":{"property":"idValue"}},
							{"action":"invoke","objectID":"parentForm","method":"closeForm"}
						]}
					]}
				],
				"columns":[
					[!columns!]
				]}
			[/IF]
		]},
// boutons valider, annuler
		{"type":"HBox",
		"components":[
			//{"type":"Spacer"},
			{"type":"Button", "id":"ok", "label":"Valider", "width":80},
			{"type":"Button", "id":"cancel", "label":"Annuler", "width":80,
			"events":[
				{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
			]}
		]}		
	]}
],
"popup":"modal"
}}
