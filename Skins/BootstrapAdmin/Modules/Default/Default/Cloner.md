[STORPROC [!Query!]|O|0|1][/STORPROC]
[!C:=[!O::getClone()!]!]
[!C::Save()!]
[STORPROC [!O::typesParent!]|P]

    [STORPROC [!O::Module!]/[!P::Titre!]/[!O::ObjectType!]/[!O::Id!]|Par]
        [!C::AddParent([!Par::Module!]/[!Par::ObjectType!]/[!Par::Id!])!]
    [/STORPROC]
[/STORPROC]
[!C::Save()!]
<div class="alert alert-success">Le produit a été cloné avec succès.</div>
<a href="/[!Sys::getMenu([!O::Module!]/[!O::ObjectType!])!]/[!C::Id!]" class="btn btn-info"> Aller sur le produit cloné</a>