[OBJ Boutique|Magasin|Magasin]
[!Mag:=[!Magasin::getCurrentMagasin()!]!]
[STORPROC Systeme/User/Mail=[!Mail!]&&CodeVerif=[!CodeVerif!]|PO|0|1]
// on vient du lien envoyé par mail
[!ErreurPass:=0!]
[IF [!I_SaisiPass!]!=]
[IF [!C_Pass!]!=[!C_Pass2!]] [!ErreurPass:=1!] [/IF]
[!CliOk:=0!]
[STORPROC Boutique/Client/UserId=[!PO::Id!]|Cli|0|1][!CliOk:=1!][/STORPROC]
[IF [!CliOk!]=0||[!ErreurPass!]=1]
[!ErreurPass:=1!]
<div style="margin:5px">
    [BLOC Erreur|Liste des erreurs]
    <ul class="Error">
        [IF [!CliOk!]=0]
        <li>Cette adresse e-mail n'existe pas dans notre base ! </li>
        [/IF]
        [IF [!ErreurPass!]=1]
        <li>Les deux mots de passe ne sont pas identiques, merci de les ressaisir.</li>
        [/IF]
    </ul>
    [/BLOC]
</div>
[ELSE]
// Mise à jour client avec new pass
[METHOD PO|Set]
[PARAM]Pass[/PARAM]
[PARAM][!C_Pass!][/PARAM]
[/METHOD]
[METHOD PO|Save][/METHOD]
[CONNEXION [!Mail!]|[!C_Pass!]]
[REDIRECT]Mon-compte[/REDIRECT]
[/IF]
[/IF]
[IF [!I_SaisiPass!]=||[!ErreurPass!]>0]
<h1 class="moncompte">Mot de passe oublié</h1>
<div class="user">
    <div class="BlocLogin">
        Merci de saisir votre nouveau mot de passe.<br /><br />
        <form action="/[!Lien!]" method="post" id="connexion">
            <input  type="hidden" value="[!Mail!]" name="Mail">
            <input  type="hidden" value="[!CodeVerif!]" name="CodeVerif">
            <div class="LigneForm" style="width:80%">
                <label>Votre nouveau mot de pass</label>
                <input type="password" name="C_Pass" id="C_Pass" value="" />
            </div>
            <div class="LigneForm" style="width:80%">
                <label>Confirmation de votre nouveau mot de pass</label>
                <input type="password" name="C_Pass2" id="C_Pass2" value="" />
            </div>
            <div class="LigneForm" style="width:80%">
                <div class="BoutonsCentre" >
                    <input  type="submit" value="Valider" name="I_SaisiPass" tabindex="2" class="btn btn-info Connexion">
                </div>
            </div>
        </form>
    </div>
</div>
[/IF]

// on entre pour demander la saisie d'un nouveau pass
[NORESULT]
[!Erreur:=0!]
[IF [!I_RecupPass!]!=]
[!CliOk:=0!]
[!UsOk:=0!]
[STORPROC Boutique/Client/Mail=[!C_Login!]|CLI|0|1]
[!CliOk:=1!]
[STORPROC Systeme/User/[!CLI::UserId!]|Us|0|1][!UsOk:=1!][/STORPROC]
[/STORPROC]
[IF [!CliOk!]=1&&[!UsOk!]=1]
[LIB Mail|LeMail]
[METHOD LeMail|From]
[PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM]
[/METHOD]
[METHOD LeMail|To]
[PARAM][!CLI::Mail!][/PARAM]
[/METHOD]
[METHOD LeMail|Subject]
[PARAM][!Mag::Nom!] : Mot de passe oublié[/PARAM]
[/METHOD]
[METHOD LeMail|Body][PARAM]
[BLOC Mail]
Bonjour [!CLI::Civilite!] [!CLI::Prenom!] [!CLI::Nom!]<br />
Vous avez oublié votre mot de passe, le lien ci-dessous va vous permettre de saisir un nouveau mot de passe. <br />
<hr/>
[!Domaine!]/RecupPass?Mail=[!C_Login!]&CodeVerif=[!Us::CodeVerif!]<br />
<hr/>
Toute l'équipe de [!Mag::Nom!] vous remercie de votre confiance.<br/><br/>
<hr/>
Ce mail est envoyer automatiquement, merci de na pas y répondre.
<hr/>
Pour nous contacter : [!CONF::MODULE::SYSTEME::CONTACT!]<br/><br/>
[/BLOC]
[/PARAM][/METHOD]
[METHOD LeMail|Send][/METHOD]
<h1 class="moncompte">Mot de passe oublié</h1>
<h3 class="moncompteinstruction">Un email d'instruction vient de vous être envoyé. [!Mag::Nom!] vous remercie.</h3>

[ELSE]
[!Erreur:=1!]

[BLOC Erreur|Liste des erreurs]
<ul>
    [IF [!CliOk!]=0]<li>Cette adresse e-mail n'existe pas dans notre base ! </li>[/IF]
    [IF [!UsOk!]=0]<li>Pas de code de vérification trouvé pour votre compte </li>[/IF]
</ul>
[/BLOC]

[/IF]


[/IF]

[IF [!I_RecupPass!]=||[!Erreur!]=1]
<h1 class="moncompte">Mot de passe oublié</h1>
<div class="user">
    <div class="BlocLogin">
        Merci de saisir votre adresse mail, si cette adresse existe dans notre base de client vous allez recevoir un mail d'instruction à suivre qui vous permettront de saisir un nouveau mot de passe.<br /><br />

        <form action="/[!Lien!]" method="post" id="connexion">
            <div class="LigneForm">
                <label>Votre e-mail</label>
                <input type="text" name="C_Login" id="C_Login" value="[!C_Login!]" />
            </div>
            <div class="LigneForm">
                <div class="BoutonsValider" >
                    <input type="submit" value="Valider" name="I_RecupPass" tabindex="2" class="btn btn-info ValiderPassOublie" >
                </div>
            </div>
        </form>
    </div>
</div>

[/IF]
[/NORESULT]

[/STORPROC]