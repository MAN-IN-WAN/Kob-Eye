[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
    [MODULE ParcImmobilier/Residence/Fiche]
[ELSE]
    [MODULE ParcImmobilier/Residence/Liste]
[/IF]