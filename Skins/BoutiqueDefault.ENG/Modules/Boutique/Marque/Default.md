[INFO [!Query!]|I]
//[IF [!I::TypeSearch!]=Child]
[IF [!I::TypeSearch!]=Childdsfsf]
    [COUNT Boutique/Marque|Nb]
    <div class="contenttop row-fluid block">
        <h1 class="title_block"> [IF [!Cat::NomLong!]][!Cat::NomLong!][ELSE][!Cat::Nom!][/IF] <span class="resumecat category-product-count"> / __THERE_IS__ [!Nb!] __BRANDS__. </span></h1>
        <ul>
        [STORPROC Boutique/Marque|M]
            <li>
                <a href="/Marque/[!M::Url!]">[!M::Nom!]</a>
            </li>
        [/STORPROC]
        </ul>
    </div>


[ELSE]
//	[MODULE Boutique/Categorie?Chemin=[!Query!]]
[/IF]