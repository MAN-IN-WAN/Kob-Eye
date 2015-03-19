{"form":
{"type":"VBox","id":"FL:Devis/DevisEcheance","label":"Facturation des Contrats","percentWidth":100,"percentHeight":100, 
"setStyle":{"paddingTop":0,"paddingBottom":0,"paddingLeft":0,"paddingRight":0,"verticalGap":0},"localProxy":1, 
"components":[
	{"type":"MenuTab","maxLines":1,"id":"menuList",
	"menuItems":[
		{"children":[
			{"label":"Facturer","icon":"invoice","data":"ContractInvoices"},
			{"label":"Rafraîchir","icon":"refresh","data":"refresh"},
			{"label":"Cocher la selection","data":"checkSel","icon":"select"},
			{"label":"Décocher la selection","data":"uncheckSel","icon":"unselect"},
			{"label":"Décocher tout","data":"uncheckAll","icon":"unselect"}
		]}
	],
	"actions":[
		{"type":"itemClick", "actions":{
			"delete":{"action":"invoke", "method":"deleteFromSelection"},
			"ContractInvoices":{"action":"invoke","method":"callMethod","params":{
				"interface":1,
				"method":"object","function":"ContractInvoices",
				"data":{"module":"Devis","objectClass":"DevisEcheance","form":"Functions/ContractInvoices.json"},
				"selectionRequired":1,"args":[{"selectedValues":["dataGrid"]},{"interfaceValues":["Date","Force"]}]}}
			}
		}
	]},
	{"type":"HBox","id":"listBox","label":"Liste","percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0},
	"components":[
		{"type":"EditContainer","id":"searchBox","width":180,"percentHeight":100,
		"components":[
			{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"paddingTop":10,"verticalGap":5,"paddingLeft":4,"paddingRight":0},
			"components":[
				{"type":"LabelItem","label":"Client","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":2},"components":[
					{"type":"TextInput","dataField":"ClientIntitule","percentWidth":100,"dataGroup":"searchGroup"}
				]},
				{"type":"LabelItem","label":"Magasin","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":2},"components":[
					{"type":"TextInput","dataField":"LivraisonIntitule","percentWidth":100,"dataGroup":"searchGroup"}
				]},
				{"type":"LabelItem","label":"Date échéance","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":2},"components":[
					{"type":"DateInterval","dataField":"Echeance","defaultValue":"Month","dataGroup":"searchGroup"}
				]},
				{"type":"CheckBox3","label":"Facturé","allow3StateForUser":1,"dataField":"Facture","defaultValue":"0","dataGroup":"searchGroup"},
				{"type":"HBox","percentWidth":100,"setStyle":{"paddingTop":4},"components":[
					{"type":"Spacer","percentWidth":100},
					{"type":"Button","label":"$__Clear__$","id":"clear","width":70}
				]}
			]}
		],
		"events":[
			{"type":"proxy", "triggers":[
				{"trigger":"clear","action":"invoke","method":"clearData"}
			]}
		]},
		{"type":"AdvancedDataGrid","id":"dataGrid","dataField":"dataGrid" ,"checkBoxes":1,"allowMultipleSelection":1,"percentHeight":100,"percentWidth":100,"rowHeight":20,"variableRowHeight":1,
		"kobeyeClass":{"module":"Devis","objectClass":"DevisEcheance","setFilter":"Societe=B"},
		"events":[
			{"type":"start", "action":"loadValues"},
			{"type":"dblclick","action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav","label":"DevisEcheance"}},
			{"type":"proxy", "triggers":[
				{"trigger":"searchGroup","action":"invoke","method":"filterData","params":{"group":"searchGroup"}},
				{"trigger":"refresh", "action":"invoke", "method":"filterData","params":{"group":"searchGroup"}},
				{"trigger":"checkSel","action":"invoke","method":"checkSelected"},
				{"trigger":"uncheckSel","action":"invoke","method":"uncheckSelected"},
				{"trigger":"uncheckAll","action":"invoke","method":"uncheckAll"}
			]}
		],
		"columns":[
			{"type":"column","dataField":"Id","headerText":"ID","visible":0},
			{"type":"column","dataField":"Reference","headerText":"Devis","format":"","width":60},
			{"type":"column","dataField":"NombreEcheance","headerText":"Ech","width":35,"setStyle":{"textAlign":"right"}},
			{"type":"column","dataField":"Numero","headerText":"N°","width":24,"setStyle":{"textAlign":"right"}},
			{"type":"column","dataField":"Echeance","headerText":"Echéance","format":"longDate","width":74},
			{"type":"column","dataField":"Facture","headerText":"F","format":"boolean","width":24},
			{"type":"column","dataField":"ClientIntitule","headerText":"Intitulé client","width":150},
			{"type":"column","dataField":"LivraisonIntitule","headerText":"Intitulé livraison","width":150},
			{"type":"column","dataField":"DateDebut","headerText":"Début","format":"longDate","width":74},
			{"type":"column","dataField":"DateFin","headerText":"Fin","format":"longDate","width":74},
			{"type":"column","dataField":"Initiales","headerText":"Commercial","width":80},
			{"type":"column","width":0}
		]}
	]}
]}
}
