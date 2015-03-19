// TODO List
// Afficher images
// Champs : Admin=1 (uniquement visible par un administrateur) ; Hidden = 1 (ne pas afficher) ; Auto = 1 (readonly)

{
	fieldLabel: "[!Prop::Nom!]",
	text: "[!Prop::Valeur!]",
	anchor: "97%",
	border: true,
	style: "font-size:14px",
	[SWITCH [!Prop::Type!]|=]
		[CASE boolean]
			[IF [!Prop::Valeur!]=1]
				text: "Oui",
			[ELSE]
				text: "Non",
			[/IF]
		[/CASE]
		[CASE password]
			text: "*******",
		[/CASE]
		[CASE file]
			html: '[IF [!Prop::Valeur!]!=]<img src="[!Domaine!]/[!Prop::Valeur!].mini.250x80.jpg" alt="" />[ELSE]Pas de fichier[/IF]',
		[/CASE]
		[CASE date]
			text: "[IF [!Prop::Valeur!]=0]Non défini[ELSE][!Utils::getDate(d/m/Y,[!Prop::Valeur!])!][/IF]",
		[/CASE]
	[/SWITCH]
	listeners: {
		"afterrender": function( field ) {
			// Classe pour alternance de couleur
			field.el.parent('div.x-form-item').addClass("LigneForm[!Cls!]");
		}
	}
}