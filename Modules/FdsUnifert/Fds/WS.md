[LOG] Envoi Paul [!Tms::Now!][/LOG]
[LOG][!data!][/LOG]

[!obj:=[!Utils::jsonDecode([!data!])!]!]
[LOG]++++++++++++++++++++++++++++++++++++++++++[/LOG]
[LOG][!obj!][/LOG]
[LOG]------------------------------------------[/LOG]
[!res:=0!]
//Creation du client le cas échéant / maj sinon
[STORPROC FdsUnifert/Client/Code=[!obj::client!]|Cli]
        [!null:=[!Cli::Set(Mail,[!obj::email!])!]!]
        [!null:=[!Cli::Set(Societe,[!obj::societe!])!]!]
        
        [!null:=[!Cli::Set(Commercial,[!obj::commercial!])!]!]
        [!null:=[!Cli::Set(Adresse1,[!obj::adr1!])!]!]
        [!null:=[!Cli::Set(Adresse2,[!obj::adr2!])!]!]
        [!null:=[!Cli::Set(CodePostal,[!obj::cp!])!]!]
        [!null:=[!Cli::Set(Ville,[!obj::ville!])!]!]
        [!null:=[!Cli::Set(Tel,[!obj::tel!])!]!]
        [!null:=[!Cli::Set(Fax,[!obj::fax!])!]!]
        [!null:=[!Cli::Set(Portable,[!obj::mobile!])!]!]
        //Gestion des groupes de client
        [IF [!obj::groupe!]=]
        [ELSE]
                [LOG]FdsUnifert/Client/Code=[!obj::groupe!][/LOG]
                [STORPROC FdsUnifert/Client/Code=[!obj::groupe!]|CliGrp|0|1] 
                        [!null:=[!Cli::addParent([!CliGrp!])!]!]
                [/STORPROC] 
        [/IF]
        [!res:=[!Cli::Save()!]!]

        [NORESULT]
                [!newCli:=1!]
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
                
                [IF [!Cli::Save(1)!]]
                        [!res:=1!]
                        [STORPROC Systeme/User/[!Cli::UserId!]|Usr]
                                [!null:=[!Usr::Set(Nom,[!obj::nom!])!]!]
                                [!null:=[!Usr::Set(Prenom,[!obj::prenom!])!]!]
                                [!null:=[!Usr::Save()!]!]
                        [/STORPROC]
                        
                        //Creation des contacts si besoin / maj sinon
                        [STORPROC [!obj::contact!]|Cont]
                                [STORPROC FdsUnifert/Client/[!Cli::Id!]/Contact/Nom=[!Cont::nom!]&Prenom=[!Cont::prenom!]|Contact]
                                        [!null:=[!Contact::Set(Mail,[!Cont::email!])!]!]
                                        [!null:=[!Contact::Set(Fonction,[!Cont::fonction!])!]!]
                                        [!null:=[!Contact::Set(Type,[!Cont::type!])!]!]
                                        [!null:=[!Contact::Set(Tel,[!Cont::tel!])!]!]
                                        [!null:=[!Contact::Set(Fax,[!Cont::fax!])!]!]
                                        [!null:=[!Contact::Set(Portable,[!Cont::mobile!])!]!]
                                        [!null:=[!Contact::Set(Fds,[!Cont::contactFDS!])!]!]
                                        
                                        [!null:=[!Contact::Save()!]!]
                                        
                                        [NORESULT]
                                                [OBJ FdsUnifert|Contact|Contact]
                                                [!null:=[!Contact::Set(Nom,[!Cont::nom!])!]!]
                                                [!null:=[!Contact::Set(Prenom,[!Cont::prenom!])!]!]
                                                [!null:=[!Contact::Set(Civilite,[!Cont::civilite!])!]!]
                                                
                                                [!null:=[!Contact::Set(Mail,[!Cont::email!])!]!]
                                                [!null:=[!Contact::Set(Fonction,[!Cont::fonction!])!]!]
                                                [!null:=[!Contact::Set(Type,[!Cont::type!])!]!]
                                                [!null:=[!Contact::Set(Tel,[!Cont::tel!])!]!]
                                                [!null:=[!Contact::Set(Fax,[!Cont::fax!])!]!]
                                                [!null:=[!Contact::Set(Portable,[!Cont::mobile!])!]!]
                                                [!null:=[!Contact::Set(Fds,[!Cont::contactFDS!])!]!]
                                                
                                                [!null:=[!Contact::Save()!]!]
                                        [/NORESULT]
                                [/STORPROC]
                                [!null:=[!Contact::addParent([!Cli!])!]!]
                                [!null:=[!Contact::Save()!]!]
                        [/STORPROC]
                        
                        //Mail client
                        [LIB Mail|LeMail]
                        [!null:=[!LeMail::From(noreply@unifert.fr)!]!]
                        [STORPROC FdsUnifert/Client/[!Cli::Id!]/Contact/Fds=1&&Mail!=|Ct]
                                [!null:=[!LeMail::To([!Ct::Mail!])!]!]
                                [NORESULT]
                                        [!null:=[!LeMail::To([!Cli::Mail!])!]!]
                                [/NORESULT]
                        [/STORPROC]
                        //[!null:=[!LeMail::To(gcandella@abtel.fr)!]!]
                        [!null:=[!LeMail::Bcc(fds@unifert.fr)!]!]
                        [!null:=[!LeMail::Subject(Unifert : Nouveau compte client sur le site Unifert.fr)!]!]
                        
                        [METHOD LeMail|Body]
                                [PARAM]
                                        //[BLOC Mail]
                                        //
                                        //        Madame, Monsieur, Cher client,<br />
                                        //        <br />
                                        //        Unifert France SAS vient de mettre en ligne un espace client pour vous permettre de consulter les fiches de données de sécurité des produits que vous avez commandés.<br />
                                        //        <br />
                                        //        À cet effet, un compte client vient d'être créé pour vous de manière automatique sur le site www.unifert.fr<br />
                                        //        <br />
                                        //        Votre login d'identification correspond à votre code client  : [!Usr::Login!]<br />
                                        //        <br />
                                        //        Le lien suivant vous permettra de choisir un mot de passe connu de vous seul.<br />
                                        //                http://www.unifert.fr/Activation?Login=[!Usr::Login!]&CodeVerif=[!Usr::CodeVerif!]<br />
                                        //        Toute l'équipe d'Unifert vous remercie de votre confiance.<br />
                                        //        <br />
                                        //        <br />
                                        //        Ce mail est envoyé automatiquement, merci de ne pas y répondre. <br />
                                        //        Pour nous contacter : <a href="mailto:[!CONF::GENERAL::INFO::ADMIN_MAIL!]">[!CONF::GENERAL::INFO::ADMIN_MAIL!]</a><br />
                                        //[/BLOC]
                                        [BLOC Mail]
                                                Madame, Monsieur, Cher client,<br />
                                                <br />
                                                UNIFERT France SAS a le plaisir de vous compter parmi ses clients et nous vous invitons à visiter notre site internet www.unifert.fr<br />
                                                <br />
                                                Ce site vous permettra d’obtenir facilement toutes les informations dont vous auriez besoin sur les produits commercialisés par UNIFERT France SAS, comme par exemple, les fiches techniques et les fiches de données de sécurité.<br />
                                                <br /> 
                                                Nous vous avons créé automatiquement un accès client.<br />
                                                Votre login d'identification correspond à votre code client : [!Usr::Login!]<br />
                                                <br />
                                                Le lien suivant vous permettra de choisir un mot de passe connu de vous seul.<br />
                                                        http://www.unifert.fr/Activation?Login=[!Usr::Login!]&CodeVerif=[!Usr::CodeVerif!]<br />
                                                Nous vous souhaitons une bonne navigation et vous prions de bien vouloir agréer, Madame, Monsieur, Cher client, l'expression de nos salutations distinguées.<br />
                                                <br />
                                                L’équipe d’UNIFERT France SAS<br />
                                                <br />
                                                <br />
                                                Ce mail est envoyé automatiquement, merci de ne pas y répondre.<br />
                                                Pour nous contacter : <a href="mailto:[!CONF::GENERAL::INFO::ADMIN_MAIL!]">[!CONF::GENERAL::INFO::ADMIN_MAIL!]</a><br />
                                        [/BLOC]
                                [/PARAM]
                        [/METHOD]
                        [!null:=[!LeMail::Send()!]!]
        
                        //Mail Unifert
                        [LIB Mail|LeMail]
                        [!null:=[!LeMail::From(noreply@unifert.fr)!]!]
                        [!null:=[!LeMail::To([!CONF::GENERAL::INFO::ADMIN_MAIL!])!]!]
                        [!null:=[!LeMail::Cc(gcandella@abtel.fr)!]!]
                        [!null:=[!LeMail::Bcc(fds@unifert.fr)!]!]
                        [!null:=[!LeMail::Subject(Unifert : Nouveau compte client sur le site Unifert.fr)!]!]
                        [METHOD LeMail|Body]
                                [PARAM]
                                        [BLOC Mail]
                                                Bonjour,<br />
                                                Un compte client vient d'être créé pour [!Cli::Societe!] de manière automatique sur le site Unifert.fr . <br />
                                                <br/>
                                                Veuillez vous rendre dans la partie administration afin de vérifier les informations renseignées et des les completer le cas échéant.
                                                <br/>
                                                Ce mail est envoyé automatiquement, merci de na pas y répondre.
                                        [/BLOC]
                                [/PARAM]
                        [/METHOD]
                        [!null:=[!LeMail::Send()!]!]
                [/IF]                   
        [/NORESULT]
        [IF [!newCli!]!=1]
                //Creation des contacts si besoin / maj sinon
                [STORPROC [!obj::contact!]|Cont]
                        [STORPROC FdsUnifert/Client/[!Cli::Id!]/Contact/Nom=[!Cont::nom!]&Prenom=[!Cont::prenom!]|Contact]
                                [!null:=[!Contact::Set(Mail,[!Cont::email!])!]!]
                                [!null:=[!Contact::Set(Fonction,[!Cont::fonction!])!]!]
                                [!null:=[!Contact::Set(Type,[!Cont::type!])!]!]
                                [!null:=[!Contact::Set(Tel,[!Cont::tel!])!]!]
                                [!null:=[!Contact::Set(Fax,[!Cont::fax!])!]!]
                                [!null:=[!Contact::Set(Portable,[!Cont::mobile!])!]!]
                                [!null:=[!Contact::Set(Fds,[!Cont::contactFDS!])!]!]
                                
                                [!null:=[!Contact::Save()!]!]
                                
                                [NORESULT]
                                        [OBJ FdsUnifert|Contact|Contact]
                                        [!null:=[!Contact::Set(Nom,[!Cont::nom!])!]!]
                                        [!null:=[!Contact::Set(Prenom,[!Cont::prenom!])!]!]
                                        [!null:=[!Contact::Set(Civilite,[!Cont::civilite!])!]!]
                                        
                                        [!null:=[!Contact::Set(Mail,[!Cont::email!])!]!]
                                        [!null:=[!Contact::Set(Fonction,[!Cont::fonction!])!]!]
                                        [!null:=[!Contact::Set(Type,[!Cont::type!])!]!]
                                        [!null:=[!Contact::Set(Tel,[!Cont::tel!])!]!]
                                        [!null:=[!Contact::Set(Fax,[!Cont::fax!])!]!]
                                        [!null:=[!Contact::Set(Portable,[!Cont::mobile!])!]!]
                                        [!null:=[!Contact::Set(Fds,[!Cont::contactFDS!])!]!]
                                        
                                        [!null:=[!Contact::Save()!]!]
                                [/NORESULT]
                        [/STORPROC]
                        [!null:=[!Contact::addParent([!Cli!])!]!]
                        [!null:=[!Contact::Save()!]!]
                [/STORPROC]
        
                [STORPROC Systeme/User/[!Cli::UserId!]|Usr][/STORPROC]
                        
                   
                [IF [!Usr::Actif!]!=1]
                        //Mail client
                        [LIB Mail|LeMail]
                        [!null:=[!LeMail::From(noreply@unifert.fr)!]!]
                        [STORPROC FdsUnifert/Client/[!Cli::Id!]/Contact/Fds=1&&Mail!=|Ct]
                                [!null:=[!LeMail::To([!Ct::Mail!])!]!]
                                [NORESULT]
                                        [!null:=[!LeMail::To([!Cli::Mail!])!]!]
                                [/NORESULT]
                        [/STORPROC]
                        //[!null:=[!LeMail::To(gcandella@abtel.fr)!]!]
                        [!null:=[!LeMail::Bcc(fds@unifert.fr)!]!]

                        [!null:=[!LeMail::Subject(Unifert : Nouveau compte client sur le site Unifert.fr)!]!]
                        
                        [METHOD LeMail|Body]
                                [PARAM]
                                        //[BLOC Mail]
                                        //
                                        //        Madame, Monsieur, Cher client,<br />
                                        //        <br />
                                        //        Unifert France SAS vient de mettre en ligne un espace client pour vous permettre de consulter les fiches de données de sécurité des produits que vous avez commandés.<br />
                                        //        <br />
                                        //        À cet effet, un compte client vient d'être créé pour vous de manière automatique sur le site www.unifert.fr<br />
                                        //        <br />
                                        //        Votre login d'identification correspond à votre code client  : [!Usr::Login!]<br />
                                        //        <br />
                                        //        Le lien suivant vous permettra de choisir un mot de passe connu de vous seul.<br />
                                        //                http://www.unifert.fr/Activation?Login=[!Usr::Login!]&CodeVerif=[!Usr::CodeVerif!]<br />
                                        //        Toute l'équipe d'Unifert vous remercie de votre confiance.<br />
                                        //        <br />
                                        //        <br />
                                        //        Ce mail est envoyé automatiquement, merci de ne pas y répondre. <br />
                                        //        Pour nous contacter : <a href="mailto:[!CONF::GENERAL::INFO::ADMIN_MAIL!]">[!CONF::GENERAL::INFO::ADMIN_MAIL!]</a><br />
                                        //[/BLOC]
                                        [BLOC Mail]
                                                Madame, Monsieur, Cher client,<br />
                                                <br />
                                                UNIFERT France SAS a le plaisir de vous compter parmi ses clients et nous vous invitons à visiter notre site internet www.unifert.fr<br />
                                                <br />
                                                Ce site vous permettra d’obtenir facilement toutes les informations dont vous auriez besoin sur les produits commercialisés par UNIFERT France SAS, comme par exemple, les fiches techniques et les fiches de données de sécurité.<br />
                                                <br /> 
                                                Nous vous avons créé automatiquement un accès client.<br />
                                                Votre login d'identification correspond à votre code client : [!Usr::Login!]<br />
                                                <br />
                                                Le lien suivant vous permettra de choisir un mot de passe connu de vous seul.<br />
                                                        http://www.unifert.fr/Activation?Login=[!Usr::Login!]&CodeVerif=[!Usr::CodeVerif!]<br />
                                                Nous vous souhaitons une bonne navigation et vous prions de bien vouloir agréer, Madame, Monsieur, Cher client, l'expression de nos salutations distinguées.<br />
                                                <br />
                                                L’équipe d’UNIFERT France SAS<br />
                                                <br />
                                                <br />
                                                Ce mail est envoyé automatiquement, merci de ne pas y répondre.<br />
                                                Pour nous contacter : <a href="mailto:[!CONF::GENERAL::INFO::ADMIN_MAIL!]">[!CONF::GENERAL::INFO::ADMIN_MAIL!]</a><br />
                                        [/BLOC]
                                [/PARAM]
                        [/METHOD]
                        [!null:=[!LeMail::Send()!]!]
                [/IF]
        [/IF]
