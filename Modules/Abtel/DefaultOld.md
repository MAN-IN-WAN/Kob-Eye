[HEADER JS]Tools/Js/CkEditor/ckeditor.js[/HEADER]
<div id="signatureAbt">
        <div id="upperSig" >
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
                                                <label for="formSigFax">Fax : </label>
                                                <input type="text" name="formSigFax" id="formSigFax" placeholder="Facultatif" value="[!formSigFax!]">
                                        </div>
                                        <div class="formGrp col-md-6">
                                                <label for="formSigEmail">Email : </label>
                                                <input type="text" name="formSigEmail" id="formSigEmail" value="[!formSigEmail!]">
                                        </div>
                                        <div id="mpSig" class="formGrp col-md-12">
                                                <label for="formSigPerso">Message Perso : </label>
                                                <textarea name="formSigPerso" id="formSigPerso">[!formSigPerso!]</textarea>
                                        </div>
                                </div>
                        </div>
                </div>
                <div id="signCode" class="col-md-6">
                        <div class="abtelContent">
                                <button id="copySig">Copier la signature</button>
                                <pre id="displayCode" class="brush: html">
                                        
                                </pre>
                        </div>
                </div>
        </div>
        <div id="lowerSig" class="row">
                <div id="signPreview" class="col-md-12">
                        <div class="abtelContent">
                                <div id="sigContain">
                                       <div id="hiddenCode"><table style="background-color: #fff; max-width: 600px;font-family: sans-serif;font-size: 11pt;color: #000;">
        <tbody>
                <tr>
                        [STORPROC Abtel/Entite/Nom=Groupe|EG][/STORPROC]
                        <td rowspan="3" id="logoSig" style="background-color:[!EG::CodeCouleur!];padding:10px;padding-top: 25px;text-align: center;vertical-align: middle;width: 120px;font-size: 11pt;">
                                <img src="http://dev.abtel.fr/[!EG::Logo!]" alt="Logo Abtel" title="" style="width: 100px;">
                        </td>
                        <td id="entSig" style="color:[!EG::CodeCouleur!];padding: 0 10px; padding-bottom: 5px;text-align: center;font-weight: 600;font-size: 11pt;" colspan="2">
                                Abtel <span>Méditerranée</span>
                        </td>
                </tr>
                <tr>
                        <td style="padding: 0 10px;width: 200px;font-size: 11pt;">
                                <p style="margin: 0;"><span id="nomSig" style="text-transform: uppercase; font-weight: 600;">nom</span> <span id="prenomSig" style="text-transform: capitalize">prenom</span></p>
                                <p style="margin: 0;"><span id="fonctionSig">Fonction</span></p>
                                <p style="margin: 0;">Tel : <span id="telSig" style="color: #4444aa;">04 66 04 06 13</span></p>
                                <p style="margin: 0;">Fax : <span id="faxSig" style="color: #4444aa;">04 66 04 09 80</span></p>
                                <p style="margin: 0;"><span id="mailSig" style="color: #992299;">Mail</span></p>
                        </td>
                        <td style="font-size: 11pt;">
                                <p style="margin: 0;">Parc delta</p>
                                <p style="margin: 0;">Km 4 - Route d'Arles</p>
                                <p style="margin: 0;">30230 Bouillargues</p>
                                <p style="margin: 0;">France</p>
                                <p style="margin: 0;"><a href="http://www.abtel.fr">http://www.abtel.fr</a></p>
                        </td>
                </tr>
                <tr>
                        <td id="persoSig" colspan="2" style="padding: 0 10px; padding-top: 5px;font-size: 11pt;">
                                <p style="color: green; font-family: verdana, helvetica, sans-serif; font-size: 8pt; font-weight: 600;font-style: italic;margin: 0;">
                                        Pensez ENVIRONNEMENT : n'imprimez que si nécessaire !
                                </p>
                        </td>
                </tr>
                <tr>
                        <td id="pubSig" colspan="3">
                                 <img src="http://dev.abtel.fr/[!EG::PubSignature!]?[!TMS::Now!]" alt="Pub Abtel" title="Pub Abtel">
                        </td>
                </tr>
        </tbody>        
</table></div>
        
                                </div>
                        </div>
                </div>
        [IF [!Systeme::User::Privilege!]]
                <div id="signPubChange" class="col-md-12">
                        [MODULE Abtel/Signature/ModImg]
                </div>
        [/IF]
        </div>
</div>
<script type="text/javascript">
        var entites = {
                [STORPROC Abtel/Entite|E]
                        [!E::Id!]:    {
                                                Logo:"[!E::Logo!]",
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
                        $('#logoSig').css('background-color',ent.Couleur);
                        $('#entSig span').text(ent.Nom);
                        $('#entSig').css('color',ent.Couleur);
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
                        $('#mailSig').text($('#formSigEmail').val());
                        refreshCode();
                });
                
                //$('#formSigPerso').on('change',function(){
                //        $('#persoSig').html($('#formSigEmail').val());
                //        refreshCode();
                //});
                
                //Copie le html de la signature dnas le presse papier.
                $('#copySig').on('click',function(){
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
        });
        
</script>