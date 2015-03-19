{"form":{"type":"VBox","id":"FormStockFamille","label":"Matériel sortie","percentHeight":100,
"setStyle":{"verticalGap":5,"paddingTop":5,"paddingLeft":5,"paddingRight":5},"clipContent":0,
"localProxy":{
	"actions":{
		"searchGroup":{
			"action":"invoke","method":"callMethod","params":{
			"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},
			"function":"GetStockFamille","args":"dv:Famille,dv:Reference,dv:Periode"  //,dv:Livre" //,"Etat"
			}
		},
		"panne":{
			"action":"invoke","method":"callMethod","params":{
			"interface":1,
			"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete","form":"Functions/EchangePanne.json"},
			"function":"EchangePanne","args":"idv:items,iv:Date"
			}
		},
		"stock":{
			"action":"invoke","method":"callMethod","params":{
			"interface":1,
			"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete","form":"Functions/RemiseEnStock.json"},
			"function":"RemiseEnStock","args":"idv:items,iv:Date,iv:Controle"
			}
		}
	}
},
"components":[
	{"type":"MenuTab","maxLines":1,"id":"menuList",
	"menuItems":[
		{"children":[
			{"label":"Echange Panne","icon":"back","data":"panne","needFocus":1,"compare":"Etat=2"},
			{"label":"Remise en Stock","icon":"addlevel","data":"stock","needFocus":1,"compare":"Etat=2"},
			{"label":"$__Refresh__$","icon":"refresh", "data":"searchGroup"}
		]}
	]},
	{"type":"HBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0,"horizontalGap":4},
	"components":[
		{"type":"EditContainer","id":"searchBox","dataField":"searchBox","width":180,"percentHeight":100,
		"components":[
			{"type":"CollapsiblePanel","dividerVisible":0,"titleHeight":0,"setStyle":{"backgroundColor":"#d9d9d9","color":"black"},"open":1,"title":"Stock Famille",
			"components":[
				{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"paddingTop":2,"paddingLeft":4,"paddingRight":4,"paddingBottom":4,"verticalGap":6},
				"components":[
					{"type":"LabelItem","label":"Famille","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":0},
					"components":[
						{"type":"ComboBox","dataField":"Famille","percentWidth":100,"dataGroup":"searchGroup",
						"kobeyeClass":{"module":"StockLocatif","objectClass":"Famille","identifier":"Id","label":"Famille"},
						"events":[
							{"type":"init","action":"loadData"}
						]}
					]},
					{"type":"LabelItem","label":"Référence","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":0},
					"components":[
						{"type":"TextInput","dataField":"Reference","percentWidth":100,"dataGroup":"searchGroup"}
					]},
					{"type":"LabelItem","label":"Période","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":0},
					"components":[
						{"type":"DateInterval","dataField":"Periode","dataGroup":"searchGroup"}
					]},
					//{"type":"CheckBox3","label":"Livré","allow3StateForUser":1,"dataField":"Livre","dataGroup":"searchGroup"},
					{"type":"Spacer","height":30}
				]}
			]}
		]},
		{"type":"AdvancedDataGrid","dataField":"items","percentHeight":100,"percentWidth":100,"rowHeight":20,"variableRowHeight":1,
		"kobeyeClass":{"module":"StockLogistique","objectClass":"Element"},
		"columns":[
			{"type":"column","dataField":"Livraison","headerText":"Magasin","width":140},
			{"type":"column","dataField":"CodPostal","headerText":"CP","width":50},
			{"type":"column","dataField":"Ville","headerText":"Ville","width":100},
			{"type":"column","dataField":"Client","headerText":"Client","width":140},
			{"type":"column","dataField":"Famille","headerText":"Famille","width":100},
			{"type":"column","dataField":"Quantite","headerText":"Qté","width":40,"format":"0dec"},
			{"type":"column","dataField":"Reference","headerText":"Référence","width":120},
			{"type":"column","dataField":"Status","headerText":"Etat","width":80},
			{"type":"column","dataField":"Echange","headerText":"X","width":20,"format":"boolean"},
			{"type":"column","dataField":"DateDebut","headerText":"Début","width":60,"format":"date"},
			{"type":"column","dataField":"DateFin","headerText":"Fin","width":60,"format":"date"},
			{"type":"column","dataField":"Devis","headerText":"Devis","width":60},
			{"type":"column","width":0}
		],
		"contextMenu":[
			{"label":"Echange Panne","data":"panne","icon":"back","compare":"Etat=2"},
			{"label":"Remise en Stock","data":"stock","icon":"addlevel","compare":"Etat=2"}
		]}
	]}
]}
}