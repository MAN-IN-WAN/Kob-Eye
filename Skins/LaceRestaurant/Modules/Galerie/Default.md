
//GALERIE
[HEADER CSS]Skins/LaceRestaurant/Js/justifiedGallery.min.css[/HEADER]
<section>
    <article>
        <header class="ico"><h1>
            Les photos
        </h1></header>
        <div id="mygallery" >
            [STORPROC Galerie/Media|M]
            <a href="/[!M::Fichier!]">
                <img alt="[!M::Titre!]" src="/[!M::Fichier!]"/>
            </a>
            [/STORPROC]
            <!-- other images... -->
        </div>
        <script>
            $(function () {
                $("#mygallery").justifiedGallery();
            })
        </script>

    </article>
</section>

<!-- galerie -->
<script type="text/javascript" src="/Skins/LaceRestaurant/Js/jquery.justifiedGallery.min.js"></script>
