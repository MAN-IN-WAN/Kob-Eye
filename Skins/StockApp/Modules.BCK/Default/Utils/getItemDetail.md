[INFO [!Query!]|I]
//generation object
[OBJ [!I::Module!]|[!I::TypeChild!]|P]

Ext.define('ActivaStock.item.Liste[!I::TypeChild!]ItemDetail', {
    extend: 'Ext.Panel',
    xtype: 'liste[!I::TypeChild!]ItemDetail',

    config: {
        flex : 2,
        layout: 'hbox',
        items : [
            {
                flex :  5,
                layout : "vbox",
                minHeight : 80,
                items: [
                    [STORPROC [!P::getElementsByAttribute(searchOrder,,1)!]|El|0|4]
                    {
                        xtype: 'panel',
                        flex :  [IF [!Pos!]=1]3[ELSE]2[/IF],
                        itemId : 'prop[!El::name!]'
                    },
                    [/STORPROC]
                    {
                        flex :  2,
                        layout : "hbox",
                        items: [
                            [STORPROC [!P::getElementsByAttribute(searchOrder,,1)!]|El|4|4]
                            [IF [!Pos!]>1],[/IF]
                            {
                                xtype: 'panel',
                                flex :  2,
                                itemId : 'prop[!El::name!]'
                            }
                            [/STORPROC]
                        ]
                    }
                ]
            }
        ]

    },

    [STORPROC [!P::getElementsByAttribute(searchOrder,,1)!]|El|0|1]
    set[!El::name!] : function([!El::name!]){
        this.down("#prop[!El::name!]").setHtml('<h1>'+[!El::name!]+'</h1>');
    }
    [/STORPROC]
    [STORPROC [!P::getElementsByAttribute(searchOrder,,1)!]|El|1|4]
    ,
    set[!El::name!] : function([!El::name!]){
        this.down("#prop[!El::name!]").setHtml('<p><b>[JSON][!El::description!][/JSON]:</b>'+[!El::name!]+'</p>');
    }
    [/STORPROC]
    ,
    updateRecord : function(record){
    }
    ,
    updateStore : function(store){
    }
});
