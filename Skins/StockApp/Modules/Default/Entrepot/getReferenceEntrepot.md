Ext.define('ActivaStock.fiche.ReferenceEntrepot', {
    extend: 'Ext.form.Panel',
    xtype: 'referenceEntrepot',
    requires: [
        'ActivaStock.item.ListeEntrepotReferenceItem',
        'ActivaStock.form.FormEntrepotReference',
        'Ext.Toast'
    ],
    config: {
        title:'Références',
        iconCls: 'bookmarks',
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
                        text  : 'Entrée de stock',
                        action: 'ajouter'
                    }
                ]
            },
            {
                width: '100%',
                height: '100%',
                xtype: 'dataview',
                useComponents: true,
                scrollable:true,
                cls: 'listeentrepotreference',
                defaultType: 'listeEntrepotReferenceItem'
            }
        ]
    },
    updateRecord: function (record) {
        if (!record.isNew()) {
            var tmpstore =  record.getChildren('references');
            this.down('dataview').setStore(tmpstore);
            this.down('actionButton').setStore(tmpstore);
            this.down('actionButton').setRefresh(this.down('dataview'));
        }
    }
});
