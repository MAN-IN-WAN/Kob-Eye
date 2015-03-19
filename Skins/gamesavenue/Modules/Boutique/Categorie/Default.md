<!--Boutique/Categorie/Default-->
[MODULE Systeme/Structure/Gauche_Boutique]
[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H][/STORPROC]
[IF [!I::NbHisto!]=1]
	//Affichage accueil pour les catégorie de premier niveau
	[MODULE Boutique/Categorie/Accueil]
[ELSE]
	//Affichage liste des produits
	[MODULE Boutique/Produit/Liste?Chemin=[!Query!]]
[/IF]