[/STORPROC]

        
[IF [!res!]]        
        //Creation de la fiche si nécéssaire puis affectation si pas déja fait
        [STORPROC [!obj::fds!]|codeFds]
                [STORPROC FdsUnifert/Fds/Code=[!codeFds!]|Fds]
                        [NORESULT]
                                [OBJ FdsUnifert|Fds|Fds]
                                [!null:=[!Fds::Set(Code,[!codeFds!])!]!]
                                [!null:=[!Fds::Save()!]!]
                                
                                //Mail Unifert
                                [LIB Mail|LeMail]
                                [!null:=[!LeMail::From(noreply@unifert.fr)!]!]
                                [!null:=[!LeMail::To([!CONF::GENERAL::INFO::ADMIN_MAIL!])!]!]
                                [!null:=[!LeMail::Cc(gcandella@abtel.fr)!]!]
                                [!null:=[!LeMail::Bcc(fds@unifert.fr)!]!]
                                [!null:=[!LeMail::Subject(Unifert : Nouvelle fiche de sécurité créée sur le site Unifert.fr)!]!]
                                [METHOD LeMail|Body]
                                        [PARAM]
                                                [BLOC Mail]
                                                        Bonjour,<br />
                                                        Une fiche de sécurité "[!Fds::Code!]" vient d'être créée de manière automatique sur le site Unifert.fr suite à une commande de [!Cli::Societe!]. <br />
                                                        <br/>
                                                        Veuillez vous rendre dans la partie administration afin de completer les informations nécéssaires.
                                                        <br/>
                                                        Ce mail est envoyé automatiquement, merci de na pas y répondre.
                                                [/BLOC]
                                        [/PARAM]
                                [/METHOD]
                                [!null:=[!LeMail::Send()!]!]]
                        [/NORESULT]
                [/STORPROC]
                [!null:=[!Cli::addParent([!Fds!])!]!]
                [!null:=[!Cli::Save()!]!]
        [/STORPROC]
        
        
        //Retour Paul
        OK
[ELSE]
        //Retour Paul
        NOT OK
        [STORPROC [!Cli::Error!]|err]
                Champ :[!err::Prop!]
                Message : [!err::Message!]                
        [/STORPROC]
[/IF]


