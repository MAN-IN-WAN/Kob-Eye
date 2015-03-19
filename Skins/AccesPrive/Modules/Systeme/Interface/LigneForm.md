// TODO List
// Si un select a plus de 100 possibilités, gérer le champ en asynchrone
// Champs : Admin=1 (uniquement visible par un administrateur) ; Hidden = 1 (ne pas afficher) ; Auto = 1 (readonly)
// Fancy upload

// TIPS
// Value du password non répétée
// Limite synchrone vs. assynchrone
[!Lim:=5!]

{
	fieldLabel: "[!Prop::Nom!]",
	value: "[!Prop::Valeur!]",
	name: "[!Prop::Nom!]",
	type: "[!Prop::Type!]",
	anchor: "97%",
	border: true,
	[SWITCH [!Prop::Type!]|=]
		[CASE boolean]
			xtype: "checkbox",
			name: "[!Prop::Nom!]",
			[IF [!Prop::Valeur!]=1]
				checked: true,
			[/IF]
		[/CASE]
		[CASE date]
			xtype: "datefield",
			value: "[IF [!Prop::Valeur!]=0||[!Prop::Valeur!]=][ELSE][!Utils::getDate(d/m/Y, [!Prop::Valeur!])!][/IF]",
			format: "d/m/Y",
		[/CASE]
		[CASE file]
			[!idUpload:=[!Utils::getDate(U)!]-[!Utils::Random(1000000)!]!]
			xtype: "panel",
			border: false,
			bodyStyle: "background:none",
            html: '<a href="#" id="select-file-[!idUpload!]">Changer le fichier</a> - <a href="#" onclick="$(\'selected-file-[!idUpload!]\').value=\'\'">Effacer le fichier</a> <img id="progress-file-[!idUpload!]" src="/Skins/[!Systeme::Skin!]/Img/fancy/bar.gif" style="background:url(\'/Skins/[!Systeme::Skin!]/Img/fancy/progress.gif\') 100% 0; display: none;" /><br /><input type="text" name="[!Prop::Nom!]" id="selected-file-[!idUpload!]" class="x-form-text x-form-field" style="width:98%" value="[IF [!Prop::Valeur!]=]Pas de fichier[ELSE][!Prop::Valeur!][/IF]" />',
		[/CASE]
		[CASE float]
			xtype: "numberfield",
		[/CASE]
		[CASE int]
			xtype: "numberfield",
		[/CASE]
		[CASE mail]
			vtype: "email",
		[/CASE]
		[CASE order]
			xtype: "numberfield",
			width: 80,
			anchor: "",
		[/CASE]
		[CASE password]
			inputType: "password",
			value: "*******",
		[/CASE]
		[CASE text]
			xtype: "textarea",
		[/CASE]
		[CASE bbcode]
			[!idEditor:=[!Utils::getDate(U)!]-[!Utils::Random(1000000)!]!]
			xtype: "panel",
			border: false,
			bodyStyle: "background:none",
			html: '<textarea rows="15" class="mceEditor x-form-textarea x-form-field" style="width:98%" name="[!Prop::Nom!]" id="[!idEditor!]">[CONCAT][JSON][!Prop::Valeur!][/JSON][/CONCAT]</textarea>',
		[/CASE]
		[CASE html]
			[!idEditor:=[!Utils::getDate(U)!]-[!Utils::Random(1000000)!]!]
			xtype: "panel",
			border: false,
			bodyStyle: "background:none",
			html: '<textarea rows="15" class="mceEditor x-form-textarea x-form-field" style="width:98%" name="[!Prop::Nom!]" id="[!idEditor!]">[CONCAT][JSON][!Prop::Valeur!][/JSON][/CONCAT]</textarea>',
		[/CASE]
		[CASE varchar]
			[IF [!Prop::query!]]
				[STORPROC [![!Prop::query!]:/::!]|ReqOptions|0|1][/STORPROC]
				[STORPROC [![!Prop::query!]:/::!]|OptVisible|1|1][/STORPROC]
				[STORPROC [![!Prop::query!]:/::!]|OptEnbase|2|1][/STORPROC]
				xtype: "combo",
				store: [
					[COUNT [!ReqOptions!]|CO]
					["", "- Veuillez sélectionner -"]
					[STORPROC [!ReqOptions!]|Val|0|100]
						[IF [!OptEnbase!]=]
							[IF [!OptVisible!]=]
								// Seulement la requête
								[!OptBase:=[!Val::Id!]!]
								[!OptHTML:=[!Val::getFirstSearchOrder!]!]
							[ELSE]
								// Requête + champ spécifique
								[!OptBase:=[!Val::[!OptVisible!]!]!]
								[!OptHTML:=[!Val::[!OptVisible!]!]!]
							[/IF]
						[ELSE]
							// Valeurs stockée et affichée définies
							[!OptBase:=[!Val::[!OptEnbase!]!]!]
							[!OptHTML:=[!Val::[!OptVisible!]!]!]
						[/IF]
						// On affiche l'option
						, ["[!OptBase!]", "[!OptHTML!]"]
					[/STORPROC]
				],
				[IF [!CO!]>[!Lim!]]
					enableKeyEvents: true,
					valueField: "value",
    				displayField: "html",
					mode: "remote",
					tpl:'<tpl for="."><div class="x-combo-list-item">{html}</div></tpl>',
				[/IF]
			[/IF]
		[/CASE]
	[/SWITCH]
	[IF [!Prop::obligatoire!]=1]
		allowBlank: false,
	[/IF]
	listeners: {
		"afterrender": function( field ) {
			///////////// Classe pour alternance de couleur
			field.el.parent('div.x-form-item').addClass("LigneForm[!Cls!]");
			///////////// Fancy Upload
			[IF [!Prop::Type!]=file]
				[INFO [!Query!]|I]
				launchUpload('[!idUpload!]', '[!I::Module!]', '[!I::TypeChild!]');
			[/IF]
			////////////// Editeurs
			[IF [!Prop::Type!]=html]
				tinyMCE.execCommand('mceAddControl', false, '[!idEditor!]');
			[/IF]
			[IF [!Prop::Type!]=bbcode]
				tinyMCE.execCommand('mceAddControl', false, '[!idEditor!]');
			[/IF]
		},
		////////////// Liste plus longue que 100 -> on passe en asynchrone
		[IF [!CO!]>[!Lim!]]
			"beforerender": function( field ) {
				field.store = new Ext.data.JsonStore({
					id: "value",
					remoteSort: true,
					root: "data",
					totalProperty: "count",
					fields:[
						{name: "value", type:"string"},
						{name: "html", type:"string"}
					],
					url: "/Systeme/Interface/Options.htm?Req=[!ReqOptions!]"
				});
				field.store.setDefaultSort('Id', 'asc');
			},
			"keyup": function( field, e ) {
				this.getStore().load({params:{keywords:this.getRawValue()}});
			},
			"select":function(combo, record, index) {
				this.setValue(record.get('value'));
			},
		[/IF]
	}
}