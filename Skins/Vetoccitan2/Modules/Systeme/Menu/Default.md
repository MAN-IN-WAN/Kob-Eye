

[STORPROC [!Systeme::Menus!]/MenuPrincipal=1&Affiche=1|M]
	<ul class="Menu0 cssMenu" id="burger">
        [LIMIT 0|100]
            <li class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] Current [/IF] [IF [!Pos!]=1] First [/IF] [IF [!Pos!]=[!NbResult!]] Last [/IF]">
                [IF [!M::Url!]~http]  
                [IF [!Lien!]=]<p onClick="animAccueil(1)">[!M::Titre!]</p>[ELSE]<a href="[!M::Url!]" target="_blank">[!M::Titre!]</a>[/IF]     
                [ELSE]                  
                    [IF [!Lien!]=]
                        [IF[!M::Url!]=Accueil]
                            <p style="cursor:pointer" onClick="animAccueil(1)">[!M::Titre!]</p>
                        [/IF]
                        [IF[!M::Url!]=clinique]
                           <p style="cursor:pointer" onClick="animAccueil(2)">[!M::Titre!]</p>
                        [/IF]
                        [IF[!M::Url!]=equipe]
                           <p style="cursor:pointer" onClick="animAccueil(3)">[!M::Titre!]</p>
                        [/IF]
                        [IF[!M::Url!]=services]
                           <p style="cursor:pointer" onClick="animAccueil(4)">[!M::Titre!]</p>
                        [/IF]
                        [IF[!M::Url!]=conseils]
                           <p style="cursor:pointer" onClick="animAccueil(5)">[!M::Titre!]</p>
                        [/IF]
                        [IF[!M::Url!]=eshop]
                           <p style="cursor:pointer" onClick="animAccueil(6)">[!M::Titre!]</p>
                        [/IF]
                        [IF[!M::Url!]=news]
                           <p style="cursor:pointer" onClick="animAccueil(7)">[!M::Titre!]</p>
                        [/IF] 
                        [IF[!M::Url!]=contact]
                           <p style="cursor:pointer" onClick="animAccueil(8)">[!M::Titre!]</p>
                        [/IF]    
                        [IF[!M::Url!]=accesclient]
                           <p style="cursor:pointer" onClick="animAccueil(9)">[!M::Titre!]</p>
                        [/IF]
                        [ELSE]
                        <a href="/[!M::Url!]" >[!M::Titre!]</a>                    
                    [/IF] 
                    [IF [!M::Alias!]~Redaction]
                        [STORPROC [!M::Alias!]/Categorie/Publier=1|SCat]
                            <ul class="Menu1 cssMenu">
                                [LIMIT 0|100]
                                    <li>                                       
                                        <a href="/[!M::Url!]/[!SCat::Url!]">[!SCat::Nom!]</a>
                                        [STORPROC Redaction/Categorie/[!SCat::Id!]/Categorie/Publier=1|SCat2]
                                            <div class="Menu2 cssMenu">
                                                [LIMIT 0|100]
                                                        <a href="/[!M::Url!]/[!SCat::Url!]/[!SCat2::Url!]">[!SCat2::Nom!]</a><br>
                                                [/LIMIT]
                                            </div>
                                        [/STORPROC]
                                    </li>
                                [/LIMIT]
                            </ul>
                        [/STORPROC]
                    [/IF]
                [/IF]
            </li>
        [/LIMIT]
	</ul>
[/STORPROC]