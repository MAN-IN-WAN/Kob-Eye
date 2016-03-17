[INFO [!Query!]|I]
//generation object
[OBJ [!I::Module!]|[!I::TypeChild!]|P]

Ext.define("ActivaStock.store.[!I::TypeChild!]s", {
    extend: 'ActivaStock.store.BaseStore',
//    extend: 'Ext.data.Store',
    alias: 'store.[!I::TypeChild!]s',
    config: {
        model: 'ActivaStock.model.[!I::TypeChild!]',
        /*autoLoad: true,*/
        grouper: function(record) {
            [STORPROC [!P::SearchOrder!]|Prop|0|1]
            if (!record.get('[!Prop::Nom!]')) return;
            return record.get('[!Prop::Nom!]')[0];
            [/STORPROC]
        },
        localProxy: {
             /*type: 'localstorage',
             id: 'local.[!I::TypeChild!]s'*/
            type: 'sql',
            database: "ActivaStock",
            table: "[!I::TypeChild!]"
        },
        serverProxy: {
            type: 'ajax',
            useDefaultXhrHeader: false,
            api: {
                create: 'http://app.madeinchina.boutique/[!I::Module!]/[!I::TypeChild!]/getData.json',
                read: 'http://app.madeinchina.boutique/[!I::Module!]/[!I::TypeChild!]/getData.json',
                update: 'http://app.madeinchina.boutique/[!I::Module!]/[!I::TypeChild!]/getData.json',
                destroy: 'http://app.madeinchina.boutique/[!I::Module!]/[!I::TypeChild!]/deleteData.json'
            },
            method: 'GET',
            reader: {
                type: 'json',
                rootProperty: 'results',
                totalProperty: 'total'
            },
            writer: {
                type: 'json',
                writeAllFields: true
            }
        }
        /*proxy: {
            type: 'ajax',
            useDefaultXhrHeader: false,
            api: {
                create: 'http://app.madeinchina.boutique/[!I::Module!]/[!I::TypeChild!]/getData.json',
                read: 'http://app.madeinchina.boutique/[!I::Module!]/[!I::TypeChild!]/getData.json',
                update: 'http://app.madeinchina.boutique/[!I::Module!]/[!I::TypeChild!]/getData.json',
                destroy: 'http://app.madeinchina.boutique/[!I::Module!]/[!I::TypeChild!]/deleteData.json'
            },
            actionMethods: {
                create : 'POST',
                read   : 'POST',
                update : 'POST',
                destroy: 'POST'
            },
            reader: {
                type: 'json',
                rootProperty: 'results',
                totalProperty: 'total'
            },
            writer: {
                type: 'json',
                writeAllFields: true
            }
        }*/
    }
});
