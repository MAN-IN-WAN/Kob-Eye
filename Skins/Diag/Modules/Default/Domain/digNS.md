[IF [!ids!]=]
        {
                [STORPROC [!Query!]|D|0|1]
                        [!DNS:=[!D::digNS()!]!]
                        [IF [!DNS!]=2]
                                "[!D::Id!]": "<div class=\"gotCli\">X</div>"
                        [ELSE]
                                "[!D::Id!]": "<div class=\"noCli\">!</div><div class=\"info\">[STORPROC [!DNS!]|N][!N!]<br/>[NORESULT]Aucune information[/NORESULT][/STORPROC]</div>"
                        [/IF]
                [/STORPROC]
        }
[ELSE]
        {
        "data":     {
                [COUNT [!ids!]|cids]
                [STORPROC [!ids!]|ID]
                        [STORPROC [!Query!]/[!ID!]|D|0|1]
                                [!DNS:=[!D::digNS()!]!]
                                [IF [!DNS!]=2]
                                        "[!D::Id!]": "<div class=\"gotCli\">X</div>"
                                [ELSE]
                                        "[!D::Id!]": "<div class=\"noCli\">!</div><div class=\"info\">[STORPROC [!DNS!]|N][!N!]<br/>[NORESULT]Aucune information[/NORESULT][/STORPROC]</div>"
                                [/IF]
                        [/STORPROC]
                        [IF [!Pos!]!=[!cids!]],[/IF]
                [/STORPROC]
                },
        "type":   "digNS"  
        }
[/IF]