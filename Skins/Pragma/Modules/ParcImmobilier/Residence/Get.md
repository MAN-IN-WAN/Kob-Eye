{ 
	identifier: 'id',
	label: 'label',
	items: [
		[STORPROC ParcImmobilier/Residence/Reference=1|Z|0|1000|DateLivraison|ASC]
			[IF [!Pos!]>1],[/IF]{ id:'[!Z::Id!]',[STORPROC [!Z::Proprietes()!]|P][IF [!Pos!]>1],[/IF][!P::Nom!]:'[URL][!P::Valeur!][/URL]'[/STORPROC]
			,Images:[
				[STORPROC ParcImmobilier/Residence/[!Z::Id!]/Donnee/Type=References|I][IF [!Pos!]>1],[/IF]'[!I::URL!]'[/STORPROC]
			],
			Ville:[STORPROC ParcImmobilier/Ville/Residence/[!Z::Id!]|R]'[URL][!R::Nom!][/URL]'[/STORPROC],
			Departement:[STORPROC ParcImmobilier/Departement/Ville/[!R::Id!]|R]'[URL][!R::Nom!][/URL]'[/STORPROC]
			}
		[/STORPROC]
	]
}
