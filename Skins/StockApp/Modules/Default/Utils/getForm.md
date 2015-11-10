[INFO [!Query!]|I]
//generation object
[IF [!I::TypeSearch!]=Child]
	//Nouveau
	[OBJ [!I::Module!]|[!I::TypeChild!]|P]
	[!HistoBase:=[!I::Historique!]!]
	[!HistoBase:=[!HistoBase::0!]!]
	[METHOD P|AddParent]
		[PARAM][!HistoBase::Module!]/[!HistoBase::DataSource!]/[!HistoBase::Value!][/PARAM]
	[/METHOD]
	[!TYPE:=NEW!]
[ELSE]
	[STORPROC [!Query!]|P][/STORPROC]
	[!TYPE:=EDIT!]
[/IF]
[!Pref:=POPUP!]


Ext.define('ActivaStock.form.Form[!I::TypeChild!]', {
    xtype: 'form[!I::TypeChild!]',
    extend: 'Ext.form.Panel',

    requires: [
        'Ext.form.Toggle',
        'Ext.form.FieldSet',
        'Ext.field.Number',
        'Ext.field.Spinner',
        'Ext.field.Password',
        'Ext.field.Email',
        'Ext.field.Url',
        'Ext.field.DatePicker',
        'Ext.field.Select',
        'Ext.field.Hidden',
        'Ext.field.Radio'
    ],
    config: {
        iconCls: 'settings',
        title: 'Modifications',
        items: [
            [STORPROC [!P::getElementsByAttribute(mobile,1)!]|E]
            {
                xtype: 'fieldset',
                title: '[!Key!]',
                instructions: '',
                defaults: {
                    labelWidth: '50%'
                },
                items: [
                    [!NotFirst:=0!]
                    [STORPROC [!E::elements!]/hidden!=1&admin!=1&auto!=1|El]
                            [LIMIT 0|100]
                                [IF [!NotFirst!]],[ELSE][!NotFirst:=1!][/IF]
                                [MODULE Systeme/Utils/getFormInput?El=[!El!]&P=[!P!]]
                            [/LIMIT]
                    [/STORPROC]
                ]
            },
            [/STORPROC]
        ]
    }
});
