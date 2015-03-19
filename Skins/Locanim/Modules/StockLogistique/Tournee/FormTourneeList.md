{"form":{"type":"VBox","id":"StockLogistique:TourneeList","label":"Feuilles de Route","percentHeight":100,
"setStyle":{"verticalGap":5,"paddingTop":5,"paddingLeft":5,"paddingRight":5},"clipContent":0,
"localProxy":1,
"components":[
	{"type":"MenuTab","id":"menuList","maxLines":1,"searchBox":1,"dataField":"filterField","id":"filterField","filterFields":["Reference","Date","Etat","ChauffeurId","VehiculeId"],
	"menuItems":[
		{"children":[
			{"label":"Ouvrir", "icon":"open", "data":"open"},
			{"label":"Rafraîchir", "icon":"refresh", "data":"refresh"}
			//,{"label":"Imprimer", "icon":"print", "data":"printTournee"}
		]}
//	],
//	"actions":[
//		{"type":"itemClick","actions":{
//			"printTournee":{"action":"invoke","method":"callMethod","params":{
//				"interface":1,
//				"method":"object","data":{"module":"StockLogistique","objectClass":"Tournee","form":"Functions/printTournee.json"},
//				"function":"PrintDocuments","selectionRequired":1,"args":[{"selectedValues":["dataGrid"]},{"interfaceValues":["Tournee","BL","Fond"]}]}
//			}
//		}}
	]},

	{"type":"HBox","id":"listBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0},
	"components":[
		{"type":"EditContainer", "id":"searchBox", "width":180, "percentHeight":100,
		"components":[
			{"type":"CollapsiblePanel","dividerVisible":0,"titleHeight":0,"setStyle":{"backgroundColor":"#d9d9d9","color":"black"},"open":1,"title":"Tournées",
			"components":[
				{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"paddingTop":2,"verticalGap":2,"paddingLeft":4,"paddingRight":4,"paddingBottom":4},
				"components":[
					{"type":"LabelItem","label":"N° Tournée","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":0},"components":[
						{"type":"TextInput","dataField":"Reference","dataGroup":"searchGroup","percentWidth":100,"filterMode":"0"}
					]},
					{"type":"LabelItem","label":"Date","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":0},"components":[
						{"type":"DateInterval","dataField":"Date","dataGroup":"searchGroup","percentWidth":100}
					]},
					{"type":"LabelItem","label":"Etat","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":0},"components":[
						{"type":"ComboBox","dataField":"Etat","percentWidth":100,"dataGroup":"searchGroup",
						"kobeyeClass":{"module":"StockLogistique","objectClass":"Status","identifier":"Code","label":"Etat",
						"query":"StockLogistique/Status/Type=T"},				
						"actions":[
							{"type":"init","action":"loadData"}
						]}
					]},
					{"type":"LabelItem","label":"Chauffeur","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":0},"components":[
						{"type":"ComboBox","dataField":"ChauffeurId","percentWidth":100,"dataGroup":"searchGroup",
						"kobeyeClass":{"module":"Repertoire","objectClass":"Tiers","identifier":"Id","label":"Intitule","query":"Repertoire/Tiers/Livreur=1"},				
						"actions":[
							{"type":"init","action":"loadData"}
						]}
					]},
					{"type":"LabelItem","label":"Véhicule","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":0},"components":[
						{"type":"ComboBox","dataField":"VehiculeId","percentWidth":100,"dataGroup":"searchGroup",
						"kobeyeClass":{"module":"StockLogistique","objectClass":"Vehicule","identifier":"Id","label":"Designation","query":"StockLogistique/Vehicule"},
						"actions":[
							{"type":"init","action":"loadData"}
						]}
					]},
					{"type":"HBox","percentWidth":100,"setStyle":{"paddingTop":4},"components":[
						{"type":"Spacer","percentWidth":100},
						{"type":"Button","label":"Effacer","id":"clear","width":80}
					]}
				]}
			]}
		],
		"events":[
			{"type":"proxy", "triggers":[
				{"trigger":"clear","action":"invoke","method":"clearData"}
			]}
		]},
		{"type":"AdvancedDataGrid","id":"dataGrid","dataField":"dataGrid","percentHeight":100,"percentWidth":100,"rowHeight":20,"variableRowHeight":1, 
		"kobeyeClass":{"module":"StockLogistique","objectClass":"Tournee","form":"FormTournee.json"},"checkBoxes":0,
		"events":[
			{"type":"start", "action":"loadValues"},
			{"type":"dblclick","action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav"}},
			{"type":"proxy","triggers":[
				{"trigger":"searchGroup","action":"invoke","method":"filterData","params":{"group":"searchGroup"}},
				{"trigger":"save","action":"invoke","method":"callMethod",
				"params":{"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},
				"function":"SaveDestockage","args":[{"dataValue":["dataGrid"]}]}},
				{"trigger":"refresh","action":"invoke","method":"restart"},
				{"trigger":"open","action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav"}}
			]}
		],
		"columns":[
			{"type":"column","dataField":"Id","headerText":"ID","visible":0},
			{"type":"column","dataField":"Date","headerText":"Date","format":"date","width":60},
			{"type":"column","dataField":"Reference","headerText":"Tournée","width":60},
			{"type":"column","dataField":"Status","headerText":"Etat","width":80},
			{"type":"column","dataField":"ChauffeurId","headerText":"Chauffeur","width":150},
			{"type":"column","dataField":"VehiculeId","headerText":"Véhicule","width":150},
			{"type":"column","width":0}
		]}
	]}
],
"actions":[
]}
}


