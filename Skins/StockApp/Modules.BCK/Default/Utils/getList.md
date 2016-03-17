[INFO [!Query!]|I]
//generation object
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
[!ObjCls:=[!O::getObjectClass()!]!]

Ext.define('ActivaStock.view.Liste[!I::TypeChild!]', {
    extend: 'Ext.Container',
    requires: [
        'ActivaStock.utils.DataView',
        'ActivaStock.model.[!I::TypeChild!]',
        'ActivaStock.item.Liste[!I::TypeChild!]Item',
        'ActivaStock.fiche.Fiche[!I::TypeChild!]',
        'Ext.field.Search'
    ],
    config: {
        layout: 'fit',
        cls: 'demo-list',
        items: [
            {
                xtype: 'toolbar',
                ui: 'dark',
                docked: 'top',
                scrollable: {
                    direction: 'horizontal',
                    indicators: false
                },
                items: [
                    {
                        xtype: 'searchfield',
                        placeHolder: 'Recherche...',
                        itemId: 'searchBox'
                    },
                    {
                        xtype: 'spacer'
                    }
                    [IF [!ObjCls::AccessPoint!]]
                    ,
                    {
                        xtype : 'actionButton',
                        hidden: false,
                        align : 'right',
                        ui    : 'confirm',
                        action: 'fiche',
                        text  : 'Ajouter [!I::TypeChild!]'
                    }
                    [/IF]
                ]
            }/*,
            {
                width: '100%',
                height: '100%',
                xtype: 'actionDataview',
                store: '[!I::TypeChild!]s',
                indexBar: true,
                grouped: true,
                useComponents: true,
                pinHeaders: false,
                cls: 'listearticle',
                id: 'liste[!I::TypeChild!]',
                defaultType: 'liste[!I::TypeChild!]Item'
            }*/
            ,
            {
                width: '100%',
                height: '100%',
                xtype: 'actionDataview',
                store: '[!I::TypeChild!]s',
                indexBar: false,
                grouped: true,
                pinHeaders: false,
                cls: 'listearticle',
                infinite: true,
                id: 'liste[!I::TypeChild!]',
                /*plugins: [
                    {
                        xclass: 'Ext.plugin.ListPaging',
                        autoPaging: true,
                        loadMoreText: 'Chargement...',
                        noMoreRecordsText: 'Pas plus d\'enregistrements'
                    },
                    {
                        xclass: 'Ext.plugin.PullRefresh',
                        pullText: 'Glissez vers le bas pour rafraichir.'
                    }
                ],*/
                itemTpl: '<div class="as-table-line">[STORPROC [!O::getElementsByAttribute(type,image,1)!]|E|0|1]<img src="http://app.madeinchina.boutique/{Image}.mini.40x40.jpg" />[/STORPROC]<div class="as-table-detail"><h2><b> {[STORPROC [!O::getElementsByAttribute(searchOrder,,1)!]|E|0|1][!E::name!][/STORPROC]} </b></h2><small>[STORPROC [!O::getElementsByAttribute(searchOrder,,1)!]|E|1|2]{[!E::name!]} [/STORPROC]</small></div></div>'
            }
        ],
        listeners: {
            'painted': function () {
              [IF [!ObjCls::AccessPoint!]]
                var newrecord = Ext.create('ActivaStock.model.[!I::TypeChild!]');
                this.down('[action=fiche]').setTargetRecord(newrecord);
                this.down('[action=fiche]').setStore(this.down('#liste[!I::TypeChild!]').getStore());
              [/IF]
            }
        }
    }
});
