[STORPROC [!Query!]|R|0|1]
    <ul>
    [STORPROC Parc/Client|C|0|1000]
        <li>[!C::Nom!]</li>
        [!C::AddParent(Parc/Revendeur/[!R::Id!])!]
        [!C::Save()!]
    [/STORPROC]
    </ul>
[/STORPROC]