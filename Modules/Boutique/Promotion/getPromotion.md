[!DATE:=[!TMS::Now!]!]
{

    results:[
    [STORPROC Boutique/Promotion/DateDebutPromo<[!DATE!]&DateFinPromo>[!DATE!]&Display=1&SliderEnable=1|S|0|100]
        [IF [!Pos!]>1],[/IF]{
            "image": "[!S::Image!].limit.800x400.jpg",
            "textdate": "Offre valable du [DATE d/m/Y][!S::DateDebutPromo!][/DATE] au [DATE d/m/Y][!S::DateFinPromo!][/DATE]"
            [STORPROC Boutique/Promotion/[!S::Id!]/PromotionCalque|SC|0|1]
            ,"text": "[JSON][!SC::Texte!][/JSON]"
            [/STORPROC]
        }
    [/STORPROC]
    ]
}