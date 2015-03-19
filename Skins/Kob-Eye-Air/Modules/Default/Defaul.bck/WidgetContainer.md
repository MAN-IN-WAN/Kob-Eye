{"type":"ChildrenGrid","dataField":"Container.ContainerShipmentId","percentWidth":100,"percentHeight":100,
"buttons":["add","delete"],
"kobeyeClass":{"dirtyParent":1,"objectClass":"Container"},
"columns":[
	{"type":"column","dataField":"Id","headerText":"ID","visible":0},
	{"type":"column","dataField":"ContainerNumber","headerText":"Container number","width":200},
	{"type":"column","dataField":"Volume","headerText":"Volume","width":100},
	{"type":"column","visible":0}
],
"events":[
	{"type":"start","action":"loadValues","params":{"needsParentId":1}}
]}
