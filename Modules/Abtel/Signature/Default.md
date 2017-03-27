[HEADER JS]Tools/Js/CkEditor/ckeditor.js[/HEADER]
[IF [!Systeme::User::Public!]=1][REDIRECT]/[/REDIRECT][/IF]
<div id="signatureAbt">
        <div id="signForm" class="col-md-6">
                <div class="abtelContent">
                        <div class="row">
                                <div class="formGrp col-md-6">
                                        <label for="formSigEntity">Entité : </label>
                                        <select name="formSigEntity" id="formSigEntity">
                                                [STORPROC Abtel/Entite|E]
                                                <option value="[!E::Id!]">[!E::Nom!]</option>
                                                [/STORPROC]
                                        </select>
                                </div>
                                <div class="clear"></div>
                                <div class="formGrp col-md-6">
                                        <label for="formSigNom">Nom : </label>
                                        <input type="text" name="formSigNom" id="formSigNom" value="[!formSigNom!]">
                                </div>
                                <div class="formGrp col-md-6">
                                        <label for="formSigPrenom">Prenom : </label>
                                        <input type="text" name="formSigPrenom" id="formSigPrenom" value="[!formSigPrenom!]">
                                </div>
                                <div class="formGrp col-md-6">
                                        <label for="formSigFonction">Fonction : </label>
                                        <input type="text" name="formSigFonction" id="formSigFonction" value="[!formSigFonction!]">
                                </div>
                                <div class="formGrp col-md-6">
                                        <label for="formSigTel">Tel : </label>
                                        <input type="text" name="formSigTel" id="formSigTel" placeholder="Facultatif" value="[!formSigTel!]">
                                </div>
                                <div class="formGrp col-md-6">
                                        <select id="faxGsm">
                                                <option value="Fax">Fax</option>
                                                <option value="Gsm">Gsm</option>
                                        </select>
                                        <input type="text" name="formSigFax" id="formSigFax" placeholder="Facultatif" value="[!formSigFax!]">
                                </div>
                                <div class="formGrp col-md-6">
                                        <label for="formSigEmail">Email : </label>
                                        <input type="text" name="formSigEmail" id="formSigEmail" value="[!formSigEmail!]">
                                </div>
                                <div id="mpSig" class="formGrp col-md-12">
                                        <label for="formSigPerso">Message Perso : </label>
                                        <div class="onoffswitch">
                                                <input type="checkbox" name="showMp"  class="onoffswitch-checkbox" id="showMp" checked>
                                                <label class="onoffswitch-label" for="showMp">
                                                        <span class="onoffswitch-inner"></span>
                                                        <span class="onoffswitch-switch"></span>
                                                </label>
                                        </div>
                                        <div id="ckeMPContainer">
                                                <textarea name="formSigPerso" id="formSigPerso">[!formSigPerso!]</textarea>
                                        </div>
                                </div>
                                [IF [!Systeme::User::Privilege!]]
                                <div id="signPubChange" class="col-md-12">
                                        <h3>Choix de la Publicité :</h3>
                                        [MODULE Abtel/Signature/ModImg]
                                </div>
        
                                [/IF]
                        </div>
                </div>
        </div>
        <div id="signCode" class="col-md-6">
                <div class="abtelTabs">
                        <ul class="nav nav-tabs" role="tablist" id="ongletsSign">
                                <li role="presentation" class="active"><a href="#apercuSig" role="tab" data-toggle="tab">Aperçu de la signature</a></li>
                                <li role="presentation"><a href="#codeSig" role="tab" data-toggle="tab">Code de la signature</a></li>
                                <li id="setSig"><button id="setSigButton">Exporter la signature</button></li>
                                <li id="copySig"><button id="copySigButton">Copier la signature</button></li>
                        </ul>
                        <div class="tab-content">
                                <div role="tabpanel" class="tab-pane" id="codeSig">
                                        <pre id="displayCode" class="brush: html"></pre>
                                </div>
                                <div role="tabpanel" class="tab-pane active" id="apercuSig">
                                        <div id="signPreview">
                                                <div class="abtelContent">
                                                        <div id="sigContain">
                                                                <div id="hiddenCode"><table style="background-color: #fff; width: 530px;font-family: Arial !important;letter-spacing: 0px !important;font-size: 11pt;color: #000;">
