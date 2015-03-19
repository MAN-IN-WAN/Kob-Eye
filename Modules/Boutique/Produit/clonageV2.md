[IF [!Systeme::User::Public!]]
	[MODULE Systeme/Login]
[ELSE]
	[STORPROC Boutique/Produit/374|P2|0|1][/STORPROC]

	[STORPROC [!Query!]|P|0|1]
		<h4>Clonage de [!P::Nom!]</h4>
		[!P::getCloneMathildeV2([!P2!])!]
	[/STORPROC]

<a href="http://admin.kirigami.fr/#/Boutique/Produit/[!P::Id!].htm" style="font-size:16px;margin-left:30px;">retour fiche</a>
[/IF]