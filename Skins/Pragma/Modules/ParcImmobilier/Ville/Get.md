{ 
	identifier: 'id',
	label: 'label',
	items: [
		{ id:'-1',Nom:'Toutes les villes'}
		[STORPROC ParcImmobilier/Ville|Z|0|1000]
			[COUNT ParcImmobilier/Ville/[!Z::Id!]/Residence/CategorieId=5|C]
			[IF [!C!]>0]
				,{ id:'[!Z::Id!]',[STORPROC [!Z::Proprietes()!]|P][IF [!Pos!]>1],[/IF][!P::Nom!]:'[URL][!P::Valeur!][/URL]'[/STORPROC]}
			[/IF]
		[/STORPROC]
	]
}
