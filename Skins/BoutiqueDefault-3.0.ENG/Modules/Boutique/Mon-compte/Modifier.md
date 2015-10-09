[IF [!Systeme::User::Public!]=1][REDIRECT][!Systeme::getMenu(Systeme/User)!][/REDIRECT][/IF]
<div class="block">
    <h3 class="title_block">Mon Compte</h3>
    [MODULE Boutique/Mon-compte/Home]

    <h3 class="title_block">Modifier mes données</h3>
    <div class="ColonneCreationCompte">
        [MODULE Systeme/Login/Inscription?Redirect=[!Systeme::getMenu(Boutique/Mon-compte)!]]
    </div>
</div>