<tbody>
        <tr style="height: 4px;"></tr>
        <tr>
                [STORPROC Abtel/Entite/Nom=Groupe|EG][/STORPROC]
                <td rowspan="4" id="logoSig" style="padding:0px 10px;text-align: center;vertical-align: middle;width: 120px; border-right: 1px solid [!EG::CodeCouleur!];">
                        <img src="http://dev.abtel.fr/[!EG::LogoSignature!]" alt="Logo Abtel" title="" style="width: 140px;">
                </td>
                <td id="entSig" style="padding: 0px 10px; font-size: 14px;line-height: 14px !important;height: 21px;padding-bottom: 7px; width: 330px;" colspan="2">
                        <span id="nomSig" style="text-transform: uppercase; font-weight: 600;">nom</span> <span id="prenomSig" style="text-transform: capitalize">prenom</span> - <span id="fonctionSig" >Fonction</span>
                </td>
        </tr>
        <tr id="mainSignContent">
                <td style="padding: 0 10px;width: 200px;font-size: 11px;">
                        <div style="margin: 0;margin-bottom: 0 !important;font-family: Arial !important;line-height: 15px !important;height: 15px;"><span style="display: inline-block;width: 35px;">Tel</span> <span id="telSig" style="color: [!EG::CodeCouleur!]; font-weight:600; font-size: 14px;">04 66 04 06 13</span></div>
                        <div style="margin: 0;margin-bottom: 0 !important;font-family: Arial !important;line-height: 15px !important;height: 15px;"><span id="fax_gsm" style="display: inline-block;width: 35px;">Fax</span> <span id="faxSig" style="color: [!EG::CodeCouleur!]; font-weight:600; font-size: 14px">04 66 04 09 80</span></div>
                </td>
                <td style="font-size: 11px; padding: 0 10px;">
                        <div style="margin: 0;margin-bottom: 0 !important;font-family: Arial !important;line-height: 13px !important;height: 11px;">Parc delta</div>
                        <div style="margin: 0;margin-bottom: 0 !important;font-family: Arial !important;line-height: 13px !important;height: 11px;">Km 4 - Route d'Arles</div>
                        <div style="margin: 0;margin-bottom: 0 !important;font-family: Arial !important;line-height: 13px !important;height: 11px;">30230 Bouillargues</div>
                </td>
        </tr>
        <tr id="siteWeb">
                <td style="font-size: 11px; padding: 5px 10px 0 10px; font-family: Arial !important;" colspan="2">
                        <span id="mailSig" ><a href="mailto:" style="color: #992299;text-decoration: none !important;">Mail</a></span> - <a href="http://www.abtel.fr">www.abtel.fr</a>
                </td>
        </tr>
        <tr id="msgPerso">
                <td id="persoSig" colspan="2" style="padding: 5px 10px;font-size: 15px;">
                        <div style="color: green; font-family: verdana, helvetica, sans-serif; font-size: 8pt; font-weight: 600;font-style: italic;margin: 0;">
                Pensez ENVIRONNEMENT : n'imprimez que si nécessaire !
                        </div>
                </td>
        </tr>
        <tr>
                <td id="pubSig" colspan="3" style="line-height: 0px">
                        <img src="http://dev.abtel.fr/[!EG::PubSignature!]?[!TMS::Now!]" alt="Pub Abtel" title="Pub Abtel">
                </td>
        </tr>
</tbody>        
</table>
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>       
                                </div>
                        </div>
                </div>
        </div>
