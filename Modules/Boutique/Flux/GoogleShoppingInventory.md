<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
    <channel>
[STORPROC Boutique/Magasin/[!CurrentMagasin::Id!]/Categorie/*/Produit/Actif=1|Prod|0|100]
        [STORPROC [!Prod::getParents(Marque)!]|Marque|0|1]
        <item>
            <g:id>[!Prod::Reference!]</g:id>
            [IF [!Prod::StockReference!]]
            <g:availability>in stock</g:availability>
            [ELSE]
            <g:availability>out of stock</g:availability>
            [/IF]
            <g:price>[!Prod::getTarif()!] [!CurrentDevise::Code!]</g:price>
        </item>
        [/STORPROC]
[/STORPROC]
    </channel>
</rss>