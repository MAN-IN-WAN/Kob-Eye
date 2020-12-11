//création d'un nouveau numero de boitier
[OBJ Formation|Boitier|B]
[!t:=[!B::Save()!]!]
[!B::Numero:=[!B::Id!]!]
[!t:=[!B::Save()!]!]
{
        "success": true,
        "numero": [!B::Id!]
}