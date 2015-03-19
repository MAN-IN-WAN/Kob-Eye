[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
[!chldrn:=[!O::getChildTypes!]!]
[!firstField:=!]

[!mnuItm:=!][!mnuAct:=!][!chkbox:=!][!mnuLin:=1!]
[STORPROC [!O::getFunctions!]|mnu]
	[!acts:=[!mnu::actions!]!]
	[IF [!mnu::listOnly!]!=1]
		[IF [!mnu::type!]=vseparator]
			[!mnuItm+=,{"type":"vseparator"}!]
		[ELSE]
			[IF [!mnu::interface!]]
			[ELSE]
				[IF [!mnu::localProxy!]=1]
					[!mnuAct+=,[!acts::0!]!]
				[ELSE]
					[IF [!acts::1!]]
						[!mnuItm+=,{"label":"[!mnu::label!]","icon":"[!mnu::icon!]","data":"[!mnu::name!]"!]
						[IF [!mnu::stateGroup!]][!mnuItm+=,"stateGroup":"[!mnu::stateGroup!]"!][/IF]
						[!mnuItm+=}!]
						[!acts:=[!mnu::actions!]!]
						[IF [!acts::1!]][!mnuAct+=,"[!mnu::name!]":[!acts::1!]!][/IF]
					[/IF]
				[/IF]
			[/IF]
		[/IF]
		[IF [!mnu::menuLines!]][!mnuLin:=[!mnu::menuLines!]!][/IF]
	[/IF]
[/STORPROC]
[!intf:=[!O::getInterfaces!]!][!intf:=[!intf::Interface!]!][!intf:=[!intf::FormBase!]!]

{"form":{"type":"VBox","id":"[!I::TypeChild!]?","label":"[!O::getDescription()!]","percentHeight":100,
"setStyle":{"paddingLeft":0,"paddingRight":0,"verticalGap":0},"clipContent":0,
"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},
"localProxy":{
	"actions":{
		"proxy_kobeye_status":{"action":[
			{"action":"invoke","method":"groupState","params":{"group":"saved","property":"enabled","hasID":1}},
			{"action":"invoke","method":"groupState","params":{"group":"idle","property":"enabled","idle":1}},
			{"action":"invoke","method":"groupState","params":{"group":"updated","property":"enabled","updated":1}},
			{"action":"invoke","method":"groupState","params":{"group":"savedIdle","property":"enabled","hasID":1,"idle":1}}
		]}
		[!mnuAct!]
	}
},
"components":[
	{"type":"MenuTab","id":"menuList","maxLines":[!mnuLin!],"menuItems":[
		{"children":[
			[IF [!intf::showNew!]==1]{"label":"$__New__$","icon":"new","data":"new"},[/IF]
			{"label":"$__Save__$","icon":"save","data":"save","stateGroup":"updated"},
			{"label":"$__Save & Close__$","icon":"save","data":"saveClose","stateGroup":"updated"},
			{"label":"$__Close__$","icon":"close","data":"close"},
			{"type":"vseparator"},
			{"label":"$__Cancel__$","icon":"refresh","data":"cancel","stateGroup":"updated"},
			{"label":"$__Delete__$","icon":"iconDelete","data":"delete","stateGroup":"saved"}
			[!mnuItm!]
			,{"type":"vseparator"},
			{"label":"Edit name", "icon":"open", "data":"opendb","needFocus":1,"objectClass":["SubRange","SubProduct","SubModel"]},
			{"label":"Delete item from database", "icon":"remove", "data":"deletedb","needFocus":1,"objectClass":["SubRange","SubProduct","SubModel"]},
			{"label":"Update to current version", "icon":"open","needFocus":1, "data":"updatedb","objectClass":["SubRange","SubProduct","SubModel"]},
			{"type":"vseparator"},
			{"label":"Refresh database", "icon":"refresh", "data":"refreshdb"}
		]}
	]},
	{"type":"Box","percentWidth":100,"percentHeight":100,"minHeight":0,
	"components":[
		{"type":"EditContainer","percentHeight":100,"id":"edit",
		"components":[
			{"type":"VBox","percentWidth":100,"percentHeight":100,"verticalScrollPolicy":"auto","minWidth":0,"minHeight":0,
				"components":[
					[MODULE Systeme/formElements?I=[!I!]]
					,{"type":"CheckTree","id":"global_tree", "percentWidth":100, "percentHeight":100,
						"kobeyeClass":{
							"module":"Vitrine",
							"objectClass":"Categorie",
							"label":"Nom",
							"identifier":"Id",
							"icon":"products",
							"filters":"Affiche=0",
							"form":"FormBase.json",
							"children":["Categorie","Produit"]
						},
						"otherKobeyeClass":{
							"Produit":{"module":"Vitrine","objectClass":"Produit","identifier":"Id","label":"Nom", "iconField":"Image", "children":["Modele"]},
							"Modele":{"module":"Vitrine","objectClass":"Modele","identifier":"Id","label":"Nom", "iconField":"CodeBarre"}
						},
						"checkKobeyeClass":{
							"kobeyeClass":{
								"module":"Vitrine",
								"objectClass":"SubRange",
								"dirtyParent":true,
								"label":"Nom",
								"identifier":"Id",
								"icon":"products",
								"form":"FormDetail.json",
								"extra":{
									"linkObject":"Categorie",
									"linkField":"CategorieId",
									"versionField":"Version",
									"subVersionField":"SubVersion"
								},
								"children":["SubRange","SubProduct"]
							},
							"otherKobeyeClass":{
								"SubProduct":{"module":"Vitrine","objectClass":"SubProduct","extra":{"linkObject":"Produit","linkField":"ProduitId","versionField":"Version","subVersionField":"SubVersion"},"identifier":"Id","label":"Nom","form":"FormDetail.json", "iconField":"Image", "children":["SubModel"]},
								"SubModel":{"module":"Vitrine","objectClass":"SubModel","extra":{"linkObject":"Modele","linkField":"ModeleId","versionField":"Version","subVersionField":"SubVersion"},"identifier":"Id","label":"Nom","form":"FormDetail.json", "iconField":"CodeBarre"}
							}
						},
						"events":[
							{"type":"proxy","triggers":[
								{"trigger":"refreshdb", "action":"invoke", "method":"refreshData"},
								{"trigger":"opendb", "action":"invoke","method":"loadFormWithSelection","params":{"resetParent":1}},
								{"trigger":"deletedb", "action":"invoke", "method":"deleteFromSelection"}
							]},
							{"type":"check","action":"invoke","method":"callMethod","params":{"method":"object","module":"Vitrine","objectClass":"Database","dirtyId":1,"function":"addSub","args":[]}},
							{"type":"uncheck","action":"invoke","method":"callMethod","params":{"method":"object","module":"Vitrine","objectClass":"Database","dirtyId":1,"function":"removeSub","args":[]}}
						],
						"actions":[
							{"type":"init", "action":"loadData"}
						]
					}
				]
			}
		],
		"events":[
			{"type":"start","action":"loadValues","params":{"needsId":1}},
			{"type":"proxy","triggers":[
				{"trigger":"saveClose","action":"invoke","method":"saveData","params":{"closeForm":1}},
				{"trigger":"save","action":"invoke","method":"saveData","params":{"closeForm":0}},
				{"trigger":"close","action":"invoke","objectID":"parentForm","method":"closeForm"},
				{"trigger":"delete","action":"invoke","method":"deleteData"},
				{"trigger":"cancel","action":"invoke","method":"cancelEdit"},
				{"trigger":"new","action":[
					{"action":"invoke","method":"clearData"},
					{"action":"invoke","method":"restart"}
				]}
			]}
		]}
	]}
],
[IF [!firstField!]]"focusedID":"[!firstField!]",[/IF]
"actions":[
	{"type":"close", "action":"confirmUpdate"}
]}
}


