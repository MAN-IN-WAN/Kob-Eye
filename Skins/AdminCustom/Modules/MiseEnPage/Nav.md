[INFO [!Query!]|I]



[IF [!I::ObjectType!]=Categorie]
<div id="ModNav">
        [IF [!I::TypeSearch!]=Child]
                <p>Categories Principales</p>
        [ELSE]
                [STORPROC [!Query!]|Cat][/STORPROC]
                [!arbo:=[!Cat::getAncestry()!]!]
                <p><a href="/MiseEnPage/Categorie">Categories Principales</a> >
                [STORPROC [!arbo!]|lCat]
                        [IF [!Pos!]!=[!NbResult!]]
                                <a href="/MiseEnPage/Categorie/[!lCat::Url!]" title="[!lCat::Nom!]">[!lCat::Nom!]</a> > 
                        [ELSE]
                                [!lCat::Nom!]
                        [/IF]
                [/STORPROC]
                </p>
        [/IF]
</div>
[/IF]
[IF [!I::ObjectType!]=Article]
<div id="ModNav">
        [IF [!I::TypeSearch!]=Child]
                <p>Ensemble des articles</p>
        [ELSE]
                [STORPROC [!Query!]|Art][/STORPROC]
                [!Cat:=[!Art::getOneParent(Categorie)!]!]
                [!arbo:=[!Cat::getAncestry()!]!]
                <p><a href="/MiseEnPage/Categorie">Categories Principales</a> >
                [STORPROC [!arbo!]|lCat]
                        <a href="/MiseEnPage/Categorie/[!lCat::Url!]" title="[!lCat::Nom!]">[!lCat::Nom!]</a>
                         > 
                [/STORPROC]
                [!Art::Titre!]
                </p>
        [/IF]
</div>
[/IF]
