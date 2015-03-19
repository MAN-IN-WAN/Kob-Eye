{"form":{"type":"MDIWindow","id":"TaskCalendar","title":"Tasks calendar","popup":"free",
"localProxy":1,
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalGap":5,"paddingTop":5,"paddingLeft":5,"paddingRight":5}, 
	"components":[
		{"type":"MenuTab","maxLines":1,"id":"menuList",
		"menuItems":[
			{"children":[
				{"label":"$__Open__$","icon":"open","data":"open","needFocus":1},
				{"label":"$__New__$","icon":"iconNew","data":"new"},
				{"label":"$__Delete__$","icon":"iconDelete","data":"delete","needFocus":1},
				{"label":"$__Reach__$","icon":"right","data":"reach","needFocus":1},
				{"label":"$__Refresh__$","icon":"refresh", "data":"refresh"}
			]}
		]},
		{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"paddingTop":0,"horizontalGap":5},
		"components":[
			{"type":"Calendar","id":"calendar","dataField":"calendar","percentHeight":100,"percentWidth":100,
			"kobeyeClass":{"module":"Systeme","objectClass":"AlertTask","form":"FormTask.json","filters":""},
			"otherKobeyeClass":{"module":"Systeme","objectClass":"TaskType","identifier":"Id","label":"Type"},
			"dataFilter":"TypeId#",
			"getDataFunction":{"method":"object","function":"GetTaskCalendar"},
			"events":[
				{"type":"start", "action":"loadValues"},
				{"type":"click","action":"invoke","method":"loadFormWithSelection"},
				{"type":"proxy", "triggers":[
					{"trigger":"new","action":"invoke","method":"createForm"},
					{"trigger":"searchGroup","action":"invoke","method":"filterData","params":{"group":"searchGroup"}},
					{"trigger":"clear","action":"invoke","method":"filterData"},
					{"trigger":"reach","action":"invoke","method":"findFormWithItem","params":{"field":"Tag"}},
					{"trigger":"refresh", "action":"invoke", "method":"restart"}
				]}
			]}
		]}
	]}
]}
}
