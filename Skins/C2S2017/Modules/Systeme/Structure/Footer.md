<div class="container">
        <div class="row">
                <div class="col-md-3 col-sm-4 col-xs-6 bCol">
                        [STORPROC Redaction/Categorie/16/Categorie/Publier=1|Cat|1|2]
                                [IF [!Pos!]!=1]<div class="spacer"></div>[/IF]
                                <h5>[IF [!Cat::Titre!]][!Cat::Titre!][ELSE][!Cat::Nom!][/IF]</h5>
                                [STORPROC Redaction/Categorie/[!Cat::Id!]/Article|Art]
                                            <a href="[!Art::getUrl()!]" alt="[!Art::Titre!]">[!Art::Titre!]</a>  
                                [/STORPROC]
                                [STORPROC Redaction/Categorie/[!Cat::Id!]/Categorie|Cat]
                                    <a href="[!Cat::getUrl()!]" alt="[!Cat::Nom!]">[!Cat::Nom!]</a>  
                                [/STORPROC]
                        [/STORPROC]
                </div>
                <div class="col-md-3 col-sm-4 col-xs-6 bCol">
                        [STORPROC Redaction/Categorie/16/Categorie/Publier=1|Cat|3|2]
	                        [IF [!Pos!]!=1]<div class="spacer"></div>[/IF]
                            <h5>[!Cat::Nom!]</h5>
                            [STORPROC Redaction/Categorie/[!Cat::Id!]/Article|Art]
    							<a href="[!Art::getUrl()!]" alt="[!Art::Titre!]">[!Art::Titre!]</a>  
                            [/STORPROC]
                            [STORPROC Redaction/Categorie/[!Cat::Id!]/Categorie|Cat]
                                <a href="[!Cat::getUrl()!]" alt="[!Cat::Nom!]">[!Cat::Nom!]</a>  
                            [/STORPROC]
                        [/STORPROC]
                </div>
                <div class="col-md-3 col-sm-4 col-xs-6 bCol last">
                    [STORPROC Redaction/Categorie/7|Cat]
                    	<h5>[!Cat::Titre!]</h5>
                        [STORPROC Redaction/Categorie/[!Cat::Id!]/Article/ALaUne=1&Publier=1|Art]
                        	<a href="[!Art::getUrl()!]" alt="[!Art::Titre!]">[IF [!Art::SousTitre!]][!Art::SousTitre!][ELSE][!Art::Titre!][/IF]</a>
                        [/STORPROC]
                     [/STORPROC]
                     <a href="/la-presse" alt="la presse en parle">La presse en parle</a>
                     <a href="/Nos-partenaires" alt="les partenaires">Les partenaires</a>
					[STORPROC [!Systeme::Menus!]/Affiche=1&MenuBas=1|M]
						[IF [!Pos!]=1]<h5>Divers</h5>[/IF]
                        <a href="/[!M::Url!]" alt="[!M::Titre!]">[!M::Titre!]</a>
			        [/STORPROC]
                        
                </div>
                <div class="col-md-3 col-sm-12 col-xs-6 bCol bColFin">
                        <div class="col-md-6">
                                <div class="row">
                                        <a href="/"><img src="/Skins/C2S/Img/miniC2S.png"/></a>
                                </div>
                                <div class="row">
                                        <a href="https://www.facebook.com/pages/C2S-Services-%C3%A0-la-Personne/1452562601651092" target="_blank" class="inline"><img src="/Skins/C2S/Img/facebook__7C8E47BD_.png"/></a>
                                        <a href="http://twitter.com/C2SSERVICES" target="_blank" class="inline"><img src="/Skins/[!Systeme::Skin!]/Img/twitter__3BCA94B0_.png"/></a>
                                </div>
                        </div>
                        <div class="col-md-6">
                                <div class="row">
                                        <img src="/Skins/C2S/Img/C2S_QRCODE.png">
                                </div>
                        </div>
                        <div class="clear"></div>
                        <div id="gMaps">
                                <img src="/Skins/C2S/Img/GoogleMaps2.png">
                        </div>
                        <div id="AdresseBasDePage">
							<br /><strong>[!Systeme::User::Nom!]</strong>
							<br />[!Systeme::User::Adresse!]<br />[!Systeme::User::CodPos!] [!Systeme::User::Ville!] - [!Systeme::User::Pays!]
							<br />Tel : [!Systeme::User::Tel!] - Fax : [!Systeme::User::Fax!]
							<br /><a href="/Contact" title="nous contacter"><img src="/Skins/[!Systeme::Skin!]/Img/blocmail-blanc.png" alt="nous contacter" title="nous contacter" /></a>
                             
                        </div>
                        
                </div>
        </div>
</div>