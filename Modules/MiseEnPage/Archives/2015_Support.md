


[STORPROC [!Query!]|Cat|0|1]
	<div id="support">
                <h1>[!Cat::Titre!]</h1>
		[IF [!Cat::Description!]!=]
                        <div class="descCat">[!Cat::Description!]</div>
		[/IF]
		[STORPROC [!Query!]/Article/Publier=1|Art|||Ordre|ASC]
                        <div class="artSupport row">
                                [IF [!Art::AfficheTitre!]=1]
                                        <h2 class="col-md-12">[!Art::Titre!]</h2>
                                [/IF]
                                <div class="artContenu col-md-12">[!Art::Contenu!]</div>
                                <div class="artContenu col-md-6">
                                        [STORPROC [!Query!]/Article/[!Art::Id!]/Fichier|File|||Ordre|ASC]
                                                <a href="[!File::URL!]">[!File::Titre!]</a>
                                        [/STORPROC]
                                </div>
                                <div class="artContenu col-md-6">
                                        [STORPROC [!Query!]/Article/[!Art::Id!]/Lien|Link|||Ordre|ASC]
                                                <a href="[!Utils::urlClear([!Link::URL!])!]" [IF [!Link::Type!]=Externe]target="_blank"[/IF]>[!Link::Titre!]</a>
                                        [/STORPROC]
                                </div>
                        </div>    
		[/STORPROC]
	</div>
[/STORPROC]