[INFO [!Query!]|I]
[OBJ [!Int::module!]|[!Int::objectClass!]|O]
[!container:="containerID":"tabNav"!]

{"type":"Tree","id":"tree_range", "percentWidth":100, "percentHeight":100,"drag":0,"need_id":1,
	[SWITCH [!Int::objectClass!]|=]
		[CASE Third]
			"kobeyeClass":{
				"module":"Murphy",
				"objectClass":"Third",
				"view":"ThirdList",
				"label":"Reference",
				"identifier":"Id",
				"select":"Reference,Company,Acronym,Id",
				"icon":"icon_tiers",
				"dirtyId":1,
				"form":"FormBase.json",
				"children":["Enquiry"],
				"columns":[
					{"field":"Type","type":"varchar","value":"Third","width":100},
					{"field":"Acronym","type":"image","width":85},
					{"field":"Company","type":"varchar","width":100}
				]
			},
			"otherKobeyeClass":{
				"Enquiry":{"module":"Murphy","objectClass":"Enquiry","identifier":"Id","label":"Reference","form":"FormBase.json", "icon":"icon_enquiry", "children":["Proposal","Contract"],"select":"Reference,Date,StatusIcon,StatusIcon_ToolTip,Id",
					"columns":[
						{"field":"Type","type":"varchar","value":"Enquiry","width":100},
						{"field":"Date","type":"date","width":65},
						{"field":"StatusIcon","type":"image","width":20}
					]
				},
				"Proposal":{"module":"Murphy","objectClass":"Proposal","identifier":"Id","label":"Supplier","form":"FormBase.json", "icon":"icon_proposal","select":"Supplier,Date,StatusIcon,Id",
					"columns":[
						{"field":"Type","type":"varchar","value":"Offer request","width":100},
						{"field":"Date","type":"date","width":65},
						{"field":"StatusIcon","type":"image","width":20}
					]
				},
				"Contract":{"module":"Murphy","objectClass":"Contract","identifier":"Id","label":"Reference","form":"FormBase.json", "icon":"icon_contract", "children":["Shipment"],"select":"Reference,Date,StatusIcon,StatusIcon_ToolTip,Id",
					"columns":[
						{"field":"Type","type":"varchar","value":"Contract","width":100},
						{"field":"Date","type":"date","width":65},
						{"field":"StatusIcon","type":"image","width":20}
					]
				},
				"Shipment":{"module":"Murphy","objectClass":"Shipment","identifier":"Id","label":"Supplier","form":"FormBase.json", "icon":"icon_shipment","select":"Supplier,LoadingDate,Volume,StatusIcon,StatusIcon_ToolTip,Id",
					"columns":[
						{"field":"Type","type":"varchar","value":"Shipment","width":100},
						{"field":"LoadingDate","type":"date","width":65},
						{"field":"StatusIcon","type":"image","width":20}
						//,{"field":"Volume","type":"3dec","width":100}
					]
				}
			},
		[/CASE]
		[CASE Enquiry]
			"kobeyeClass":{
				"module":"Murphy",
				"objectClass":"Enquiry",
				"label":"Reference",
				"identifier":"Id",
				"select":"Reference,Date,StatusIcon,StatusIcon_ToolTip,Id",
				"icon":"icon_enquiry",
				"dirtyId":1,
				"form":"FormBase.json",
				"view":"EnquiryList",
				"children":["Proposal","Contract"],
				"columns":[
					{"field":"Type","type":"varchar","value":"Enquiry","width":100},
					{"field":"Date","type":"date","width":65},
					{"field":"StatusIcon","type":"image","width":20}
				]
			},
			"otherKobeyeClass":{
				"Proposal":{"module":"Murphy","objectClass":"Proposal","identifier":"Id","label":"Supplier","form":"FormBase.json", "icon":"icon_proposal","select":"Supplier,Date,StatusIcon,StatusIcon_ToolTip,Id",
					"columns":[
						{"field":"Type","type":"varchar","value":"Offer request","width":100},
						{"field":"Date","type":"date","width":65},
						{"field":"StatusIcon","type":"image","width":20}
					]
				},
				"Contract":{"module":"Murphy","objectClass":"Contract","identifier":"Id","label":"Reference","form":"FormBase.json", "icon":"icon_contract", "children":["Shipment"],"select":"Reference,Date,StatusIcon,StatusIcon_ToolTip,Id",
					"columns":[
						{"field":"Type","type":"varchar","value":"Contract","width":100},
						{"field":"Date","type":"date","width":65},
						{"field":"StatusIcon","type":"image","width":20}
					]
				},
				"Shipment":{"module":"Murphy","objectClass":"Shipment","identifier":"Id","label":"Supplier","form":"FormBase.json", "icon":"icon_shipment","select":"Supplier,LoadingDate,Volume,StatusIcon,StatusIcon_ToolTip,Id",
					"columns":[
						{"field":"Type","type":"varchar","value":"Shipment","width":100},
						{"field":"LoadingDate","type":"date","width":65},
						{"field":"StatusIcon","type":"image","width":20}
						//,{"field":"Volume","type":"3dec","width":100}
					]
				}
			},
		[/CASE]
		[CASE Contract]
			"kobeyeClass":{
				"module":"Murphy",
				"objectClass":"Contract",
				"label":"Reference",
				"identifier":"Id",
				"icon":"icon_contract",
				"dirtyId":1,
				"view":"ContractList",
				"form":"FormBase.json",
				"children":["Shipment"],
				"select":"Reference,Date,StatusIcon,StatusIcon_ToolTip,Id",
				"columns":[
					{"field":"Type","type":"varchar","value":"Contract","width":100},
					{"field":"Date","type":"date","width":65},
					{"field":"StatusIcon","type":"image","width":20}
				]
			},
			"otherKobeyeClass":{
				"Shipment":{"module":"Murphy","objectClass":"Shipment","identifier":"Id","label":"Supplier","form":"FormBase.json", "icon":"icon_shipment","select":"Supplier,LoadingDate,Volume,StatusIcon,StatusIcon_ToolTip,Id",
					"columns":[
						{"field":"Type","type":"varchar","value":"Shipment","width":100},
						{"field":"LoadingDate","type":"date","width":65},
						{"field":"StatusIcon","type":"image","width":20}
						//,{"field":"Volume","type":"3dec","width":100}
					]
				}
			},
		[/CASE]
	[/SWITCH]
	"events":[
		{"type":"dblclick","action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav"}},
		{"type":"proxy","triggers":[
			{"trigger":"refresh", "action":"invoke", "method":"loadData"}
		]}
	],
	"actions":[
		{"type":"init", "action":"loadData"}
	]
}
