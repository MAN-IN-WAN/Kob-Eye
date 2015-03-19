{
	layout: "border",
	xtype: "container",
	height: "100%",
	border: false,
	defaults: {
		padding: "5"
	},
	items: [
		{
			xtype: "toolbar",
			defaultType: "button",
			region: "north",
			height: "25",
			items: [
				{
					text: "Edit",
					listeners: {
						"click": function( button ) {
							openTab('/[!Obj::Query!]/Modifier.htm', 'Modifier [!Query!]', button.ownerCt.ownerCt);
						}
					}
				},{
					text: "Delete",
					listeners: {
						"click": function( button ) {
							Ext.MessageBox.confirm('Confirmation', 'Voulez-vous vraiment supprimer cet élément ?', function(btn) {
								if(btn == 'yes') {
									Ext.Ajax.request({
										url: '/[!Obj::Query!]/Supprimer.htm',
										success: function(xhr) {
											Ext.MessageBox.alert("Status", "L'élément a bien été supprimé.");
											button.ownerCt.ownerCt.ownerCt.destroy();
										}
									});
								}
							});
						}
					}
				},{
					text: "Close",
					listeners: {
						"click": function( button, e ) {
							button.ownerCt.ownerCt.ownerCt.destroy();
						}
					}
				}
			]
		}, {
			autoScroll: true,
			region: "center",
			bodyStyle: "padding:5px 5px 0",
			border: false,
			labelWidth: 250,
			items: [
				[STORPROC [!Obj::getOrderedProperties!]|Cat]
					[!Title:=[!Key!]!]
					[STORPROC [!Cat!]|Categ]
						[COUNT [!Categ!]|Total]
						[STORPROC [!Categ!]|Prop]
							// Un fieldset par Catégorie
							{
								xtype: "fieldset",
								title: "[!Title!]",
								layout: "column",
								defaults: {
									defaultType: "label",
									bodyStyle: "padding:5px",
									columnWidth: .5,
									layout: "form",
									border: false,
									defaults: {
										labelSeparator: ""
									}
								},
								[!Cls:=1!]
								items: [
									{
										items: [
											[LIMIT 0|[!Math::Round([!Total:/2!])!]]
												[!Cls:=[!1:-[!Cls!]!]!]
												[IF [!Pos!]>1],[/IF][MODULE Systeme/Interface/LigneEtat?Prop=[!Prop!]&Cls=[!Cls!]]
											[/LIMIT]
										]
									}
									[IF [!Total!]>1]
									,{
										items: [
											[LIMIT [!Math::Round([!Total:/2!])!]|1000]
												[!Cls:=[!1:-[!Cls!]!]!]
												[IF [!Pos!]>1],[/IF][MODULE Systeme/Interface/LigneEtat?Prop=[!Prop!]&Cls=[!Cls!]]
											[/LIMIT]
										]
									}
									[/IF]
								]
							},
						[/STORPROC]
					[/STORPROC]
				[/STORPROC]
			]
		}
	]
}