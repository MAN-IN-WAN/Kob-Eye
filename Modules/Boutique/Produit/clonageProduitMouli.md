[IF [!Systeme::User::Public!]]
	[MODULE Systeme/Login]
[ELSE]
	[STORPROC [!Query!]|P|0|1]
		<h1>Clonage de [!P::Nom!]</h1>
 		//[!Query!]
		[STORPROC Boutique/Produit/374|P2|0|1]
			[!P::getCloneMathilde([!P2!])!]
		[/STORPROC]
	[/STORPROC]

<a href="http://admin.kirigami.fr/#/Boutique/Produit/[!P::Id!].htm" style="font-size:16px;margin-left:30px;">retour fiche</a>
[/IF]