[IF [!ids!]=]
        {
//                [STORPROC [!Query!]|D|0|1]
//                        [STORPROC Parc/Client/Domain/[!D::Id!]|C]
//                        "[!D::Id!]": "<div class=\"gotCli\">X</div><div class=\"info\">Id: [!C::Id!] <br/>Code Web: [IF [!C::CodeClient!]][!C::CodeClient!][ELSE]Non renseigné[/IF] <br/>Code Info: [IF [!C::CodeClientInfo!]][!C::CodeClientInfo!][ELSE]Non renseigné[/IF]</div>"
//                        [NORESULT]
//                        "[!D::Id!]": "<div class=\"noCli\">!</div>"
//                        [/NORESULT]
//                        [/STORPROC]
//                [/STORPROC]
        }
[ELSE]
        {
        "data":     {
                'aaaaaaaaaaaaaaaaaaaaa'
//                [COUNT [!ids!]|cids]
//                [STORPROC [!ids!]|ID]
//                        [STORPROC Parc/Client/Domain/[!ID!]|C]
//                        "[!ID!]": "<div class=\"gotCli\">X</div><div class=\"info\">Id: [!C::Id!] <br/>Code Web: [IF [!C::CodeClient!]][!C::CodeClient!][ELSE]Non renseigné[/IF] <br/>Code Info: [IF [!C::CodeClientInfo!]][!C::CodeClientInfo!][ELSE]Non renseigné[/IF]</div>"
//                        [NORESULT]
//                        "[!ID!]": "<div class=\"noCli\">!</div>"
//                        [/NORESULT]
//                        [/STORPROC]
//                        [IF [!Pos!]!=[!cids!]],[/IF]
//                [/STORPROC]
//                },
        "type":   "getHome"  
        }
[/IF]