// TODO List
// Echapper les values

{
	layout: "border",
	xtype: "container",
	layout: "border",
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
					text: "Save",
					listeners: {
						"click": function( button, e ) {
							tinyMCE.triggerSave();
							button.ownerCt.nextSibling().getForm().submit({
								success: function() {
									var tab = button.ownerCt.ownerCt.ownerCt;
									// Actualisation....
									// Parent = grille
									if(tab.parentTab.items.get(2) != undefined) {
										tab.parentTab.items.get(2).getStore().reload();
									}
									// Parent = état
									else {
										loadTab(tab.parentTab.ownerCt);
									}
									// Message de confirmation et fermeture de cet onglet
									Ext.MessageBox.alert("Status", "Enregistrement effectué.");
									tab.destroy();
								},
								waitMsg: "Enregistrement en cours..."
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
			xtype: "form",
			region: "center",
			autoScroll: true,
			url: "[!Obj::myUrl!]/Enregistrer.htm",
			bodyStyle: "padding:5px 5px 0",
			border: false,
			labelWidth: 250,
			items: [
				[STORPROC [!Obj::getOrderedProperties!]|Cat]
					[!Title:=[!Key!]!]
					[STORPROC [!Cat!]|Categ]
						[COUNT [!Categ!]|Total]
						[STORPROC [!Categ!]|Prop]
							// Fieldset de la Catégorie
							{
								xtype: "fieldset",
								title: "[!Title!]",
								layout: "column",
								defaults: {
									defaultType: "textfield",
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
												[IF [!Pos!]>1],[/IF][MODULE Systeme/Interface/LigneForm?Prop=[!Prop!]&Cls=[!Cls!]]
											[/LIMIT]
										]
									}
									[IF [!Total!]>1]
									,{
										items: [
											[LIMIT [!Math::Round([!Total:/2!])!]|1000]
												[!Cls:=[!1:-[!Cls!]!]!]
												[IF [!Pos!]>1],[/IF][MODULE Systeme/Interface/LigneForm?Prop=[!Prop!]&Cls=[!Cls!]]
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