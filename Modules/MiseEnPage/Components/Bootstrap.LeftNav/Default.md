[STORPROC [!Systeme::CurrentMenu::Alias!]|Base][/STORPROC]
[STORPROC [!Query!]|Art][/STORPROC]
[!nav:=[!Base::buildSubNav([!Art::Id!])!]!]
[IF [!nav!]]
<div class="MEPNav">
    [!nav!]
</div>
[/IF]
