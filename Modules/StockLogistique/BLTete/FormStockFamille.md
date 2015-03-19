{"form":{"type":"VBox","id":"FormStockFamille","label":"Matériel sortie","percentHeight":100,
"setStyle":{"paddingLeft":5,"paddingRight":5,"verticalGap":2},"clipContent":0,
"localProxy":{
	"actions":{
		"searchGroup":{
			"action":"invoke","method":"callMethod",
			"params":{"method":"object","data":{"module":"StockLogistique","objectClass":"CommandeTete"},
			"function":"GetStockFamille","args":[{"dataValue":["Famille","Reference","Periode","Etat"]}]}
		}
	}
},
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
	"components":[
		{"type":"Group","layout":{"type":"HorizontalLayout","verticalAlign":"baseline","gap":4},"components":[
			{"type":"Label","text":"Famille"},
			{"type":"ComboBox","dataField":"Famille","width":150,"dataGroup":"searchGroup",
			"kobeyeClass":{"module":"StockLocatif","objectClass":"Famille","identifier":"Id","label":"Famille"},
			"events":[
				{"type":"init","action":"loadData"}
			]},
			{"type":"Spacer","width":4},
			{"type":"Label","text":"Référence"},
			{"type":"TextInput","dataField":"Reference","width":120,"dataGroup":"searchGroup"},
//			{"type":"ComboBox","dataField":"Reference","width":120,"dataGroup":"searchGroup",
//			"kobeyeClass":{"module":"StockLocatif","objectClass":"Reference","identifier":"Id","label":"Reference"},
//			"events":[
//				{"type":"init","action":"loadData"}
//			]},
			{"type":"Spacer","width":4},
			{"type":"Label","text":"Période"},
			{"type":"DateInterval","dataField":"Periode","dataGroup":"searchGroup"},
			{"type":"Spacer","width":4},
			{"type":"Label","text":"Etat"},
			{"type":"ComboBox","dataField":"Etat","defaultValue":0,"width":70,"dataGroup":"searchGroup",
			"dataProvider":[
				{"data":"0","label":""},
				{"data":"1","label":"Réservé"},
				{"data":"2","label":"Livré"},
				{"data":"3","label":"Repris"},
				{"data":"4","label":"Panne"}
			]}
		]},
		{"type":"AdvancedDataGrid","dataField":"list","percentHeight":100,"percentWidth":100,"rowHeight":20,"variableRowHeight":1,
		"columns":[
			{"type":"column","dataField":"Magasin","headerText":"Magasin","width":200},
			{"type":"column","dataField":"CodPostal","headerText":"CP","width":50},
			{"type":"column","dataField":"Ville","headerText":"Ville","width":120},
			{"type":"column","dataField":"Client","headerText":"Client","width":200},
			{"type":"column","dataField":"Reference","headerText":"Référence","width":100},
			{"type":"column","dataField":"Etat","headerText":"Etat","width":60},
			{"type":"column","dataField":"DateDebut","headerText":"Début","width":60,"format":"date"},
			{"type":"column","dataField":"DateFin","headerText":"Fin","width":60,"format":"date"},
			{"type":"column","width":0}
		]}
	]}
]}
}