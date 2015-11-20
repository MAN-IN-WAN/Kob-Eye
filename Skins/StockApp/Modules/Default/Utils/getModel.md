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

Ext.define('ActivaStock.model.[!I::TypeChild!]', {
    extend: 'ActivaStock.model.BaseModel',
    config: {
        autoLoad:true,
        fields: [
            {name: 'id',          type: 'int'},
            {name: 'label',          type: 'string'},
            [!NotFirst:=0!]
            [STORPROC [!P::getElements()!]|E]
                [STORPROC [!E::elements!]/hidden!=1&admin!=1&type!=rkey|El]
                        [LIMIT 0|100]
                            [IF [!NotFirst!]],[ELSE][!NotFirst:=1!][/IF][MODULE Systeme/Utils/getModelInput?El=[!El!]&P=[!P!]]
                        [/LIMIT]
                [/STORPROC]
            [/STORPROC]
        ],
        belongsTo: [
            [STORPROC [!P::getParentElements()!]|E]
                [IF [!Pos!]>1],[/IF]
                [MODULE Systeme/Utils/getModelFkey?El=[!E!]&P=[!P!]]
            [/STORPROC]
        ],
        hasMany: [
            [STORPROC [!P::getChildElements()!]|E]
                [IF [!Pos!]>1],[/IF]
                [MODULE Systeme/Utils/getModelRkey?El=[!E!]&P=[!P!]]
            [/STORPROC]
        ]/*,
        proxy: {
            type: 'ajax',
            api: {
                create: 'http://app.madeinchina.boutique/[!I::Module!]/[!I::TypeChild!]/getData.json',
                read: 'http://app.madeinchina.boutique/[!I::Module!]/[!I::TypeChild!]/getData.json',
                update: 'http://app.madeinchina.boutique/[!I::Module!]/[!I::TypeChild!]/getData.json',
                destroy: 'http://app.madeinchina.boutique/[!I::Module!]/[!I::TypeChild!]/deleteData.json'
            },
            method: 'GET',
            reader: {
                type: 'json',
                rootProperty: 'results'
            },
            writer: {
                type: 'json',
                writeAllFields: true
            }
        }*/

    }
});
