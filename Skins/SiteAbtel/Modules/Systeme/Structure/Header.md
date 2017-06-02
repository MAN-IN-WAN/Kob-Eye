[OBJ Systeme|Site|Sit]
[!CurSite:=[!Sit::getCurrentSite()!]!]
[!EntiteSite:=[!CurSite::getOneChild(Entite)!]!]

<a href="/" alt="Abtel" title="Abtel" id="abtelMainLogo">
        <div class="col-md-2 col-sm-2 col-xs-5" id="logoAbtel"><img src="/[!EntiteSite::Logo!]" id="logoHead"></div>
</a>
<div class="col-md-10 col-sm-10 col-xs-7 pull-right" id="menuTop">
        [MODULE Systeme/Menu/menuTop]
</div>
