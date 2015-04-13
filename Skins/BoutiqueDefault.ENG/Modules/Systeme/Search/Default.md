<div class="container" style="padding:20px;">
    <h1>RECHERCHE</h1>
    [STORPROC [!Systeme::getSearch([!Search!])!]|TL]
    [NORESULT]
    <h3>__NO_RESULT__</h3>
    [/NORESULT]
    <div class="media">
        <a class="pull-left" href="[!TL::Url!]" style="display: block;width: 75px;height: 75px;background-color: #00CC99;">
            [IF [!TL::Image!]]
            <img src="/[!TL::Image!].mini.75x75.jpg" />
            [/IF]
        </a>
        <div class="media-body">
            <a class="pull-left" href="[!TL::Url!]">
                <h4 class="media-heading">[!TL::Title!]</h4>
                <p>[SUBSTR 0|200][!O::Description!][/SUBSTR]</p>
            </a>
        </div>
    </div>
    [/STORPROC]
</div>