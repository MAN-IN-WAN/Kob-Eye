[!data:={"client": "99997","email": "falsemail@abtel.fr","societe": "ABTEL","commercial": "","contact": [],"adr1": "zone Delta","adr2": "","cp": "30320","ville": "Bouillargues","tel": "0123456789","fax": "0123456789","mobile": "","web": "","fds": ["1021", "1005", "1020", "1903", "1201", "1901"],"groupe": ""}!]

[!obj:=[!Utils::jsonDecode([!data!])!]!]

[!DEBUG::obj!]
[!obj::client!]



        [OBJ FdsUnifert|Client|Cli]
        [!null:=[!Cli::Set(Code,[!obj::client!])!]!]
        [!null:=[!Cli::Set(Mail,[!obj::email!])!]!]
        [!null:=[!Cli::Set(Societe,[!obj::societe!])!]!]
        [!null:=[!Cli::Set(Commercial,[!obj::commercial!])!]!]
        [!null:=[!Cli::Set(Adresse1,[!obj::adr1!])!]!]
        [!null:=[!Cli::Set(Adresse2,[!obj::adr2!])!]!]
        [!null:=[!Cli::Set(CodePostal,[!obj::cp!])!]!]
        [!null:=[!Cli::Set(Ville,[!obj::ville!])!]!]
        [!null:=[!Cli::Set(Tel,[!obj::tel!])!]!]
        [!null:=[!Cli::Set(Fax,[!obj::fax!])!]!]
        //Gestion des groupes de client
        [IF [!obj::groupe!]=]
        [ELSE]
                [STORPROC FdsUnifert/Client/Code=[!obj::groupe!]|CliGrp|0|1]
                        [!null:=[!Cli::addParent([!CliGrp!])!]!]
                [/STORPROC] 
        [/IF]
        
        [!Cli::Save(1)!]