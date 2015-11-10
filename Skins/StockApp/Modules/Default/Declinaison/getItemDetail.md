Ext.define('ActivaStock.item.ListeDeclinaisonItemDetail', {
    extend: 'Ext.Panel',
    xtype: 'listedeclinaisonitemdetail',

    config: {
        flex : 2,


        layout: {
            type: 'hbox'
        },
        items : [
            {
                xtype: 'panel',
                flex: 2,
                layout : "vbox",
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
        this.down('[action=modifier]').setTargetRecord(record);
        this.down('[action=supprimer]').setTargetRecord(record);
    },
    updateStore : function(store){
        this.down('[action=modifier]').setStore(store);
        this.down('[action=supprimer]').setStore(store);
    }

});
