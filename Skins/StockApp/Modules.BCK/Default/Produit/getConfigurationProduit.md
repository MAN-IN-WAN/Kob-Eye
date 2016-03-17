Ext.define('ActivaStock.fiche.ConfigurationProduit', {
    extend: 'Ext.form.Panel',
    requires: [
        'ActivaStock.item.ListeAttributItem',
        'ActivaStock.form.FormAttribut',
        'ActivaStock.utils.Button'
    ],
    xtype: 'configurationProduit',
    config: {
        title:'Configuration',
        iconCls: 'download',
        layout: 'vbox',
        cls: 'hasrecord',
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
                        align : 'right',
                        action: 'ajouter',
                        ui    : 'confirm',
                        text  : 'Ajouter Attribut'
                    }
                ]
            },
            {
                width: '100%',
                height: '100%',
                xtype: 'dataview',
                /*store: 'Attributs',*/
                useComponents: true,
                id: 'listeattributs',
                scrollable:true,
                cls: 'listeattributs',
                defaultType: 'listeAttributItem'
                /*,itemTpl: '{Nom} <tpl for="Declinaison">{Nom}</tpl>'*/
            }
        ]
    },
    updateRecord: function (record) {
        if (!record.isNew()){
            var tmpstore = record.getChildren('attributs');
            var newrecord = record.getChildrenRecord('attributs');
            console.log('store and record !!!!!!!!',tmpstore,newrecord);
            this.down('#listeattributs').setStore(tmpstore);
            this.down('[action=ajouter]').setStore(tmpstore);
            this.down('[action=ajouter]').setTargetRecord(newrecord);
            this.down('[action=ajouter]').setRefresh(this.down('#listeattributs'));
        }
    }
});
