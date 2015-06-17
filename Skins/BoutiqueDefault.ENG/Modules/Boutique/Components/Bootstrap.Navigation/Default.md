[INFO [!Lien!]|I]
[INFO [!Query!]|J]
[IF [!Niveau!]=][!Niveau:=1!][/IF]
//recherche du magasin en cours
[IF [!J::Module!]!=Boutique]
    [!Menus:=[!Sys::searchInMenus(Alias,Boutique/Magasin/[!CurrentMagasin::Id!]/Categorie)!]!]
    [IF [!UtilsArray::SizeOf([!Menus!])!]=]
            [STORPROC Boutique/Magasin/[!CurrentMagasin::Id!]/Categorie|Cat|0|1]
                [!Menus:=[!Sys::searchInMenus(Alias,Boutique/Magasin/[!CurrentMagasin::Id!]/Categorie/[!Cat::Id!])!]!]
            [/STORPROC]
    [/IF]
[ELSE]
        [!Menus:::=[!Sys::CurrentMenu!]!]
[/IF]
[STORPROC [!Menus!]|Menu|0|1]
    [!MENU:=/[!Menu::Url!]!]
    [STORPROC [!I::Historique!]|H|[!Niveau!]|1][/STORPROC]
    <div id="" class="block navigation">
            <h3 class="title_block">[!TITRE!]</h3>
            <div class="block_content">
                    <ul class="">
                    [STORPROC [!Menu::Alias!]/Actif=1|Cato|0|20|Ordre|ASC]
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
