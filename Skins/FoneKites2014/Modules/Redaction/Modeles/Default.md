// Modele qui n'affiche pas le contenu des articles en entier
[INFO [!Lien!]|I]
//Recherche du menu racine
[STORPROC [!I::Historique!]|LaRacine|0|1][/STORPROC]


[STORPROC [!Query!]|Cat]
        [STORPROC [!Query!]/Article/Publier=1|Art]
		//COULEUR
		[IF [!Utils::isPair([!Pos!])!]]
		    [!Couleur:=gris!]
		[ELSE]
		    [!Couleur:=gris-clair!]
		[/IF]
                [MODULE Redaction/ArticleModeles/[!Art::Modele!]?Art=[!Art!]&Couleur=[!Couleur!]]
        [/STORPROC]
[/STORPROC]