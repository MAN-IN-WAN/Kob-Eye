[
	[STORPROC [!Systeme::Menus!]|M]
	[IF [!Pos!]>1],[/IF]{
		title: "[!M::Titre!]",
		items: [
			[STORPROC [!M::Menus!]|M2]
			[!L:=[!M::Url!]/[!M2::Url!]!]
			[IF [!Pos!]>1],[/IF] {
				xtype: "box",
				autoEl: {
					tag: "a",
					href: "[!L!].htm",
					children: [
						{
							tag: "img",
							src: "/Skins/[!Systeme::Skin!]/Icons/agt_web.png"
						},{
							tag: "span",
							class: "IconColumn",
							html: "[!M2::Titre!]"
						}
					]
				},
				text: "[!M2::Titre!]",
				listeners: {
					render: function (item) {
						item.getEl().on('click', function (e) {
							e.preventDefault();
							openTab("[!L!].htm", "[!M2::Titre!]");
							return false;
						}, item)
					}
				}
			}
			[/STORPROC]
		]
	}
	[/STORPROC]
]