[IF [!CONTACTMAIL!]=]
    [!CONTACTMAIL:=[!CONF::MODULE::SYSTEME::CONTACT!]!]
[/IF]

[IF [!optionDemande!]=Syndic]
    [!Sujet:=Syndic de copropriété!]
[/IF]
[IF [!optionDemande!]=Gestion_immobiliere]
    [!Sujet:=Gestion de [!optionGestion!]!]
[/IF]
[IF [!optionDemande!]=Vente_ou_location]
    [!Sujet:=[!optionVente!]!]
[/IF]
[IF [!optionDemande!]=location_saisonniere]
    [!Sujet:=Location saisonnière!]
[/IF]

[LIB Mail|LeMail]
[METHOD LeMail|Subject][PARAM]Demande de devis : [!Sujet!][/PARAM][/METHOD]
[METHOD LeMail|From][PARAM]syndic@bertrandimmobilier.fr[/PARAM][/METHOD]
[METHOD LeMail|ReplyTo][PARAM]syndic@bertrandimmobilier.fr[/PARAM][/METHOD]
[METHOD LeMail|To][PARAM]syndic@bertrandimmobilier.fr[/PARAM][/METHOD]
[METHOD LeMail|Body]
    [PARAM]
        [BLOC Mail]
            Nom : [!lastname!]<br>
            Prénom : [!firstname!]<br>
            Email : [!email!]<br>
            Téléphone : [!phone!]<br>
            [IF [!optionDemande!]=Syndic]
                Rôle : [!optionRoleSyndicAutre!]<br>
                Nombre d'appartements : [!nbAppartementImmeuble!]<br>
                Code postal : [!codePostalImmeuble!]
            [/IF]
            [IF [!optionDemande!]=Gestion_immobiliere]
                Type de bien : [!optionGestion!]<br>
                Code postal : [!codePostalGestion!]
            [/IF]
            [IF [!optionDemande!]=Vente_ou_location]
                Nature de la demande : [!optionVente!]<br>
                Type de bien : [!optionVenteType!]<br>
                Ville : [!optionVenteVille!]
            [/IF]
            [IF [!optionDemande!]=location_saisonniere]
                Nombre de couchages : [!optionSaisonniereCouchage!]<br>
                Date d'arrivée : [!optionSaisonniereDateArrivee!]<br>
                Date de départ : [!optionSaisonniereDateDepart!]<br>
            [/IF]
        [/BLOC]
    [/PARAM]
[/METHOD]
[METHOD LeMail|BuildMail][/METHOD]
[METHOD LeMail|Send][/METHOD]
1