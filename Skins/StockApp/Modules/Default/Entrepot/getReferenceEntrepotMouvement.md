Ext.define('ActivaStock.fiche.ReferenceEntrepotMouvement', {
    extend: 'Ext.form.Panel',
    xtype: 'referenceEntrepotMouvement',
    requires: [
        'ActivaStock.item.ListeMouvementItem',
        'Ext.Toast'
    ],
    config: {
        title:'Mouvements',
        iconCls: 'time',
        scrollable:null,
        items: [
            {
                xtype: 'toolbar',
                width: '100%',

                items:[
                    {
                        xtype: 'spacer'  
                    },
                    {
                        xtype : 'actionButton',
                        hidden: false,
                        ui    : 'confirm',
                        align : 'right',
                        text  : 'Rafraîchir',
                        action: 'refresh'
                    }
                ]
            },
            {
                width: '100%',
                height: '100%',
                xtype: 'dataview',
                useComponents: true,
                scrollable:true,
                cls: 'listeentrepotreferencemouvement',
                defaultType: 'listeMouvementItem'
            }
        ]
    },
    updateRecord: function (record) {
        if (!record.isNew()){
            var tmpstore =  record.getChildren('mouvements');
            this.down('dataview').setStore(tmpstore);
            this.down('actionButton').setStore(tmpstore);
            this.down('actionButton').setRefresh(this.down('dataview'));
        }
    }
});
