{
	xtype: "container",
	layout: "border",
	height: "100%",
	border: false,
	defaults: {
		padding: "5"
	},
	items: [
		{
			region: "west",
			width: 250,
			xtype: "form",
			items: [
				{
					xtype: "fieldset",
					title: "Quick search",
       				labelWidth: 80,
					items: [
						{
							xtype: "textfield",
							fieldLabel: "Keyword(s)",
							labelSeparator: "",
							anchor: "100%"
						},
						{
							xtype: "button",
							text: "Search",
							anchor: "100%",
							listeners: {
								"click": function( button ) {
									var store = button.ownerCt.ownerCt.nextSibling().nextSibling().getStore();
									store.reload();
								}
							}
						}
					]
				},
				{
					xtype: "fieldset",
					title: "Search by Field",
					defaultType: "textfield",
					items: [
						[STORPROC [!Obj::SearchOrder!]|So]
							[SWITCH [!So::Type!]|=]
								[CASE boolean]
									{
										xtype: "combo",
										mode: "local",
										triggerAction: "all",
										forceSelection: true,
										editable: false,
										disableKeyFilter: true,
										store: [
											[ "", "Both" ],
											[ "0", "Non" ],
											[ "1", "Oui" ]
										],
										fieldLabel: "[!So::Nom!]",
										labelSeparator: "",
										anchor: "100%"
									},
								[/CASE]
								[CASE date]
									{
										xtype: "datefield",
										fieldLabel: "[!So::Nom!] du",
										labelSeparator: "",
										anchor: "100%",
										format: "d/m/Y"
									},
									{
										xtype: "datefield",
										fieldLabel: "[!So::Nom!] au",
										labelSeparator: "",
										anchor: "100%",
										format: "d/m/Y"
									},
								[/CASE]
								[DEFAULT]
									{
										fieldLabel: "[!So::Nom!]",
										labelSeparator: "",
										anchor: "100%"
									},
								[/DEFAULT]
							[/SWITCH]
						[/STORPROC]
						{
							fieldLabel: "Creation date from",
							xtype: "datefield",
							labelSeparator: "",
							anchor: "100%",
							format: "d/m/Y"
						},
						{
							fieldLabel: "Creation date to",
							xtype: "datefield",
							labelSeparator: "",
							anchor: "100%",
							format: "d/m/Y"
						},
						{
							fieldLabel: "Last edit from",
							xtype: "datefield",
							labelSeparator: "",
							anchor: "100%",
							format: "d/m/Y"
						},
						{
							fieldLabel: "Last edit to",
							xtype: "datefield",
							labelSeparator: "",
							anchor: "100%",
							format: "d/m/Y"
						},
						{
							xtype: "button",
							text: "Search",
							anchor: "100%",
							listeners: {
								"click": function( button ) {
									var store = button.ownerCt.ownerCt.nextSibling().nextSibling().getStore();
									store.reload();
								}
							}
						}
					]
				}
			]
		},{
			region: "north",
			margins: "0 0 5 0",
			xtype: "toolbar",
			defaultType: "button",
			height: "25",
			items: [{
				text: "Add",
				listeners: {
					"click": function( button ) {
						openTab('/[!Obj::Module!]/[!Obj::ObjectType!]/Ajouter.htm', 'Add [!Query!]');
					}
				}
			},{
				text: "Display",
				listeners: {
					"click": function( button ) {
						var grid = button.ownerCt.nextSibling();
						grid.getSelectionModel().each( function(record) {
							var id = record.get('Id');
							openTab('/[!Obj::Module!]/[!Obj::ObjectType!]/' + id + '/Etat.htm', 'Display [!Query!]/' + id, button.ownerCt.ownerCt);
						});
					}
				}
			},{
				text: "Edit",
				listeners: {
					"click": function( button ) {
						var grid = button.ownerCt.nextSibling();
						grid.getSelectionModel().each( function(record) {
							var id = record.get('Id');
							openTab('/[!Obj::Module!]/[!Obj::ObjectType!]/' + id + '/Modifier.htm', 'Edit [!Query!]/' + id, button.ownerCt.ownerCt);
						});
					}
				}
			},{
				text: "Delete",
				listeners: {
					"click": function( button ) {
						var grid = button.ownerCt.nextSibling();
						grid.getSelectionModel().each( function(record) {
							var id = record.get('Id');
							Ext.MessageBox.confirm('Confirmation', 'Do you really want to delete this item ?', function(btn) {
								if(btn == 'yes') {
									Ext.Ajax.request({
										url: '/[!Obj::Module!]/[!Obj::ObjectType!]/' + id + '/Supprimer.htm',
										success: function(xhr) {
											Ext.MessageBox.alert("Status", "Item has been deleted.");
											grid.store.reload();
										}
									});
								}
							});
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
			}]
		}
	],
	listeners: {
		"beforerender": function(panel) {
			/////////// Configuration de la liste

			///// Système de données
			var myStore = new Ext.data.JsonStore({
				fields: [MODULE [!Obj::Module!]/[!Obj::ObjectType!]/Get?Load=Fields],
				remoteSort: true,
				root: "data",
				totalProperty: "count",
				proxy: new Ext.data.HttpProxy({
					url: "/[!Lien!]/Get.htm?Load=Data"
				}),
				listeners: {
					"beforeload": function( store, options ) {
						// Recherche générale
						var rs = panel.items.get(0).items.get(0).items.get(0).getValue();
						if(rs != undefined) {
							Ext.apply(options.params, {
								"rapidSearch": rs
							});
						}
						// Recherche par search Order
						[!Indice:=0!]
						[STORPROC [!Obj::SearchOrder!]|So]
							[SWITCH [!So::Type!]|=]
								[CASE date]
									var so = panel.items.get(0).items.get(1).items.get([!Indice!]).getValue();
									if(so != undefined) {
										Ext.apply(options.params, {
											"[!So::Nom!]Du": so
										});
									}
									[!Indice+=1!]
									var so = panel.items.get(0).items.get(1).items.get([!Indice!]).getValue();
									if(so != undefined) {
										Ext.apply(options.params, {
											"[!So::Nom!]Au": so
										});
									}
								[/CASE]
								[DEFAULT]
									var so = panel.items.get(0).items.get(1).items.get([!Indice!]).getValue();
									if(so != undefined) {
										Ext.apply(options.params, {
											"[!So::Nom!]": so
										});
									}
								[/DEFAULT]
							[/SWITCH]
							[!Indice+=1!]
						[/STORPROC]
						// tmsCreate & tmsEdit
						var so = panel.items.get(0).items.get(1).items.get([!Indice!]).getValue();
						if(so != undefined) {
							Ext.apply(options.params, {
								"tmsCreateDu": so
							});
						}
						[!Indice+=1!]
						var so = panel.items.get(0).items.get(1).items.get([!Indice!]).getValue();
						if(so != undefined) {
							Ext.apply(options.params, {
								"tmsCreateAu": so
							});
						}
						[!Indice+=1!]
						var so = panel.items.get(0).items.get(1).items.get([!Indice!]).getValue();
						if(so != undefined) {
							Ext.apply(options.params, {
								"tmsEditDu": so
							});
						}
						[!Indice+=1!]
						var so = panel.items.get(0).items.get(1).items.get([!Indice!]).getValue();
						if(so != undefined) {
							Ext.apply(options.params, {
								"tmsEditAu": so
							});
						}
					}
				}
			});
			myStore.setDefaultSort('Id', 'asc');

			///// Système de colonnes
			var myColModel = new Ext.grid.ColumnModel({
				defaults: {
					sortable: true
				},
				columns: [MODULE [!Obj::Module!]/[!Obj::ObjectType!]/Get?Load=Columns]
			});

			// Menu contextuel
			var contextMenu = new Ext.menu.Menu({
				items: [{
					text: 'Delete',
					handler: function() { alert('supprimer'); }
				},
				{
					text: 'Modifier',
					handler: function() { alert('modifier'); }
				}]
			});

			///// Grid
			var grid = new Ext.grid.GridPanel({
				region: "center",
				margins: "0 0 0 5",
				padding: "0",
				store: myStore,
				colModel: myColModel,
				loadMask: true,
				height: "100%",
				viewConfig: {
					autoFill: true,
					forceFit:true
				},
				bbar: {
					xtype: "paging",
					pageSize: 20,
					store: myStore,
					displayInfo: true,
					displayMsg: "Éléments {0} à {1} sur un total de {2}",
					emptyMsg: "Aucun élément à afficher"
				},
				contextMenu: contextMenu,
				listeners: {
					"afterrender": function(grid) {
						// Chargement des premières données
						grid.store.load({params:{start:0, limit:20}});
					},
					"rowdblclick": function( grid, index ) {
						var id = grid.getStore().getAt(index).get('Id');
						openTab('/[!Query!]/' + id + '/Etat.htm', 'Afficher [!Query!]/' + id);
					},
					"cellcontextmenu": function(grid, row, column, e){
						e.preventDefault();
						var menu = new Ext.menu.Menu({
							items : [{
								xtype: "menuitem",
								text: "Edit",
								listeners: {
									"click": function() {
										var id = grid.getStore().getAt(row).get('Id');
										openTab('/[!Obj::Module!]/[!Obj::ObjectType!]/' + id + '/Modifier.htm', 'Edit [!Query!]/' + id, panel);
									}
								}
							},
							{
								xtype: "menuitem",
								text: "Delete",
								listeners: {
									"click": function() {
										var id = grid.getStore().getAt(row).get('Id');
										Ext.MessageBox.confirm('Confirmation', 'Do you really want to delete this item ?', function(btn) {
											if(btn == 'yes') {
												Ext.Ajax.request({
													url: '/[!Obj::Module!]/[!Obj::ObjectType!]/' + id + '/Supprimer.htm',
													success: function(xhr) {
														Ext.MessageBox.alert("Status", "Item has been deleted.");
														grid.store.reload();
													}
												});
											}
										});
									}
								}
							}]
						});
						menu.showAt(e.getXY());
					}
				}
			});
			panel.items.add(grid);
		}
	}
}