[STORPROC Reservations/Client/UserId=|C]
    [STORPROC Systeme/User/Mail=[!C::Mail!]|U|0|1]
        [!C::UserId:=[!U::Id!]!]
        [!C::Save()!]
        <li>[!C::Mail!] [!U::Mail!] OK</li>
        [NORESULT]
        <li>[!C::Mail!] NOK</li>
        [/NORESULT]
    [/STORPROC]
[/STORPROC]