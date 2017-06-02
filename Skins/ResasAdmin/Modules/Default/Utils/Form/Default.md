//Config
[!FORM:=1!]

//validation du formulaire
[IF [!ValidForm!]=1]
    //Engregistrement des champs
    [STORPROC [!O::getElementsByAttribute(form,,1)!]|P]
        [SWITCH [!P::type!]|=]
            [CASE fkey]
                [!O::resetParents([!P::objectName!])!]
                [STORPROC [!Form_[!P::name!]!]|V]
                    [METHOD O|AddParent]
                        [PARAM][!P::objectModule!]/[!P::objectName!]/[!V!][/PARAM]
                    [/METHOD]
                [/STORPROC]
            [/CASE]
            [DEFAULT]
                [METHOD O|Set]
                    [PARAM][!P::name!][/PARAM]
                    [PARAM][!Form_[!P::name!]!][/PARAM]
                [/METHOD]
            [/DEFAULT]
        [/SWITCH]
    [/STORPROC]
     //verfication de la saisie
    [IF [!O::Verify()!]]
        [METHOD O|Save][PARAM]1[/PARAM][/METHOD]
        [!FORM:=0!]
        {
            "success":1,
            "message": "<div class=\"alert alert-success\">Votre élément [!O::getDescription()!] a été sauvegardé avec succès.</div>",
            "controls":{
                "close":1,
                "save":0,
                "cancel":0
            }
        }
    [ELSE]
        [!FORM:=0!]
        //affichage des erreurs
        {
            "success":0,
            "message": "<div class=\"alert alert-danger\">Les erreurs suivantes sont présentes dans le formulaire: <ul>[STORPROC [!O::Error!]|E]<li> [!E::Message!]</li>[/STORPROC]</ul></div>",
            "controls":{
                "close":0,
                "save":1,
                "cancel":1
            }
        }
    [/IF]
