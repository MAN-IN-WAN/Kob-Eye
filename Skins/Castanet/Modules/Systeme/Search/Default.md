<div class="contenttop row-fluid block">

    <h3 class="title_block"> Recherche <span class="resumecat category-product-count"> / [!search!] </span></h3>
    <div class="block-search">
        <h1>RECHERCHE</h1>
        [STORPROC [!Systeme::getSearch([!search!])!]|TL]
        [NORESULT]
        <h3>__NO_RESULT__</h3>
        [/NORESULT]
        <div class="media">
            <a class="pull-left" href="[!TL::Url!]" style="display: block;width: 75px;height: 75px;background-color: #f4f4f4;">
                [IF [!TL::Image!]]
                <img src="/[!TL::Image!].mini.75x75.jpg" />
                [/IF]
            </a>
            <div class="media-body">
                <a class="pull-left" href="[!TL::Url!]">
                    <h4 class="media-heading">[!TL::Title!]</h4>
                    <p>[!TL::Description!]</p>
                </a>
            </div>
        </div>
        [/STORPROC]
    </div>
</div>