[OBJ [!I::Module!]|[!I::TypeChild!]|O]
[!cat:=0!]
[STORPROC [!O::getElements()!]|categ]
	[IF [!Key!]!=hidden]
		[IF [!cat!]>0],[/IF]
		{"type":"CollapsiblePanel","title":"[!Key!]","layout":{"type":"HorizontalLayout"},"open":[IF [!Pos!]>2]0[ELSE]1[/IF],
		"components":[
			{"type":"VBox","width":150,"percentHeight":100,"setStyle":{"backgroundColor":"#dedede","paddingLeft":10,"paddingRight":10,"paddingTop":10,"paddingBottom":10},
			"components":[
				[!item:=0!][!form:=0!]
				[STORPROC [!categ!]|media]
					[STORPROC [!media!]|element]
						[IF [!element::hidden!]!=1]
							[IF [!element::type!]=image||[!element::type!]=file||[!element::type!]=video]
								[IF [!item!]],[/IF]
									{"type":"VBox","percentWidth":100,"setStyle":{"paddingLeft":0,"paddingRight":0,"paddingTop":10,"paddingBottom":10},
									"components":[
										{"type":"Label","text":"[IF [!element::description!]][!element::description!][ELSE][!element::name!][/IF]","percentWidth":100,
											"setStyle":{"fontWeight":"bold","color":"#000000"}
										},
										{"type":"ImageUpload","dataField":"[!element::name!]","percentWidth":100,"orientation":"vertical"}
									]}
								[!item+=1!]
							[/IF]
						[/IF]
					[/STORPROC]
				[/STORPROC]
				[IF [!form!]=1]]}[/IF]
			]},	
			{"type":"VBox","percentWidth":100,"percentHeight":100, "setStyle":{"paddingLeft":10,"paddingRight":10,"paddingTop":10,"paddingBottom":10},
			"components":[	
				[!item:=0!][!form:=0!]
				[STORPROC [!categ!]|media]
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
									[CASE rkey]
										[MODULE Systeme/formRKeys?P=[!element!]&O=[!O!]&item=[!item!]]
										[!item+=1!]
									[/CASE]
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
				[/STORPROC]
			]}
		]}
		[!cat+=1!]
	[/IF]
[/STORPROC]
