
<div id="listNews">
    <div id="newsTri">
        <div class="container">
            <a href="" data-filter="*"><h1 id="listNews">Toute l'actualité du groupe</h1></a>
            [STORPROC Abtel/Entite|Ent]
            [IF [!Ent::CodeGestion!]!=00]
            <a href="" data-filter=".[!Ent::CodeGestion!]" style="color:[!Ent::CodeCouleur!]">[!Ent::Nom!]</a>
            [/IF]
            [/STORPROC]
        </div>
    </div>
    <div id="newsDisplay"  class="container">
        [STORPROC [!Query!]/Categorie/*/Article/Publier=1|MEP]
        [STORPROC MiseEnPage/Categorie/Article/Id=[!MEP::Id!]|Cat][/STORPROC]
        [!Ent:=[!Cat::getOneChild(Entite)!]!]
        <div class="news [!Ent::CodeGestion!] row">
            <a href="[!MEP::getUrl()!]" class="row">
                <div class="col-md-2">
                    <img src="[IF [!MEP::Image!]!=][!MEP::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/Abtel-Mediterranee.svg[/IF]" class="img-responsive">
                </div>
                <div class="col-md-10">
                    //<p class="newsListEnt" style="color:[!Ent::CodeCouleur!]">[!Cat::Nom!]</p>
                    <h3>[!MEP::Titre!] - <span class="newsListEnt" style="color:[!Ent::CodeCouleur!]">[!Cat::Nom!]</span></h3>
                    <div>
                        [SUBSTR 1000][!MEP::Chapo!][/SUBSTR]
                    </div>
                </div>
            </a>
        </div>
        [/STORPROC]
    </div>
</div>



<script type="text/javascript">
    $(document).ready(function () {
        $('a.zoombox').zoombox({
            theme : 'darkprettyphoto',
            opacity     : 0.8,
            duration    : 800,              // Animation duration
            animation   : true,             // Do we have to animate the box ?
            width       : 600,              // Default width
            height      : 400,              // Default height
            gallery     : true,             // Allow gallery thumb view
            autoplay : false                // Autoplay for video
        });
    });

    $('#newsDisplay').isotope({
        itemSelector: '.news',
        layoutMode: 'fitRows'
    });
    $('#newsTri a').on('click', function(e){
        e.preventDefault();

        $('#newsDisplay').isotope({filter:$(this).data('filter')});
    });
</script>



//theme 	'zoombox'
// zoombox, lightbox, prettyphoto, darkprettyphoto, simple
//opacity 	0.8 	Page overlay opacity
//duration 	800 	Zoombox opening animation duration (ms)
//animation 	true 	Set it to false if you don't want width/height animation, zoombox will be directly appended to body and displayed
//width 	600 	Default width for videos and iframes (image size is automatically detected)
////height 	400 	Default height for videos and iframes (image size is automatically detected)
//gallery 	true 	If set to true zoombox will display a gallery of images thumbs
//autoplay 	false 	Autoplay video opened with zoombox
//overflow 	false 	Allow
