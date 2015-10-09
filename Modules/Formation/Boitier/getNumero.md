//création d'un nouveau numero de boitier
[OBJ Formation|Boitier|B]
[!B::Save()!]
[!B::Numero:=[!B::Id!]!]
[!B::Save()!]
{
        "success": true,
        "numero": [!B::Id!]
}