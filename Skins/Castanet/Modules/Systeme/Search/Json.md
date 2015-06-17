[
    [STORPROC [!Systeme::getSearch([!q!])!]|TL|0|10]
        [NORESULT]
        [/NORESULT]
[IF [!Pos!]>1],[/IF]{
            "id": [!TL::Id!],
            "label": "[JSON][!TL::Title!][/JSON]",
            "value" : "[!TL::Url!]",
            "image" : "/[!TL::Image!].mini.75x75.jpg"
        }
[/STORPROC]
]