[/IF]
[IF [!FORM!]]
[STORPROC [!O::getElementsByAttribute(form,,1)!]|P]

    [SWITCH [!P::type!]|=]
        [CASE duration]
        [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
        <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
            <label class="col-sm-5 control-label">[!P::description!]</label>
            <div class="col-sm-7">
                <select class="form-control" id="Form_[!P::name!][]" name="Form_[!P::name!]">
                    <option value=""></option>
                    <option value="30" [IF [!DF!]=30]selected="selected"[/IF]>30 minutes</option>
                    <option value="60" [IF [!DF!]=60]selected="selected"[/IF]>1 heure</option>
                    <option value="90" [IF [!DF!]=90]selected="selected"[/IF]>1 heure 30</option>
                    <option value="120" [IF [!DF!]=120]selected="selected"[/IF]>2 heures</option>
                    <option value="180" [IF [!DF!]=180]selected="selected"[/IF]>3 heures</option>
                    <option value="360" [IF [!DF!]=360]selected="selected"[/IF]>6 heures</option>
                    <option value="480" [IF [!DF!]=480]selected="selected"[/IF]>8 heures</option>
                    <option value="720" [IF [!DF!]=720]selected="selected"[/IF]>12 heures</option>
                    <option value="1440" [IF [!DF!]=1440]selected="selected"[/IF]>1 jour</option>
                    <option value="2880" [IF [!DF!]=2880]selected="selected"[/IF]>2 jours</option>
                    <option value="10080" [IF [!DF!]=10080]selected="selected"[/IF]>1 semaine</option>
                    <option value="20160" [IF [!DF!]=20160]selected="selected"[/IF]>2 semaines</option>
                    <option value="43200" [IF [!DF!]=43200]selected="selected"[/IF]>1 mois</option>
                    <option value="86400" [IF [!DF!]=86400]selected="selected"[/IF]>2 mois</option>
                    <option value="129600" [IF [!DF!]=129600]selected="selected"[/IF]>3 mois</option>
                    <option value="262800" [IF [!DF!]=262800]selected="selected"[/IF]>6 mois</option>
                    <option value="525600" [IF [!DF!]=525600]selected="selected"[/IF]>1 an</option>
                </select>
            </div>
        </div>
        [/CASE]
        [CASE boolean]
        [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
        <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
            <label class="col-sm-5 control-label">[!P::description!]</label>
            <div class="col-sm-7">
                <input type="checkbox" name="Form_[!P::name!]" [IF [!DF!]]checked="checked"[/IF] class="switch" value="1">
            </div>
        </div>
        [/CASE]
        [CASE datetime]
                [IF [!Form_[!P::name!]!]>0][!DF:=[!Form_[!P::name!]!]!][ELSE]
                [IF [!P::value!]>0]
                [!DF:=[!Utils::getDate(d/m/Y H:i:s,[!P::value!])!]!]
                [ELSE]
                [!DF:=[!Utils::getDate(d/m/Y H:i:s,[!TMS::Now!])!]!]
                [/IF]
                [/IF]
            <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
            <label class="col-sm-5 control-label">[!P::description!]</label>
            <div class="col-sm-7">
                <div class="input-group date" id='datetimepicker[!Pos!]'>
                    <input type="text" class="form-control datepicker" value="[!DF!]" name="Form_[!P::name!]" />
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </div>
                </div>
                //<input type="text" class="form-control datepicker" id="inputDate" placeholder="Sélectionnez une date" value="[!DF!]"  name="Form_[!P::name!]">
            </div>
            </div>
            <script type="text/javascript">
            $(function () {
                $('#datetimepicker[!Pos!]').datetimepicker({
                    locale: 'fr'
                });
            });
            </script>
        [/CASE]
        [CASE date]
        [IF [!Form_[!P::name!]!]>0][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!Utils::getDate(d/m/Y,[!P::value!])!]!][/IF]
        <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
            <label class="col-sm-5 control-label">[!P::description!]</label>
            <div class="col-sm-7">
                <input type="text" class="form-control datepicker" id="inputDate" placeholder="Sélectionnez une date" value="[!DF!]"  name="Form_[!P::name!]">
            </div>
        </div>
        [/CASE]
        [CASE fkey]
            [IF [!P::card!]=long]
                <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
                    <label class="col-sm-5 control-label">[!P::parentDescription!]</label>
                    <div class="col-sm-7">
                        <div class="row">
                        [STORPROC [!P::objectModule!]/[!P::objectName!]|C]
                            <div class="col-md-6">
                                [!C::getFirstSearchOrder()!] [!C::getSecondSearchOrder()!]
                                [IF [!O::Id!]]
                                    [COUNT [!P::objectModule!]/[!P::objectName!]/[!C::Id!]/[!O::ObjectType!]/[!O::Id!]|DF]
                                [ELSE]
                                    [!DF:=0!]
                                [/IF]
                                <input type="checkbox" name="Form_[!P::name!][]" [IF [!DF!]]checked="checked"[/IF] class="switch " value="[!C::Id!]">
                            </div>
                        [/STORPROC]
                        </div>
                    </div>
                </div>
            [ELSE]
                [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
                <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
                    <label class="col-sm-5 control-label">[!P::parentDescription!]</label>
                    <div class="col-sm-7">
                        <select class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!][]">
                            <option value=""></option>
                            [STORPROC [!P::objectModule!]/[!P::objectName!]|C]
                            <option value="[!C::Id!]" [IF [!DF!]=[!C::Id!]]selected="selected"[/IF]>[!C::getFirstSearchOrder()!] [!C::getSecondSearchOrder()!]</option>
                            [/STORPROC]
                        </select>
                    </div>
                </div>
            [/IF]
        [/CASE]
        [CASE image]
        [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
        <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
            <label for="Form_[!P::name!]" class="col-sm-5 control-label">[!P::description!]</label>
            <div class="col-sm-7">
                [IF [!DF!]]
                <img src="/[!DF!]"   class="img-responsive" style="max-height: 200px;"/>
                [/IF]
                <input type="hidden" class="ImageInput" id="Form_[!P::name!]" name="Form_[!P::name!]" value="[!DF!]"/>
                <input id="input-Image-[!P::name!]" type="file" multiple=false class="file-loading"/>
                <script>
                    $(document).on('ready', function() {
                        $("#input-Image-[!P::name!]").fileinput({showCaption: false, showPreview: true, language: 'fr', uploadUrl: '/Systeme/Utils/Form/Upload.htm', dropZoneEnabled: false});
                    });
                    $('#input-Image-[!P::name!]').on('fileuploaded', function(event, data, previewId, index) {
                        console.log('document upload ', data);
                        $('#Form_[!P::name!]').val(data.response.url);
                    });

                </script>
            </div>
        </div>
        [/CASE]
        [CASE password]
            [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
            <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
                <label for="Form_[!P::name!]" class="col-sm-5 control-label">[!P::description!]</label>
                <div class="col-sm-7">
                    <input type="password" class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]" placeholder="" value="[!DF!]">
                </div>
            </div>
        [/CASE]
        [CASE text]
            [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
            <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
                <label for="Form_[!P::name!]" class="col-sm-5 control-label">[!P::description!]</label>
                <div class="col-sm-7">
                    <textarea class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]" >[**DF**]</textarea>
                </div>
            </div>
        [/CASE]
        [DEFAULT]
            [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
            [IF [!P::Values!]]
                <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
                    <label class="col-sm-5 control-label">[!P::description!]</label>
                    <div class="col-sm-7">
                        <select class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]">
                            [STORPROC [!P::Values!]|C]
                            <option value="[!C!]" [IF [!DF!]=[!C!]]selected="selected"[/IF]>[!C!]</option>
                            [/STORPROC]
                        </select>
                    </div>
                </div>
            [ELSE]
                <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
                  <label for="Form_[!P::name!]" class="col-sm-5 control-label">[!P::description!]</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]" placeholder="" value="[!DF!]">
                  </div>
                </div>
            [/IF]
        [/DEFAULT]
    [/SWITCH]
[/STORPROC]
 [/IF]