</div>
<script type="text/javascript">
        $('#myTabs a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
        });

        var entites = {
                [STORPROC Abtel/Entite|E]
                        [!E::Id!]:    {
                                                Logo:"[!E::LogoSignature!]",
                                                Nom:"[!E::Nom!]",
                                                Couleur:"[!E::CodeCouleur!]"
                                        },
                [/STORPROC]
        }
        
        
        function escapeHtml(text) {
                return text
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
        }
        function refreshCode() {
                $('.mCS_img_loaded').removeAttr('class');
                $("#displayCode").replaceWith('<pre id="displayCode" class="brush: html"></pre>');
                $("#displayCode").html(escapeHtml($("#hiddenCode").html()));
                SyntaxHighlighter.highlight();
        }
        $(document).on('ready',function(){
                refreshCode();
                CKEDITOR.replace('formSigPerso', {
	    		toolbar: 'Basic'
		});
                CKEDITOR.instances.formSigPerso.on('change', function() {
                        $('#persoSig').html('\n\t\t\t\t\t\t\t\t'+CKEDITOR.instances.formSigPerso.getData()+'\n\t\t\t\t\t\t');
                        refreshCode();
                });
                $('#formSigEntity').on('change',function(){
                        var ent = entites[$('#formSigEntity').val()];
                        ent.Nom = ent.Nom != 'Groupe'? ent.Nom : 'Méditerranée'
                        $('#logoSig img').attr('src', 'http://dev.abtel.fr/'+ent.Logo);
                        $('#logoSig').css('border-right','1px solid '+ent.Couleur);
                        $('#telSig').css('color',ent.Couleur);
                        $('#faxSig').css('color',ent.Couleur);
                        refreshCode();
                });
                $('#formSigNom').on('change',function(){
                        $('#nomSig').text($('#formSigNom').val());
                        refreshCode();
                });
                $('#formSigPrenom').on('change',function(){
                        $('#prenomSig').text($('#formSigPrenom').val());
                        refreshCode();
                });
                $('#formSigFonction').on('change',function(){
                        $('#fonctionSig').text($('#formSigFonction').val());
                        refreshCode();
                });
                $('#formSigTel').on('change',function(){
                        $('#telSig').text($('#formSigTel').val());
                        refreshCode();
                });
                $('#formSigFax').on('change',function(){
                        $('#faxSig').text($('#formSigFax').val());
                        refreshCode();
                });
                $('#formSigEmail').on('change',function(){
                        $('#mailSig a').text($('#formSigEmail').val());
                        $('#mailSig a').attr('href','mailto:'+$('#formSigEmail').val());
                        refreshCode();
                });
                $('#faxGsm').on('change',function(){
                        $('#fax_gsm').text($('#faxGsm').val());
                        refreshCode();
                });
                $('#showMp').on('change',function(){
                        var messPerso = $('#showMp')[0].checked;
                        if (messPerso) {
                                $('#ckeMPContainer').show();
                                $('#logoSig').attr('rowspan',parseInt($('#logoSig').attr('rowspan'))+1);
                                var defaultMp = '<div style="color: green; font-family: verdana, helvetica, sans-serif; font-size: 8pt; font-weight: 600;font-style: italic;margin: 0;"> \
                                                        Pensez ENVIRONNEMENT : n\'imprimez que si nécessaire ! \
                                                </div>';
                                var content = CKEDITOR.instances.formSigPerso.getData() != '' ? CKEDITOR.instances.formSigPerso.getData() : defaultMp;
                                var line= '     <tr id="msgPerso"> \
                                                        <td id="persoSig" colspan="2" style="padding: 5px 10px;font-size: 11pt;"> \
                                                        '+content+' \
                                                        </td> \
                                                </tr>';
                                $('#siteWeb').after(line);
                                
                        } else{
                                $('#ckeMPContainer').hide();
                                $('#logoSig').attr('rowspan',parseInt($('#logoSig').attr('rowspan'))-1);
                                $('#msgPerso').remove();
                        }
                        refreshCode();
                });
                
                //Copie le html de la signature dnas le presse papier.
                $('#copySigButton').on('click',function(){
                        var src = document.getElementById('hiddenCode');
                        
                        // create hidden text element, if it doesn't already exist
                        var targetId = "_hiddenCopyText_";
                        var isInput = src.tagName === "INPUT" || src.tagName === "TEXTAREA";
                        var origSelectionStart, origSelectionEnd;
                        if (isInput) {
                            // can just use the original source element for the selection and copy
                            target = src;
                            origSelectionStart = src.selectionStart;
                            origSelectionEnd = src.selectionEnd;
                        } else {
                                var source = $('#hiddenCode').html();
                            // must use a temporary form element for the selection and copy
                            target = document.getElementById(targetId);
                            if (!target) {
                                var target = document.createElement("textarea");
                                target.style.position = "absolute";
                                target.style.left = "-9999px";
                                target.style.top = "0";
                                target.id = targetId;
                                document.body.appendChild(target);
                            }
                            target.textContent = source;
                        }
                        // select the content
                        var currentFocus = document.activeElement;
                        target.focus();
                        target.setSelectionRange(0, target.value.length);
                        
                        // copy the selection
                        var succeed;
                        try {
                              succeed = document.execCommand("copy");
                        } catch(e) {
                            succeed = false;
                        }
                        // restore original focus
                        if (currentFocus && typeof currentFocus.focus === "function") {
                            currentFocus.focus();
                        }
                        
                        if (isInput) {
                            // restore prior selection
                            elem.setSelectionRange(origSelectionStart, origSelectionEnd);
                        } else {
                            // clear temporary content
                            target.textContent = "";
                        }
                        return succeed;
                });
                
                
                $('#setSigButton').on('click',function(){
                        var src = document.getElementById('hiddenCode');
                        
                        var signature = src.innerHTML;
                        
                        $.post('/Abtel/Signature/Zimbra.json',{'signature':signature, 'action':'addSignature'},function(result){
                                console.log(result);    
                        });
                        //return succeed;
                });
        });
        
</script>