{
    "items":[
    [OBJ Distributeur|Shop|SH]
    [IF [!CAT!]][!REQ_CAT:=/Categorie/Url=[!CAT!]!]
    [IF [!NB!]=][!NB:=10!][/IF]
    [IF [!PAGE!]=][!PAGE:=0!][/IF]
    [ELSE][!REQ_CAT:=!][/IF]
    [IF [!REQ_CAT!]]
        [STORPROC Distributeur[!REQ_CAT!]|CAT][!REQ_CAT:=/Categorie/[!CAT::Id!]!][/STORPROC]
    [/IF]
    [STORPROC Distributeur[!REQ_CAT!]/Shop/Longitude!=&Latitude!=|S|[!NB:*[!PAGE!]!]|[!NB!]|Id|ASC]
            [IF [!Pos!]>1],[/IF]{
                    "Id":[!S::Id!],
                    "Name":"[JSON][!S::Name!][/JSON]",
                    "Adress":"[JSON][!S::Adress!][/JSON]",
                    "Adress2":"[JSON][!S::Adress2!][/JSON]",
                    "Adress3":"[JSON][!S::Adress3!][/JSON]",
                    "PostalCode":"[JSON][!S::PostalCode!][/JSON]",
                    "City":"[JSON][!S::City!][/JSON]",
                    "Phone":"[JSON][!S::Phone!][/JSON]",
                    "Fax":"[JSON][!S::Fax!][/JSON]",
                    "Email":"[JSON][!S::Email!][/JSON]",
                    "Website":"[JSON][!S::Website!][/JSON]",
                    "Region":"[JSON][!S::Region!][/JSON]",
                    "Country":"[JSON][!S::Country!][/JSON]",
                    "CountryNew":"[JSON][!S::CountryNew!][/JSON]",
                    "Photo":"[JSON][!S::Photo!][/JSON]",
                    "CountryNew":"[JSON][!S::CountryNew!][/JSON]",
                    "Proshop":"[JSON][!S::Proshop!][/JSON]",
                    "ProshopGermany":"[JSON][!S::ProshopGermany!][/JSON]",
                    "TestCenter":"[JSON][!S::TestCenter!][/JSON]",
                    "ProSchool":"[JSON][!S::ProSchool!][/JSON]",
                    "Latitude":"[JSON][!S::Latitude!][/JSON]",
                    "Longitude":"[JSON][!S::Longitude!][/JSON]",[STORPROC Distributeur/Categorie/Shop/[!S::Id!]|C][NORESULT][!C::Nom:=NO CATEGORY!][!C::IconeMap:=!][!C::Couleur:=bleu!][/NORESULT][/STORPROC]
                    "Category":"[JSON][!C::Nom!][/JSON]",
                    "IconMarqueur":"[JSON]/[!C::IconeMap!][/JSON]",
                    "Couleur":"[JSON][!C::Couleur!][/JSON]"
            }
    [/STORPROC]
    ],
    "nbresult":"[COUNT Distributeur[!REQ_CAT!]/Shop/Longitude!=&Latitude!=|NBSHOP][!NBSHOP!]",
    "nextpage":"[!PAGE:+1!]",
    "end":"[IF [![!PAGE:+1!]:*[!NB!]!]>[!NBSHOP!]]1[ELSE]0[/IF]"
}
