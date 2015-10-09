[INFO [!Lien!]|I]
[INFO [!Query!]|J]
[IF [!Niveau!]=][!Niveau:=1!][/IF]

[STORPROC Boutique/Magasin/[!CurrentMagasin::Id!]/Categorie|Cat|0|10]
[!Mt:=[!Sys::searchInMenus(Alias,Boutique/Categorie/[!Cat::Id!])!]!]
[STORPROC [!Mt!]|t]
[!Menus:::=[!t!]!]
[/STORPROC]
[/STORPROC]



[STORPROC [!Menus!]|Menu|0|10]
    [!MENU:=/[!Menu::Url!]!]
    [STORPROC [!I::Historique!]|H|[!Niveau!]|1][/STORPROC]
    <div class="block">
            <h3 class="title_block">[!TITRE!]</h3>
            <div class="block_content">
                    <ul class="navigation">
                    [STORPROC [!Menu::Alias!]/Categorie/Actif=1|Cato|0|20|Ordre|ASC]
                            <li>
                                    [IF [!Cato::Url!]=[!H::Value!]]
                                            <a href="[!MENU!]/[!Cato::Url!]" [IF [!Lien!]~[!Cato::Url!]]class="selected"[/IF]>[!Cato::Nom!]</a>
                                            [COMPONENT Boutique/Bootstrap.Navigation/SNavigation?Url=[!MENU!]/[!Cato::Url!]&CatId=[!Cato::Id!]&Niveau=2]
                                    [ELSE]
                                            <a href="[!MENU!]/[!Cato::Url!]" [IF [!Lien!]~[!Cato::Url!]]class="selected"[/IF]>[!Cato::Nom!]</a>
                                    [/IF]
                            </li>
                    [/STORPROC]
                    </ul>
    
            </div>
    </div>
[/STORPROC]
