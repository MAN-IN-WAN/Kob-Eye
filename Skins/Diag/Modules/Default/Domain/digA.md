[IF [!ids!]=]
        {
                [STORPROC [!Query!]|D|0|1]
                        [!IP:=[!D::digA()!]!]
                        [IF [!IP!]]
                                [STORPROC Parc/Server/IP=[!IP!]|S|0|1]
                                "[!D::Id!]": "<div class=\"gotCli\">X</div><div class=\"info\">[!S::Nom!]</div>"
                                        [NORESULT]
                                                "[!D::Id!]": "<div class=\"noCli\">!</div><div class=\"info\">[!IP!]</div>"
                                        [/NORESULT]
                                [/STORPROC]
                        [ELSE]
                                "[!D::Id!]": "<div class=\"noCli\">!</div><div class=\"info\">Aucune information</div>"
                        [/IF]
                [/STORPROC]
        }
[ELSE]
        {
        "data":     {
                [COUNT [!ids!]|cids]
                [STORPROC [!ids!]|ID]
                        [STORPROC [!Query!]/[!ID!]|D|0|1]
                                [!IP:=[!D::digA()!]!]
                                [IF [!IP!]]
                                        [STORPROC Parc/Server/IP=[!IP!]|S|0|1]
                                        "[!D::Id!]": "<div class=\"gotCli\">X</div><div class=\"info\">[!S::Nom!]</div>"
                                                [NORESULT]
                                                        "[!D::Id!]": "<div class=\"noCli\">!</div><div class=\"info\">[!IP!]</div>"
                                                [/NORESULT]
                                        [/STORPROC]
                                [ELSE]
                                        "[!D::Id!]": "<div class=\"noCli\">!</div><div class=\"info\">Aucune information</div>"
                                [/IF]
                        [/STORPROC]
                        [IF [!Pos!]!=[!cids!]],[/IF]
                [/STORPROC]
                },
        "type":   "digA"  
        }
[/IF]