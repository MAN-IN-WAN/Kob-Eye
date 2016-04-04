<div class="block">
        <h3 class="title_block">Préparation de vos ordonnances</h3>
        <div class="block_content">
                <div class="row" style="margin-bottom:50px;">
                    <div class="col-md-3">
                        <img src="/Skins/[!Systeme::Skin!]/Img/preparatrice.jpg" class="img-responsive" />
                    </div>
                    <div class="col-md-9">
                        <p>
                            Nous préparons vos ordonnances dès que vous nous avez envoyé une copie de votre ordonnance (scan / photo ...). Dès la réception de votre ordonnance nos préparateurs commenceront à la traiter et vous recevrez aussitôt un email ainsi qu'un sms vous informant de l'état de votre commande. Uen fois la commande prête, vous recevrez à nouveau un email ainsi qu'un sms contenant le numéro de préparation afin que vous puissiez retirer votre commande au guichet à la file rapide prévue à cet effet. Si vous avez des question n'hésitez pas à nous contacter.
                        </p>
                    </div>
                </div>


[IF [!SendContact!]!=]
    //enregistrement de la demande
    [OBJ Pharmacie|Ordonnance|Or]
    [STORPROC [!Or::Proprietes!]|P]
        [METHOD Or|Set]
            [PARAM][!P::Nom!][/PARAM]
            [PARAM][!Form_[!P::Nom!]!][/PARAM]
        [/METHOD]
    [/STORPROC]

    //SPECIAL BATTISTELLA
    [IF [!Form_SachetDose!]]
            [!TEXT:=<br /> PREPRATION SACHET DOSE!]
            [IF [!Form_Livraison!]]
                [!TEXT+=<br /> LIVRAISON DOMICILE!]
            [/IF]
            [METHOD Or|Set]
                [PARAM]Commentaire[/PARAM]
                [PARAM][!Form_Commentaire!] [!TEXT!] [/PARAM]
            [/METHOD]
    [/IF]


    [IF [!Or::Verify()!]]
        //Enregistrement
        [METHOD Or|Save][/METHOD]
        <div class="alert alert-success">
            Votre demande de préparation d'ordonnance a bien étées prise en compte. Nous vous avertirons par mail et/ou par sms de l'état de la préparation.
        </div>
    [ELSE]
        //Affichage des erreurs
        [!Form_Error:=1!]
        <div class="alert alert-danger">
        [STORPROC [!Or::Error!]|E]
            [!Form_[!C::Prop!]_Error:=1!]
            <div>[!E::Message!]</div>
        [/STORPROC]
        </div>
    [/IF]
[/IF]
[IF [!Sys::User::Public!]=]
    [IF [!SendContact!]=||[!Form_Error!]]
            <form id="FormContact" method="post" action="/[!Lien!]" class="form-horizontal" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                    <div class="form-group  [IF [!Form_Image_Error!]]error[/IF]">
                                        <label class="control-label col-sm-4" for="Form_Mail">Image de l'ordonnance <span class="Obligatoire">*</span></label>
                                        <div class="col-sm-6">
                                            <span class="exclusive btn-file">
                                                __BROWSE__ <input type="file" id="Form_Image" name="Form_Image" value="[!Form_Image!]" class="input-block-level" required/>
                                            </span>
                                        </div>
                                    </div>
                                <div class="form-group  [IF [!Form_Commentaire_Error!]]error[/IF]">
                                    <label class="control-label col-sm-4" for="Form_Commentaire">Commentaire(s)</label>
                                    <div class="col-sm-6">
                                        <textarea id="Form_Image" name="Form_Commentaire" id="Form_Commentaire" >[!Form_Commentaire!]</textarea>
                                    </div>
                                </div>
                                <div class="form-group  [IF [!Form_SachetDose_Error!]]error[/IF]">
v                                    <label class="control-label col-sm-4" for="Form_SachetDose">Souhaitez-vous une préparation en sachet dose ?</label>
                                    <div class="col-sm-6">
                                        <input id="Form_SachetDose" name="Form_SachetDose" value="1" type="checkbox" [IF [!Form_SachetDose!]]checked="checked"[/IF] />
                                    </div>
                                </div>
                                <div id="livraison" style="display:none;" class="form-group  [IF [!Form_SachetDose_Error!]]error[/IF]">
                                    <label class="control-label col-sm-4" for="Form_Livraison">Souhaitez-vous une livraison ?</label>
                                    <div class="col-sm-6">
                                        <input id="Form_Livraison" name="Form_Livraison" value="1" type="checkbox" [IF [!Form_Livraison!]]checked="checked"[/IF] />
                                    </div>
                                </div>
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <p>Retirez votre commande directement à l'officine sans attente en utilisant la borne à votre arrivée.</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <input type="hidden" name="SendContact" value="1">
                            <div class="col-md-5 col-md-offset-7">
                                    <button type="submit" class="btn btn-primary" >Envoyer</button>
                                    <a href="/[!Systeme::CurrentMenu::Url!]" class="btn btn-danger">Annuler</a>
                            </div>
                        </div>
                <script>
                    $(function () {
                        console.log('test');
                        $("#Form_SachetDose").on('change',function (e) {
                            if ($(e.target).is(':checked')) $('#livraison').css('display','block');
                            else{
                                $('#livraison').css('display','none');
                                $('#Form_Livraison').prop('checked', false);
                            }
                        });
                    });
                </script>
            </form>

                <div class="row" style="margin-top: 50px;">
                        <div class="col-md-6">
                            [OBJ Boutique|Magasin|M]
                            [!Mag:=[!M::getCurrentMagasin()!]!]
                            <p>Les champs marqués (<span class="Obligatoire">*</span>) sont obligatoires.</p>
                            <p class="ContactTel">Vous pouvez aussi nous contacter par :<br />
                            Tel : [!Mag::Tel!]<br />
                            Fax : [!Mag::Fax!]</p>
                        </div>
                        <div class="col-md-6">
                            <p>
                            Conformément à la loi n°78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers et aux libertés,
                            vous disposez d'un droit d'accès, de rectification, de suppression des informations qui vous concernent que vous pouvez exercer en vous adressant à
                            [!Mag::Nom!] - [!Mag::Adresse!] - [!Mag::CodPos!] [!Mag::Ville!] - [!Mag::Pays!].
                            </p>

                            <p>
                            [!TEXT_BAS!]
                            </p>
                        </div>
                </div>

    [/IF]
[ELSE]
            <div class="alert alert-warning">Afin d'utiliser le service d'envoi d'ordonnances, veuillez vous connecter avec votre compte utilisateur ou créez un compte en cliquant sur le bouton ci-dessous:</div>
            <a class="btn btn-success btn-large" href="/Mon-compte">Connectez-vous</a>
[/IF]
            </div>
</div>
