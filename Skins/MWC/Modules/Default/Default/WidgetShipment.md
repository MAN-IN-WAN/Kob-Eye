{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"verticalGap":0},
"localProxy":{
	"actions":{
		"proxy_kobeye_status":{"action":"invoke","method":"groupState","params":{"group":"selection","property":"enabled","selection":1}}
	}
},
"components":[
	{"type":"HBox","percentWidth":100,"setStyle":{"gap":1,"paddingLeft":4,"paddingTop":4,"paddingBottom":4,"backgroundColor":"#d9d9d9"},
	"components":[
		{"type":"ImageButton","id":"edit:Shipment","width":16,"height":16,"cornerRadius":8,"image":"mwc_i","borderWidth":1,"stateGroup":"selection","enabled":0},
		{"type":"ImageButton","id":"new:Shipment","width":16,"height":16,"cornerRadius":8,"image":"mwc_plus","borderWidth":1},
		{"type":"ImageButton","id":"delete:Shipment","width":16,"height":16,"cornerRadius":8,"image":"mwc_moins","borderWidth":1,"stateGroup":"selection","enabled":0}
	]},
	{"type":"AdvancedDataGrid","id":"dataGrid","dataField":"dataGrid","minHeight":450,"minWidth":490,"percentWidth":100,"percentHeight":100,
	"kobeyeClass":{"dirtyParent":1,"module":"Murphy","objectClass":"Shipment","form":"FormBase.json"},"changeEvent":1,
	"events":[
		{"type":"start", "action":"loadValues"},
		{"type":"dblclick","action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav"}},
		{"type":"proxy", "triggers":[
			{"trigger":"new:Shipment","action":"invoke","method":"createForm","params":{"containerID":"tabNav"}},
			{"trigger":"edit:Shipment","action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav"}},
			{"trigger":"delete:Shipment","action":"invoke","method":"deleteWithID"}
		]}
	],
	"columns":[
		{"type":"column","dataField":"Id","headerText":"ID","visible":0},
		{"type":"column","dataField":"Retained","headerText":"B","format":"boolean","width":20,"extra":"iconRed"},
		{"type":"column","dataField":"LoadingDate","headerText":"Loading","format":"date","width":60},
		{"type":"column","dataField":"DeliveryDate","headerText":"Delivery","format":"date","width":60},
		{"type":"column","dataField":"Volume","headerText":"Volume","format":"0dec","width":80},
		{"type":"column","dataField":"SupplierContract","headerText":"Supl contract","format":"","width":100},
		{"type":"column","dataField":"SupplierInvoice","headerText":"Supl invoice","format":"","width":100},
		{"type":"column","dataField":"Supplier","headerText":"Supplier","format":"","width":150},
		{"type":"column","dataField":"Buyer","headerText":"Buyer","format":"","width":150},
		{"type":"column","dataField":"Varietal","headerText":"Varietal","format":"","width":100},
		{"type":"column","width":0}
	]}
]}