


Ext.define('ActivaStock.item.ListeEntrepotReferenceItemDetail', {
    extend: 'Ext.Panel',
    xtype: 'listeEntrepotReferenceItemDetail',

    config: {
        flex : 2,
        layout: 'hbox',
        minHeight: 50,
        items : [
            {
                flex :  3,
                layout : "vbox",
                items: [
                    
                    {
                        xtype: 'panel',
                        flex :  3,
                        itemId : 'propNom'
                    },
                    {
                        xtype: 'panel',
                        flex :  2,
                        itemId : 'propNomEntrepot'
                    },
                    
                    {
                        xtype: 'panel',
                        flex :  2,
                        itemId : 'propQuantite'
                    }
                ]
            },
            {
                flex :  2,
                layout : "vbox",
                items: [
                    
                    {
                        xtype: 'panel',
                        flex :  2,
                        itemId : 'propNumAllee'
                    }
                    ,
                    {
                        xtype: 'panel',
                        flex :  2,
                        itemId : 'propNumZone'
                    }
                    ,
                    {
                        xtype: 'panel',
                        flex :  2,
                        itemId : 'propNumEtage'
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
                        text  : 'Entrée en stock'
                    },
                    {
                        xtype : 'actionButton',
                        hidden: false,
                        align : 'right',
                        ui    : 'confirm',
                        action: 'modifier',
                        text  : 'Inventaire'
                    },
                    {
                        xtype : 'actionButton',
                        hidden: false,
                        align : 'right',
                        ui    : 'decline',
                        action: 'supprimer',
                        text  : 'Transfert'
                    }
                ]
            }
        ]
    },

    
    setNom : function(Nom){
        this.down("#propNom").setHtml('<h1>'+Nom+'</h1>');
    }
    
    ,
    setQuantite : function(Quantite){
        this.down("#propQuantite").setHtml('<p><b>Quantité:</b>'+Quantite+'</p>');
    }
    
    ,
    setNomEntrepot : function(NomEntrepot){
        this.down("#propNomEntrepot").setHtml('<p><b>'+NomEntrepot+'</b></p>');
    }    
    ,
    setNumAllee : function(NumAllee){
        this.down("#propNumAllee").setHtml('<p><b>Numéro d\'allée:</b>'+NumAllee+'</p>');
    }
    
    ,
    setNumZone : function(NumZone){
        this.down("#propNumZone").setHtml('<p><b>Numero de zone:</b>'+NumZone+'</p>');
    }
    
    ,
    setNumEtage : function(NumEtage){
        this.down("#propNumEtage").setHtml('<p><b>Numéro d\'étages:</b>'+NumEtage+'</p>');
    }
    
    
     ,
    updateRecord : function(record){
        this.callParent(arguments);
        if (record) {
            console.log('ENTREPOTREFERENCE updateRecord',record);
            this.down('[action=ajouter]').setTargetRecord(record);
            this.down('[action=modifier]').setTargetRecord(record);
            this.down('[action=supprimer]').setTargetRecord(record);
        }
    }
    ,
    updateStore : function(store){
        this.down('[action=ajouter]').setStore(store);
        this.down('[action=ajouter]').setRefresh(this.up('dataview'));
        this.down('[action=modifier]').setStore(store);
        this.down('[action=modifier]').setRefresh(this.up('dataview'));
        this.down('[action=supprimer]').setStore(store);
        this.down('[action=supprimer]').setRefresh(this.up('dataview'));
    }
});
