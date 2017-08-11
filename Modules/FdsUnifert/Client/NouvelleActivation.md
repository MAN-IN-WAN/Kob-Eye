[TITLE]Admin Kob-Eye | Nouvel envoi du mail d'activation[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
[STORPROC [!Query!]|Cli][/STORPROC]

[STORPROC Systeme/User/[!Cli::UserId!]|Usr][/STORPROC]

[IF [!Usr::Actif!]=1]

<br />
<br />
<br />
<p class="success" style="text-align: center;color: #aa0022;">Le client [!Cli::Code!] : [!Cli::Societe!] est déjà actif ! <br/>
<br/>
<a href="[!Query!]" style="font-size: 1.5em;"> > Retour au client</a>
</p>

[ELSE]

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
                [BLOC Mail]
                
                        Madame, Monsieur, Cher client,<br />
                        <br />
                        Unifert France SAS vient de mettre en ligne un espace client pour vous permettre de consulter les fiches de données de sécurité des produits que vous avez commandés.<br />
                        <br />
                        À cet effet, un compte client vient d'être créé pour vous de manière automatique sur le site www.unifert.fr<br />
                        <br />
                        Votre login d'identification correspond à votre code client  : [!Usr::Login!]<br />
                        <br />
                        Le lien suivant vous permettra de choisir un mot de passe connu de vous seul.<br />
                                http://www.unifert.fr/Activation?Login=[!Usr::Login!]&CodeVerif=[!Usr::CodeVerif!]<br />
                        Toute l'équipe d'Unifert vous remercie de votre confiance.<br />
                        <br />
                        <br />
                        Ce mail est envoyé automatiquement, merci de ne pas y répondre. <br />
                        Pour nous contacter : <a href="mailto:[!CONF::GENERAL::INFO::ADMIN_MAIL!]">[!CONF::GENERAL::INFO::ADMIN_MAIL!]</a><br />
                [/BLOC]
                //[BLOC Mail]
                //        Madame, Monsieur, Cher client,<br />
                //        <br />
                //        UNIFERT France SAS a le plaisir de vous compter parmi ses clients et nous vous invitons à visiter notre site internet www.unifert.fr<br />
                //        <br />
                //        Ce site vous permettra d’obtenir facilement toutes les informations dont vous auriez besoin sur les produits commercialisés par UNIFERT France SAS, comme par exemple, les fiches techniques et les fiches de données de sécurité.<br />
                //        <br /> 
                //        Nous vous avons créé automatiquement un accès client.<br />
                //        Votre login d'identification correspond à votre code client : [!Usr::Login!]<br />
                //        <br />
                //        Le lien suivant vous permettra de choisir un mot de passe connu de vous seul.<br />
                //                http://www.unifert.fr/Activation?Login=[!Usr::Login!]&CodeVerif=[!Usr::CodeVerif!]<br />
                //        Nous vous souhaitons une bonne navigation et vous prions de bien vouloir agréer, Madame, Monsieur, Cher client, l'expression de nos salutations distinguées.<br />
                //        <br />
                //        L’équipe d’UNIFERT France SAS<br />
                //        <br />
                //        <br />
                //        Ce mail est envoyé automatiquement, merci de ne pas y répondre.<br />
                //        Pour nous contacter : <a href="mailto:[!CONF::GENERAL::INFO::ADMIN_MAIL!]">[!CONF::GENERAL::INFO::ADMIN_MAIL!]</a><br />
                //[/BLOC]
        [/PARAM]
[/METHOD]
[!null:=[!LeMail::Send()!]!]


<br />
<br />
<br />
<p class="success" style="text-align: center;color: #00aa22;">Le mail a été correctement envoyé au client [!Cli::Code!] : [!Cli::Societe!] ! <br/>
<br/>
<a href="[!Query!]" style="font-size: 1.5em;"> > Retour au client</a>
</p>

[/IF]
