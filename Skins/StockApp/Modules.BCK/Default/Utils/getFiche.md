[INFO [!Query!]|I]
//generation object
[OBJ [!I::Module!]|[!I::TypeChild!]|P]

Ext.define('ActivaStock.fiche.Fiche[!I::TypeChild!]', {
    extend: 'Ext.form.Panel',

    requires: [
        'ActivaStock.fiche.Resume[!I::TypeChild!]',
        [STORPROC [!P::getInterfaces()!]|Int]
            [STORPROC [!Int!]|Form]
        '[!Form::form!]',
            [/STORPROC]
        [/STORPROC]
        'ActivaStock.form.Form[!I::TypeChild!]'
    ],
    config: {
        layout: 'fit',
        scrollable:null,
        items: [
            {
                xtype: 'tabpanel',
                tabBarPosition: 'bottom',
                scrollable:null,
                layout: {
                    animation: 'slide',
                    type: 'card'
                },
                items: [
                    {
                        xtype: 'resume[!I::TypeChild!]',
                    },
                    {
                        title: 'Modification',
                        iconCls: 'settings',
                        layout:'fit',
                        items:[
                            {
                                xtype: 'toolbar',
                                ui: 'dark',
                                docked: 'top',
                                cls:'mainNavigationBar',
                                items: [
                                    {
                                        xtype: 'spacer'
                                    },
                                    {
                                        xtype : 'actionButton',
                                        hidden: false,
                                        align : 'right',
                                        ui    : 'decline',
                                        action: 'supprimer',
                                        text  : 'Supprimer [!I::TypeChild!]'
                                    },
                                    {
                                        xtype : 'actionButton',
                                        hidden: false,
                                        align : 'right',
                                        ui    : 'confirm',
                                        action: 'enregistrer',
                                        text  : 'Enregistrer [!I::TypeChild!]'
                                    }
                                ]
                            },
                            {
                                xtype: 'form[!I::TypeChild!]'
                            }
                        ]
                    }
                    [STORPROC [!P::getInterfaces()!]|Int]
                        [STORPROC [!Int!]|Form]
                    ,{
                        xtype: '[!Form::xtype!]',
                        title: '[JSON][!Form::name!][/JSON]'
                    }
                            
                        [/STORPROC]
                    [/STORPROC]
                ]
            }
        ]
    },
    updateRecord: function (record) {
        if (record.isNew()) {
            this.down('[xtype=resume[!I::TypeChild!]]').setDisabled(true);
            [STORPROC [!P::getInterfaces()!]|Int]
                [STORPROC [!Int!]|Form]
            this.down('[xtype=[!Form::xtype!]]').setDisabled(true);
                [/STORPROC]
            [/STORPROC]
            this.down('tabpanel').setActiveItem(1);
        }else{
            this.down('[xtype=resume[!I::TypeChild!]]').setDisabled(false);
            [STORPROC [!P::getInterfaces()!]|Int]
                [STORPROC [!Int!]|Form]
            this.down('[xtype=[!Form::xtype!]]').setDisabled(false);
                [/STORPROC]
            [/STORPROC]
            this.down('tabpanel').setActiveItem(0);
        }
        this.down('[xtype=resume[!I::TypeChild!]]').updateRecord(record);
        [STORPROC [!P::getInterfaces()!]|Int]
            [STORPROC [!Int!]|Form]
        this.down('[xtype=[!Form::xtype!]]').updateRecord(record);
            [/STORPROC]
        [/STORPROC]
        this.down('[action=supprimer]').setTargetRecord(record);
        this.down('[action=enregistrer]').setTargetRecord(record);
        this.down('[action=enregistrer]').setForm(this.down('[xtype=form[!I::TypeChild!]]'));
        this.down('[action=enregistrer]').setFiche(this);
    },
    updateStore: function (store) {
        this.down('[action=supprimer]').setStore(store);
        this.down('[action=enregistrer]').setStore(store);
    }
});