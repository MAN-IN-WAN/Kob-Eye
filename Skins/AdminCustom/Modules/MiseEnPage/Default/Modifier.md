[INFO [!Query!]|I]

[SWITCH [!I::ObjectType!]|=]
        [CASE Categorie]
                [STORPROC [!Query!]|Cat|0|1][/STORPROC]
                [!back:=[!Query!]!]
                [!title:=Retour à la catégorie!]
                [!text:=Modification de la Catégorie <span id="objName">[!Cat::Nom!]</span>!]
        [/CASE]
        [CASE Article]
                [STORPROC [!Query!]|Art|0|1][/STORPROC]
                [!back:=[!Query!]!]
                [!title:=Retour à l'Article!]
                [!text:=Modification de l'Article <span id="objName">[!Art::Titre!]</span>!]
        [/CASE]
        [CASE Contenu]
                [STORPROC [!Query!]|Con|0|1][/STORPROC]
                [!Art:=[!Con::getOneParent(Article)!]!]
                [!back:=MiseEnPage/Article/[!Art::Id!]!]
                [!title:=Retour à l'Article!]
                [!text:=Modification du Contenu <span id="objName">[!Con::Titre!]</span>!]
        [/CASE]
        [CASE Colonne]
                [STORPROC [!Query!]|Col|0|1][/STORPROC]
                [!Con:=[!Col::getOneParent(Contenu)!]!]
                [!Art:=[!Con::getOneParent(Article)!]!]
                [!back:=MiseEnPage/Article/[!Art::Id!]!]
                [!title:=Retour à l'Article!]
                [!text:=Modification de la Colonne <span id="objName">[!Col::Titre!]</span>!]
        [/CASE]
        [DEFAULT]
                [!back:=MiseEnPage!]
                [!title:=Retour à la racine du module!]
                [!text:=Erreur!!]
        [/DEFAULT]
[/SWITCH]
<a href="/[!back!]" title="[!title!]" id="ModTitle">
	<h1 class="modifTitle"><span class="glyphicon glyphicon-share-alt"> </span> [!text!]</h1>
</a>

<div class="bloc">
        [MODULE MiseEnPage/Nav]
        <div id="ModContainer">
        [SWITCH [!I::ObjectType!]|=]
                [CASE Categorie]
                <div id="modCat">
                        [MODULE MiseEnPage/Categorie/ModCat]
                </div>
                [/CASE]
                [CASE Article]
                <div id="modArt">
                        [MODULE MiseEnPage/Article/ModArt]
                </div>
                [/CASE]
                [CASE Contenu]
                <div id="modCon">
                        [MODULE MiseEnPage/Contenu/ModCon]
                </div>
                [/CASE]
                [CASE Colonne]
                <div id="modCol">
                        [MODULE MiseEnPage/Colonne/ModCol]
                </div>
                [/CASE]
                [DEFAULT]
                <div id="notFound">
                        L'objet que vous tentez de modifier n'est pas reconnu.
                </div>
                [/DEFAULT]
        [/SWITCH]
        </div>
</div>