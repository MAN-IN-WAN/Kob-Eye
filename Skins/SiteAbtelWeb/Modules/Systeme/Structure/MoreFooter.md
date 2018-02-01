
<div id="plusfooter">
    <div id="divmap">
        <div id="mapfooter">
            <iframe id="iframefooter" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d46080.56660669501!2d4.3697939480563495!3d43.792878205528034!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xb8dc56a9efeb9743!2sAbtel+Informatique!5e0!3m2!1sfr!2sfr!4v1495026817055" width="1140" height="300" frameborder="0" style="border:0" allowfullscreen></iframe>
        </div><div id="adressemap">
                <span class="adressefooter1">Abtel Méditerranée</span>
                <span id="iconefooter1" class="adressefooter2">Adresse:</span>
                <span class="ssicone adressefooter2">Groupe Delta Km 4</span>
                <span class="ssicone adressefooter2">Route Départementale, D6113</span>
                <span class="ssicone adressefooter2">30230 Bouillargues</span>
                <span id="iconefooter2"class="adressefooter2">04 66 04 06 13</span>
            <span class="adressefooter2"></span>
            <span id="iconefooter3"class="adressefooter2">Ouvert de 8h à 19h</span>
            </div>
    </div>
    <div class="container">
        <nav class="col-lg-3 col-sm-6">
            <a href="https://agence-web.abtel.fr/references" class="grandsliens" id="imgnosrefs">
            Nos références
            </a>
        </nav>
        <nav class="col-lg-3 col-sm-6">
            <a href="https://agence-web.abtel.fr/developpement-services" class="grandsliens" id="imgdevweb">
            Développement Web
            </a>
        </nav>
        <nav class="col-lg-3 col-sm-6">
            <a href="https://agence-web.abtel.fr/hebergement-services" class="grandsliens" id="imghebergement">
            Hébergement et services
            </a>
        </nav>
        <nav class="col-lg-3 col-sm-6">
            <a href="https://agence-web.abtel.fr/actualite" class="grandsliens" id="actualite">
            Actualité
            </a>
        </nav>
    </div>


    <div class="container">
        <nav class="col-lg-3 col-sm-6">
            <a href="https://www.abtel.fr/" class="grandsliens2" id="imggroupe">

            </a>
        </nav>
            <nav class="col-lg-3 col-sm-6">
            <a href="https://informatique.abtel.fr/" class="grandsliens2"  id="imginformatique">

            </a>
        </nav>
            <nav class="col-lg-3 col-sm-6">
            <a href="https://networks.abtel.fr/" class="grandsliens2" id="imgnetwork">

            </a>
        </nav>
        <nav class="col-lg-3 col-sm-6">
            <a href="https://formation.abtel.fr/" class="grandsliens2" id="imginformation">

            </a>
        </nav>


    </div>



    <div class="separefootergris"></div>

    <nav>
        [STORPROC [!Systeme::Menus!]/Affiche=1&MenuSpecial=1&MenuPrincipal=0|M|0|10|Ordre|ASC]
        [IF [!M::Url!]~http]
        <a href="[!M::Url!]" target="_blank" class="liensbasfooter [IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF]"  >
            [ELSE]
            <a href="/[!M::Url!]" class="liensbasfooter [IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF]" [IF [!Pos!]=[!NbResult!]]style="background-color:[!EntiteSite::CodeCouleur!];"[/IF] >
                [/IF]
                [!M::Titre!] [!M::SousTitre!]
            </a>
            [/STORPROC]
    </nav>

</div>

