[SWITCH [!_ACTION_!]|=]
        [CASE cli]
                [IF [!_COMMAND_!]!=]
                
                        [IF [!_DOMAIN_!]=]
                                [!_DOMAIN_:=abtel.fr!]
                        [/IF]

                        {
                               "retour" : "[!Utils::exec([!_COMMAND_!],[!_DOMAIN_!])!]",
                               "status" : 1
                        }
                [ELSE]
                        {
                               "retour" : "Erreur : Aucune commande reçue!",
                               "status" : 0
                        }
                [/IF]
        [/CASE]
        [CASE gandi]
                [IF [!_PARAMS_!]=]
                        [!GandiApi::[!_METHOD_!]()!]
                [ELSE]
                        [IF [!_OPTS_!]=]
                                [!GandiApi::[!_METHOD_!]([!_PARAMS_!])!]
                        [ELSE]
                                [!GandiApi::[!_METHOD_!]([!_PARAMS_!],[!_OPTS_!])!]
                        [/IF]
                [/IF]
        [/CASE]
        [DEFAULT]
                Nada nib
        [/DEFAULT]
[/SWITCH]
