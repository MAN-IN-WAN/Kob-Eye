


[STORPROC [!Query!]|Cat|0|1]
	<div id="support">
                <div id="supportHead">
                        <div class="container">
                                <h1>[!Cat::Titre!]</h1>
                                [IF [!Cat::Description!]!=]
                                        <div class="descCat">[!Cat::Description!]</div>
                                [/IF]
                        </div>
                </div>
                [STORPROC Abtel/Entite/CodeGestion=AI|Ent][/STORPROC]
                <div id="supportInfo" style="background-color:[!Ent::CodeCouleur!]">
                        <div class="container">
                                <div class="supportCustom">
                                        <h2>Votre adresse IP</h2>
                                        <p>Vous êtes connecté depuis l'adresse : <span id="supportIP">[!SERVER::REMOTE_ADDR!]</span></p>
                                </div>
                                <div class="supportCustom">
                                        <h2>BlackList</h2>
                                        <p>Vous rencontrez des problèmes d'envoi de mail ? Vérifiez la réputation de votre adresse IP :  <a target="_blank" href="http://www.anti-abuse.org/multi-rbl-check-results/?host=[!SERVER::REMOTE_ADDR!]">Anti-abuse.org</a></p>
                                </div>
                        </div>
                </div>
                <div class="container">
                        <div id="supportContent">
                        [STORPROC [!Query!]/Article/Publier=1|Art|||Ordre|ASC]
                                <div class="artSupport row">
                                        [!cols:=12!]
                                        [STORPROC [!Query!]/Article/[!Art::Id!]/Image|Img|0|1|Ordre|ASC]
                                                [!cols-=1!]
                                                <div  class="col-md-1">
                                                        <img src="[!Img::URL!]" alt="[!Img::Titre!]" title="[!Img::Titre!]" class="img-responsive">      
                                                </div>
                                                [NORESULT]
                                                        //Image par defaut ?
                                                        //<div  class="col-md-1">
                                                        //        <img src="[!Img::URL!]" alt="[!Img::Titre!]" title="[!Img::Titre!]" class="img-responsive">      
                                                        //</div>
                                                [/NORESULT]
                                        [/STORPROC]
                                        
                                        [IF [!Art::AfficheTitre!]=1]
                                                <h2 class="col-md-[!cols!]">[!Art::Titre!]</h2>
                                        [/IF]
                                        <div class="artContenu col-md-12">[!Art::Contenu!]</div>
                                        
                                        [STORPROC [!Query!]/Article/[!Art::Id!]/Fichier|File|||Ordre|ASC]
                                                <div class="artFile">
                                                        <a href="[!File::URL!]">[!File::Titre!]</a>
                                                </div>        
                                                [NORESULT]
                                                [/NORESULT]
                                        [/STORPROC]
                                        
                                        [STORPROC [!Query!]/Article/[!Art::Id!]/Lien|Link|||Ordre|ASC]
                                                <div class="artLink">
                                                        <a href="[!Utils::urlClear([!Link::URL!])!]" [IF [!Link::Type!]=Externe]target="_blank"[/IF]>[!Link::Titre!]</a>
                                                </div>
                                                [NORESULT]
                                                [/NORESULT]
                                        [/STORPROC]
                                </div>    
                        [/STORPROC]
                </div>
                </div>
	</div>
[/STORPROC]