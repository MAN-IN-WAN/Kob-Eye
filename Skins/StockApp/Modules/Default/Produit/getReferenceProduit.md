Ext.define('ActivaStock.fiche.ReferenceProduit', {
    extend: 'Ext.form.Panel',
    xtype: 'referenceProduit',
    requires: [
        'ActivaStock.item.ListeReferenceSpecialItem'
    ],
    config: {
        title:'Références',
        iconCls: 'bookmarks',
        scrollable:null,
        items: [
            {
                xtype: 'toolbar',
                width: '100%',
//                cls: 'transparentToolbar',
                items:[
                    {
                        xtype: 'spacer'  
                    },
                    {
                        xtype : 'actionButton',
                        hidden: false,
                        align : 'right',
                        text  : 'Générer les références',
                        handler: function (){
                            var me = this;
                            console.log('générer les références et mettre à jour la liste');
                            Ext.Ajax.request({
                                url: 'http://app.madeinchina.boutique/Boutique/Produit/'+me.getTargetRecord().get('id')+'/genererReference.json',
                                success: function(response){
                                    console.log('response success',response);
                                    // process server response here
                                    me.getStore().load();
                                    Ext.getStore(me.getTargetRecord().getFlatClassName()+'s').load();
                                }
                            });
                        }
                    }
                ]
            },
            {
                width: '100%',
                height: '100%',
                xtype: 'dataview',
                useComponents: true,
                scrollable:true,
                cls: 'listereference',
                defaultType: 'listeReferenceSpecialItem'
            }
        ]
    },
    updateRecord: function (record) {
        if (!record.isNew()){
            var tmpstore = record.getChildren('references');
            this.down('dataview').setStore(tmpstore);
            this.down('actionButton').setStore(tmpstore);
            this.down('actionButton').setTargetRecord(record);
            this.down('actionButton').setForm(this.down('dataview'));
        }
    }
});
