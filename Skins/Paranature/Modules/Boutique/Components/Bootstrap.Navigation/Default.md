[INFO [!Lien!]|I]
[INFO [!Query!]|J]
[IF [!Niveau!]=][!Niveau:=1!][/IF]
[!Menu:=[!Systeme::CurrentMenu!]!]
[IF [!MENUID!]]
    [STORPROC Systeme/Menu/[!MENUID!]|Menu|0|1][/STORPROC]
[ELSE]
    [IF [!Module::Actuel::Nom!]!=Boutique][!DISPLAY:=0!][/IF]
    [!Menu:=[!Systeme::CurrentMenu!]!]
    [!MENU:=[!Systeme::CurrentMenu::Url!]!]
[/IF]

//[IF [!J::Module!]=Boutique]
    [STORPROC [!I::Historique!]|H|[!Niveau!]|1][/STORPROC]
    <div id="" class="block">
            <h3 class="title_block">[!TITRE!]</h3>
            <div class="block_content">
                    <ul class="">
                    [STORPROC [!Menu::Alias!]/Categorie/Actif=1|Cato|0|20|Ordre|ASC]
                            <li>
                                    [IF [!Cato::Url!]=[!H::Value!]]
                                            <a href="/[!MENU!]/[!Cato::Url!]" [IF [!Lien!]~[!Cato::Url!]]class="selected"[/IF]>[!Cato::Nom!]</a>
                                            [COMPONENT Boutique/Bootstrap.Navigation/SNavigation?Url=/[!MENU!]/[!Cato::Url!]&CatId=[!Cato::Id!]&Niveau=2]
                                    [ELSE]
                                            <a href="/[!MENU!]/[!Cato::Url!]" [IF [!Lien!]~[!Cato::Url!]]class="selected"[/IF]>[!Cato::Nom!]</a>
                                    [/IF]
                            </li>
                    [/STORPROC]
                    </ul>
    
            </div>
    </div>
//[/IF]