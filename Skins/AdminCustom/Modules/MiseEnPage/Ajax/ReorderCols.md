
[!baseOrder:=10!]
[IF [!order!]]
        [IF [!modParent!]&&[!modChild!]]
                [IF [!modParent!]!=false]
                        [STORPROC MiseEnPage/Colonne/[!modChild!]|Col]
                                [!oldParent:=[!Col::getOneParent(Contenu)!]!]
                                [METHOD Col|delParent]
                                        [PARAM][!oldParent!][/PARAM]
                                [/METHOD]
                                //[METHOD Col|Save][/METHOD]
                                [METHOD Col|addParent]
                                        [PARAM]MiseEnPage/Contenu/[!modParent!][/PARAM]
                                [/METHOD]
                                [METHOD Col|Save][/METHOD]
                        [/STORPROC]
                [/IF]
        [/IF]
        [STORPROC [!order!]|colId]
                [!newOrder:=[!Pos:*[!baseOrder!]!]!]
                [STORPROC MiseEnPage/Colonne/[!colId!]|Col]
                        [METHOD Col|Set]
                                [PARAM]Ordre[/PARAM]
                                [PARAM][!newOrder!][/PARAM]
                        [/METHOD]
                        [METHOD Col|Save][/METHOD]
                [/STORPROC]
        [/STORPROC]

        {
                "status" : "success",
                "message" : "Everything went pretty well !"
        }
[ELSE]
        {
                "status" : "error",
                "message" : "Missing expected parameter order!"
        }
[/IF]