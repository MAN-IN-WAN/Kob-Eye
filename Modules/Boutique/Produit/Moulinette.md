[COUNT Boutique/Produit|NbP]
[!R:=[!Math::Floor([!NbP:/100!])!]!]
[STORPROC [!R:+1!]|N|0|1000000]
    [BASH green]Page [!Key!]/[!R!][/BASH]
    [STORPROC Boutique/Produit|P|[!Key:*100!]|100]
        [!P::Title:=!]
        [!P::Save()!]
        [BASH green][!P::Id!] [!P::Title!][/BASH]
    [/STORPROC]
[/STORPROC]