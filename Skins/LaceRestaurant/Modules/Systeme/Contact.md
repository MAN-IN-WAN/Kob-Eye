[IF [!CONTACTMAIL!]=]
//        [!CONTACTMAIL:=pierre.foata@free.fr!]
        [!CONTACTMAIL:=contact@lace-restaurant.fr!]
[/IF]
[IF [!receiver!]!=]
    [!CONTACTMAILCC:=[!CONF::MODULE::SYSTEME::CONTACT!]!]
[/IF]
[!SHOW_FORM:=1!]

<section id="contact" class="contact">
    <article>
        <header class="ico"><h1>
            Contactez-nous
            <br/>Prenez une réservation
        </h1></header>
        <div class="content" style="padding:0 20px;">
            <form id="FormContact" method="post" action="/[!Lien!]" >
                <div class="row-fluid">
                    [IF [!SendContact!]!=]
                        //Verification des informations du formulaire
                        [!C_Error:=0!]
                        [IF [!agree!]=1][ELSE][!agree_Error:=1!][!C_Error:=1!][/IF]
                        [IF [!FullName!]][ELSE][!FullName_Error:=1!][!C_Error:=1!][/IF]
                        [IF [!Utils::isMail([!Email!])!]][ELSE][!Email_Error:=1!][!C_Error:=1!][/IF]
                        [IF [!Subject!]][ELSE][!Subject_Error:=1!][!C_Error:=1!][/IF]
                        [IF [!Message!]][ELSE][!Message_Error:=1!][!C_Error:=1!][/IF]
                        [IF [!C_Error!]]
                        // Si il y a des erreurs, on les affiche
                        <div class="alert alert-danger">
                            <strong>__CONTACT_ERRORS__</strong>
                            <ul>
                                [IF [!agree_Error!]]<li>__PLEASE_ACEEPT_ALL_TERMS_AND_CONDITIONS__</li>[/IF]
                                [IF [!FullName_Error!]]<li>__ERROR_NAME__</li>[/IF]
                                [IF [!Email_Error!]]<li>__ERROR_EMAIL__</li>[/IF]
                                [IF [!Subject_Error!]]<li>__ERROR_SUBJECT__</li>[/IF]
                                [IF [!Message_Error!]]<li>__ERROR_MESSAGE__</li>[/IF]
                            </ul>
                        </div>
                    [ELSE]
                        // Sinon envoi du mail
                        [LIB Mail|LeMail]
                        [METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - [!C_Objet!][/PARAM][/METHOD]
                        [METHOD LeMail|From][PARAM][!Email!][/PARAM][/METHOD]
                        [METHOD LeMail|ReplyTo][PARAM][!Email!][/PARAM][/METHOD]
                        [METHOD LeMail|To][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
                        [METHOD LeMail|Bcc][PARAM]enguer@enguer.com[/PARAM][/METHOD]
                        [METHOD LeMail|Body]
                           [PARAM]
                                [BLOC Mail]
                                <font face="arial" color="#000000" size="2">
                                    <strong>Objet de la demande</strong> : [!Subject!]<br/>
                                    <strong>Envoyé par</strong> : <span style="text-transform:uppercase">[!FullName!]</span><br/>
                                    <strong>Adresse e-mail</strong> : [!Email!]<br/>
                                    <strong>Pays</strong> : [!Country!]<br/>
                                    <strong>Message</strong> : [UTIL BBCODE][!Message!][/UTIL]<br /></font>
                                <strong>Adresse Ip</strong> : <span><a href="https://geoiptool.com/fr/?ip=[!SERVER::REMOTE_ADDR!]">[!SERVER::REMOTE_ADDR!]</a></span><br/><br />
                                [/BLOC]
                           [/PARAM]
                        [/METHOD]
                        [METHOD LeMail|BuildMail][/METHOD]
                        [METHOD LeMail|Send][/METHOD]

                    <div class="col-lg-12">
                        [IF [!newsletter!]]
                            //Enregistrement à la newsletter
                            [COUNT Newsletter/GroupeEnvoi/15/Contact/Email=[!Email!]|C]
                            [IF [!C!]=0]
                                [OBJ Newsletter|Contact|Con]
                                // 2 - on vérifie que le contact existe, s'il n'existe pas on le créé
                                [STORPROC Newsletter/GroupeEnvoi/15/Contact/Email=[!Email!]|Con|0|1]
                                    [NORESULT]
                                        [METHOD Con|Set]
                                            [PARAM]Email[/PARAM][PARAM][!Email!][/PARAM]
                                        [/METHOD]
                                        [METHOD Con|Set]
                                            [PARAM]Nom[/PARAM][PARAM][!FullName!][/PARAM]
                                        [/METHOD]
                                        [METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
                                        [METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/15[/PARAM][/METHOD]
                                        [METHOD Con|Save][/METHOD]
                                        <div class="alert alert-success">__NEWSLETTER_SUSCRIBE_SUCCESS__</div>
                                    [/NORESULT]
                                [/STORPROC]

                                // 3 - enregistrement du message
                                [OBJ Newsletter|Reception|Rec]
                                [METHOD Rec|Set]
                                    [PARAM]Contenu[/PARAM]
                                    [PARAM][!Message!][/PARAM]
                                [/METHOD]
                                [METHOD Rec|Set]
                                    [PARAM]Destinatire[/PARAM]
                                    [PARAM][!CONTACTMAIL!][/PARAM]
                                [/METHOD]
                                [METHOD Rec|Set]
                                    [PARAM]Sujet[/PARAM]
                                    [PARAM][!Subject!][/PARAM]
                                [/METHOD]
                                [METHOD Rec|AddParent]
                                    [PARAM]Newsletter/Contact/[!Con::Id!][/PARAM]
                                [/METHOD]
                                [METHOD Rec|Save][/METHOD]
                            [ELSE]
                            <div class="alert alert-warning">__NEWSLETTER_SUSCRIBE_WARNING__</div>
                            [/IF]
                        [/IF]
                        [STORPROC [!CONF::MODULE!]|Mod]
                            [IF [!Key!]=NEWSLETTER]
                                // 1 - on vérifie que le groupe existe, s'il n'existe pas on le créé
                                [STORPROC Newsletter/GroupeEnvoi/10|GR|0|1]
                                [/STORPROC]

                                // 2 - on vérifie que le contact existe, s'il n'existe pas on le créé
                                [STORPROC Newsletter/GroupeEnvoi/10/Contact/Email=[!Email!]|Con|0|1]
                                    [NORESULT]
                                        [OBJ Newsletter|Contact|Con]
                                        [METHOD Con|Set]
                                            [PARAM]Email[/PARAM]
                                            [PARAM][!Email!][/PARAM]
                                        [/METHOD]
                                        [METHOD Con|Set]
                                            [PARAM]Nom[/PARAM]
                                            [PARAM][!FullName!][/PARAM]
                                        [/METHOD]
                                        [METHOD Con|AddParent]
                                            [PARAM]Newsletter/GroupeEnvoi/[!GR::Id!][/PARAM]
                                        [/METHOD]
                                        [METHOD Con|Save][/METHOD]
                                    [/NORESULT]
                                [/STORPROC]

                                // 3 - enregistrement du message
                                [OBJ Newsletter|Reception|Rec]
                                [METHOD Rec|Set]
                                    [PARAM]Contenu[/PARAM]
                                    [PARAM][!Message!][/PARAM]
                                [/METHOD]
                                [METHOD Rec|Set]
                                    [PARAM]Destinatire[/PARAM]
                                    [PARAM][!CONTACTMAIL!][/PARAM]
                                [/METHOD]
                                [METHOD Rec|Set]
                                    [PARAM]Sujet[/PARAM]
                                    [PARAM][!Subject!][/PARAM]
                                [/METHOD]
                                [METHOD Rec|AddParent]
                                    [PARAM]Newsletter/Contact/[!Con::Id!][/PARAM]
                                [/METHOD]
                                [METHOD Rec|Save][/METHOD]
                            [/IF]
                        [/STORPROC]
                    </div>

                    // Mail de confirmation
                    [LIB Mail|LeMail]
                    [METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - Confirmation[/PARAM][/METHOD]
                    [METHOD LeMail|From][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
                    [METHOD LeMail|ReplyTo][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
                    [METHOD LeMail|To][PARAM][!Email!][/PARAM][/METHOD]
                    [METHOD LeMail|Body]
                        [PARAM]
                            [BLOC Mail]
                            __HELLO__ [!FullName!],<br />
                            __MSG_RECEPTION__
                            [/BLOC]
                        [/PARAM]
                    [/METHOD]
                    [METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
                    [METHOD LeMail|BuildMail][/METHOD]
                    [METHOD LeMail|Send][/METHOD]
                    <div class="col-lg-12">
                        <div class="alert alert-success">__MESSAGE_SENT_SUCCESSFULLY__</div>
                    </div>
                    [!SHOW_FORM:=0!]
                [/IF]
            [/IF]
            [IF [!SHOW_FORM!]=1]
                    <div class="col-lg-4-1">
                        <input type="text" class="form-control error formu" placeholder="__FULL_NAME__" name="FullName" value="[!FullName!]">
                    </div>
                    <div class="col-lg-4-2">
                        <input type="text" class="form-control formu" placeholder="__EMAIL_ADDRESS__" name="Email" value="[!Email!]">
                    </div>
                </div>
                <div class="col-lg-4-1">
                    <input type="text" class="form-control formu" placeholder="__SUBJECT__" name="Subject" value="[!Subject!]">
                </div>
                <div class="col-lg-4-2">
                    <textarea rows="13" id="textarea" class="form-control formu" placeholder="__MESSAGE__" name="Message" style="text-transform:none;">[!Message!]</textarea>
                </div>
                <div class="col-lg-4-3">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="agree" value="1" [IF [!SendContact!]=1&&[!agree!]=][ELSE]checked="checked"[/IF]> __AGREE_ALL_TERMS_AND_CONDITIONS__
                        </label>
                    </div>
                </div>
                <div class="col-lg-4-3">
                    <input type="hidden" name="SendContact" value="1" />
                    <button class="btn btn-primary btn-send formu pull-right" type="submit" >__SEND_MESSAGE__</button>
                </div>
                <div class="alert alert-danger message" style="display:none;margin:50px 0;">__MESSAGE_SAV_CONTACT__</div>
            [/IF]
                        </div>
                    </form>
    </article>
    <article>
        <header class="ico">
            <h1>Nous situer</h1>
        </header>
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d11513.783715314616!2d4.305452!3d43.825851!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x4d6b2fea967aa820!2sL&#39;ACE!5e0!3m2!1sfr!2sfr!4v1467287712857" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
        <br />
        <h2>
            Adresse
        </h2>
        <hr />
        <p itemprop="address" >
            1530   che du Carreau de Lanes  30900 Nîmes
        </p>
        <hr />
        <p>Notre établissement se situe à Nimes dans le département du Gard.</p>
    </article>
</section>