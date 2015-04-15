<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
    <channel>
[STORPROC Boutique/Magasin/[!CurrentMagasin::Id!]/Categorie/*/Produit/Actif=1|Prod|0|100]
        [STORPROC [!Prod::getChildren(Marque)!]|Marque|0|1]
        <item>
            <g:id>[!Prod::Reference!]</g:id>
            <g:mpn>[!Prod::Reference!]</g:mpn>
            <title>[!Prod::Nom!]</title>
            <description>[UTIL NOHTML][!Prod::Description!][/UTIL]</description>
            <link>[!Prod::getUrl()!]</link>
            <g:image_link>[!Domaine!]/[!Prod::Image!]</g:image_link>
            <g:condition>new</g:condition>
            [IF [!Prod::StockReference!]]
            <g:availability>in stock</g:availability>
            [ELSE]
            <g:availability>out of stock</g:availability>
            [/IF]
            <g:availability_date>[!Utils::getDate(c,[!TMS::Now:+200000!])!]</g:availability_date>
            <g:price>[!Prod::getTarif()!] [!CurrentDevise::Code!]</g:price>
            <g:brand>[!Marque::Nom!]</g:brand>
            <g:shipping>
                <g:country>FR</g:country>
                <g:service>Standard</g:service>
                <g:price>0 EUR</g:price>
            </g:shipping>
            [IF [!Prod::GoogleProductCategory!]]
            <g:google_product_category>[UTIL SPECIALCHARS][!Prod::GoogleProductCategory!][/UTIL]</g:google_product_category>
            [/IF]
            <g:product_type>[UTIL SPECIALCHARS][!Prod::getCategoryString()!][/UTIL]</g:product_type>
        </item>
        [/STORPROC]
[/STORPROC]
    </channel>
</rss>