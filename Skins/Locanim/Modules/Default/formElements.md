[OBJ [!I::Module!]|[!I::TypeChild!]|O]
[!cat:=0!]
[STORPROC [!O::getElements()!]|categ]
	[IF [!Key!]!=hidden]
		[IF [!cat!]>0],[/IF]
		{"type":"Panel","title":"[!Key!]", "layout":{"type":"HorizontalLayout"},"titleHeight":20,"setStyle":{"dropShadowVisible":0},
		"components":[
			{"type":"VBox","percentWidth":100,"percentHeight":100, "setStyle":{"verticalGap":2,"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
			"components":[	
				[!item:=0!][!form:=0!]
				[STORPROC [!categ!]|media]
	//				{"type":"Form","setStyle":{"verticalGap":1,"paddingLeft":1,"paddingRight":1,"paddingTop":0,"paddingBottom":1},"percentWidth":100,"components":[
					[LIMIT 0|100]
						[STORPROC [!media!]|element]
							[IF [!element::hidden!]!=1&&[!element::hideParent!]!=1]
								[SWITCH [!element::type!]|=]
									[CASE fkey]
										//[IF [!element::card!]=short]
										//	[MODULE Systeme/formParentData?P=[!element!]&O=[!O!]&item=[!item!]]
										//	[!item+=1!]
										//[ELSE]
											[MODULE Systeme/formKeys?P=[!element!]&O=[!O!]&item=[!item!]]
											[!item+=1!]
										//[/IF]
									[/CASE]
									[CASE rkey][/CASE]
									[CASE image][/CASE]
									[CASE video][/CASE]
									[CASE file][/CASE]
									[DEFAULT]
										[MODULE Systeme/formProperty?P=[!element!]&O=[!O!]&item=[!item!]]
										[!item+=1!]
									[/DEFAULT]
								[/SWITCH]
							[/IF]
						[/STORPROC]
					[/LIMIT]
	//				]}
				[/STORPROC]
			]}
		]}
		[!cat+=1!]
	[/IF]
[/STORPROC]
