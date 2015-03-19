[OBJ [!I::Module!]|[!I::TypeChild!]|O]
[!cat:=0!]
[STORPROC [!O::getElements()!]|categ]
	[IF [!Key!]!=hidden]
		[IF [!cat!]>0],[/IF]
		{"type":"[IF [!Pos!]>2]Collapsible[/IF]Panel","title":"[!Key!]","layout":{"type":"HorizontalLayout"}[IF [!Pos!]>2],"open":0[/IF],"titleHeight":20,"dividerVisible":0,"setStyle":{"backgroundColor":"#d9d9d9","color":"black"},
		"components":[
			{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalGap":0,"paddingLeft":10,"paddingRight":10,"paddingTop":0,"paddingBottom":6},
			"components":[	
				[!item:=0!][!form:=0!]
				[STORPROC [!categ!]|media]
					[LIMIT 0|100]
						[STORPROC [!media!]|element]
							[IF [!element::hidden!]!=1&&[!element::hideParent!]!=1]
								[IF [!element::description!]][!desc:=[!element::description!]!][ELSE][!desc:=[!element::name!]!][/IF]
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
									[CASE image]
									{"type":"VBox","percentWidth":100,"setStyle":{"paddingLeft":0,"paddingRight":0,"paddingTop":10,"paddingBottom":10},
									"components":[
										{"type":"Label","text":"[!desc!]","percentWidth":100,
											"setStyle":{"fontWeight":"bold","color":"#000000"}
										},
										{"type":"ImageUpload","dataField":"[!element::name!]","percentWidth":100,"orientation":"vertical"}
									]}
									[/CASE]
									[CASE video][/CASE]
//									[CASE file][/CASE]
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
