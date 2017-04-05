[IF [!CONTACTMAIL!]=]
    [!CONTACTMAIL:=[!CONF::MODULE::SYSTEME::CONTACT!]!]
[/IF]

[LIB Mail|LeMail]
[METHOD LeMail|Subject][PARAM]Contact depuis le site internet Bertrand Immobilier[/PARAM][/METHOD]
[METHOD LeMail|From][PARAM]syndic@bertrandimmobilier.fr[/PARAM][/METHOD]
[METHOD LeMail|ReplyTo][PARAM]syndic@bertrandimmobilier.fr[/PARAM][/METHOD]
[METHOD LeMail|To][PARAM]syndic@bertrandimmobilier.fr[/PARAM][/METHOD]
[METHOD LeMail|Body]
    [PARAM]
        [BLOC Mail]
            Nom : [!inputContactName!]<br>
            Email : [!inputContactEmail!]<br>
            Message : [!inputContactMessage!]
        [/BLOC]
    [/PARAM]
[/METHOD]
[METHOD LeMail|BuildMail][/METHOD]
[METHOD LeMail|Send][/METHOD]
1