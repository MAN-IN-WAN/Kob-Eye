[
    [STORPROC Boutique/Produit/~[!q!]|TL|0|10]
        [NORESULT]
        [/NORESULT]
[IF [!Pos!]>1],[/IF]{
            "id": [!TL::Id!],
            "label": "[JSON][!TL::Nom!][/JSON]",
            "value" : "[!TL::getUrl()!]",
            "image" : "/[!TL::Image!].mini.75x75.jpg"
        }
[/STORPROC]
]