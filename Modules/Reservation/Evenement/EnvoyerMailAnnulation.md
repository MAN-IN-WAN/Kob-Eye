[STORPROC [!Query!]|MonEv|0|1]
		[!Lieu:=!] [!Organisme:=!][!Spectacle:=!]
        [STORPROC Reservation/Salle/Evenement/[!MonEv::Id!]|Sal]
            [!Lieu+=[!Sal::Nom!] [!Sal::Adresse!] [!Sal::CodPos!] [!Sal::Ville!]!]
        [/STORPROC]
        [STORPROC Reservation/Spectacle/Evenement/[!MonEv::Id!]|Sp]
            [!Spectacle:=[!Sp::Nom!]!]
            [STORPROC Reservation/Organisation/Spectacle/[!Sp::Id!]|Org]
                [!Organisme+=[!Org::Nom!]!]
            [/STORPROC]
        [/STORPROC]
        [STORPROC Reservation/Evenement/[!MonEv::Id!]/Reservations|Resas]
            [LIB Mail|LeMail_[!Resas::Id!]]
            [METHOD LeMail_[!Resas::Id!]|Subject][PARAM]culture et sport solidaires 34 :  Annulation de spectacle[/PARAM][/METHOD]
            [METHOD LeMail_[!Resas::Id!]|From][PARAM]rapport@cultureetsportsolidaires34.fr[/PARAM][/METHOD]
            [METHOD LeMail_[!Resas::Id!]|ReplyTo][PARAM]pasdereponse@cultureetsportsolidaires34.fr[/PARAM][/METHOD]
            [METHOD LeMail_[!Resas::Id!]|To][PARAM]direction@css34.fr[/PARAM][/METHOD]
            [METHOD LeMail_[!Resas::Id!]|To][PARAM]lison@css34.fr [/PARAM][/METHOD]
            [METHOD LeMail_[!Resas::Id!]|Bcc][PARAM] myriam@abtel.fr[/PARAM][/METHOD]
            [METHOD LeMail_[!Resas::Id!]|Body]
                [PARAM]
                [BLOC Mail]
                    [STORPROC Reservation/Client/Reservations/[!Resas::Id!]|Cli]
                        [IF [!Pos!]=1]
                            <BR /><strong>[!Cli::Nom!], </strong><br /><br />
                            <strong>Nous sommes au regret de vous annoncer que le spectacle - [!Spectacle!] - organisé par <u>[!Organisme!]</u> qui devait avoir lieu le : [!Utils::getDate(d/m/Y H:i,[!MonEv::DateDebut!])!]  à [!Lieu!]   <u>a été  annulé </u></strong> <br /> <br />
                             <strong> La réservation concernée par cette annulation est la réservation numéro: [!Resas::Reference!]  </strong> <br /> <br />
                             Vous trouverez ci-dessous le détail de vos réservations annulées.<br />
                        [/IF]
                        [IF [!Cli::Mail!]=='']
                            <br /><br /><strong>POUR CSS34 ATTENTION cette structure SOCIALE n'a pas de mail merci de la contacter par un autre moyen   ! </strong><br /><br />
                        [ELSE]   
                            [!Lesmails:=[!Utils::cleanMail([!Cli::Mail!])!]!]
                            [STORPROC [!Lesmails!]|MM]
                                    [METHOD LeMail_[!Resas::Id!]|To][PARAM][!MM!] [/PARAM][/METHOD]
                            [/STORPROC]
                        [/IF]
                        <br />   Voici la liste des personnes qui étaient prévues pour ce spectacle<br />
                        [STORPROC Reservation/Reservations/[!Resas::Id!]/Personne|Pers]
                             --><strong>   [!Pers::Nom!] [!Pers::Prenom!] </strong><br />
                        [/STORPROC]
                         <br />
                    [/STORPROC]
                [/BLOC]
                [/PARAM]
            [/METHOD]
            [METHOD LeMail_[!Resas::Id!]|Priority][PARAM]5[/PARAM][/METHOD]
            [METHOD LeMail_[!Resas::Id!]|BuildMail][/METHOD]
            [METHOD LeMail_[!Resas::Id!]|Send][/METHOD]
        [/STORPROC]
[/STORPROC]


