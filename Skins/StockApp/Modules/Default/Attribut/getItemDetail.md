Ext.define('ActivaStock.item.ListeAttributItemDetail', {
    extend: 'Ext.Panel',
    xtype: 'listeAtributItemDetail',

    requires: [
        'ActivaStock.item.ListeDeclinaisonItem',
        'ActivaStock.form.FormDeclinaison'
    ],


    config: {
        flex : 2,
        cls: 'hasrecord',
        layout: {
            type: 'fit'
        },
        items : [
            {
                xtype: 'panel',
                layout : "hbox",
                minHeight : 50,
                items: [
                    {
                        xtype: 'panel',
                        layout : "vbox",
                        flex:2,
                        minHeight : 50,
                        items: [
                            {
                                xtype: 'panel',
                                flex :  3,
                                itemId : 'nom'
                            },
                            {
                                xtype: 'panel',
                                flex :  2,
                                itemId : 'code'
                            },
                            {
                                xtype: 'panel',
                                flex :  2,
                                itemId : 'nompublic'
                            }
                        ]
                    },
                    {
                        xtype: 'toolbar',
                        flex: 4,
                        width: '100%',
                        cls: 'transparentToolbar',
                        items:[
                            {
                                xtype: 'spacer'  
                            },
                            {
                                xtype : 'actionButton',
                                hidden: false,
                                align : 'right',
                                action: 'ajouter',
                                text  : 'Ajouter Déclinaison'/*,
                                handler: function (){
                                    //reset record 
                                    var newrecord = Ext.create('ActivaStock.model.Declinaison');
                                    newrecord.setAttribut(this.getTargetRecord().getAttribut());
                                    newrecord.setDirty();
                                    this.setTargetRecord(newrecord);
                                }*/
                            },
                            {
                                xtype : 'actionButton',
                                hidden: false,
                                align : 'right',
                                ui    : 'confirm',
                                action: 'modifier',
                                text  : 'Modifier'
                            },
                            {
                                xtype : 'actionButton',
                                hidden: false,
                                align : 'right',
                                ui    : 'decline',
                                action: 'supprimer',
                                text  : 'Supprimer'
                            }
                        ]
                    }
                ]
            },
            {
                xtype: 'dataview',
                /*store: 'Attributs',*/
                useComponents: true,
                scrollable: null,
                height: 'auto',
                itemHeight: 60,
                cls: 'liste-decalage',
                defaultType: 'listeDeclinaisonItem'/*,
                itemTpl: '<div class="declinaison-list-item"><h1>{Nom}</h1></div>'*/
            }
        ]

    },

    setNom : function(nom){
        this.down("#nom").setHtml('<h1>'+nom+'</h1>');
    },
    setCode : function(code){
        this.down("#code").setHtml('<p><b>Préfixe de référence:</b>'+code+'</p>');
    },
    setNomPublic : function(nompublic){
        this.down("#nompublic").setHtml('<p><b>Nom public:</b>'+nompublic+'</p>');
    },
    updateRecord : function(record,store){
        if (!record.isNew()){
            var decl = record.getChildren('declinaisons');
            this.down('[xtype=dataview]').setStore(decl);
            var newrecord = record.getChildrenRecord('declinaisons');
            this.down('[action=ajouter]').setTargetRecord(newrecord);
            this.down('[action=ajouter]').setRefresh(this.down('[xtype=dataview]'));
            this.down('[action=ajouter]').setStore(decl);
            this.down('[action=modifier]').setTargetRecord(record);
            this.down('[action=modifier]').setRefresh(this.up('[xtype=dataview]'));
            this.down('[action=supprimer]').setTargetRecord(record);
        }
    },
    updateStore: function (store){
        this.down('[action=modifier]').setStore(store);
        this.down('[action=supprimer]').setStore(store);
    }

});
