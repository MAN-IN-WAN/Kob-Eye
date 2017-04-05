[IF [!Chemin!]]
[ELSE]
[!Chemin:=[!Query!]!]
[/IF]

[STORPROC [!Chemin!]|Cat][/STORPROC]
<div MEPHeader>
    <div class="container">
        <h1>[IF [!Cat::Titre!]!=][!Cat::Titre!][ELSE][!Cat::Nom!][/IF]</h1>
    </div>
</div>
<div class="container">

    <div class="MiseEnPageModele">
        [STORPROC [!Chemin!]/Article/Publier=1|Art]
        [IF [!Art::Contenu!]!=]
            <div class="row" >
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="articleMep">
                        <div class="contenuMEP">
                            [IF [!Art::AfficheTitre!]]<h2>[!Art::Titre!]</h2>[/IF]
                            [IF [!Art::Chapo!]!=]<h3>[!Art::Chapo!]</h3>[/IF]
                        </div>
                    </div>
                </div>
            </div>
        [ELSE]
            <div class="articleMep">
                [!Art::generateDefaultLayout(0)!]
            </div>
        [/IF]

        [/STORPROC]
    </div>
</div>