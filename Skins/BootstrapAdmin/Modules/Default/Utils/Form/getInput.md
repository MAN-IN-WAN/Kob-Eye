[SWITCH [!P::type!]|=]
    [CASE duration]
        [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
    <label class="col-sm-5 control-label">[!P::description!]</label>
    <div class="col-sm-7 form-value">
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
<div class="col-sm-7 form-value">
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
<div class="col-sm-7 form-value">
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
<div class="col-sm-7 form-value">
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
        [CASE fkey]
        [IF [!P::card!]=long][ELSE]
        [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label class="col-sm-5 control-label">[!P::parentDescription!]</label>
<div class="col-sm-7 form-value">
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
                <div class="col-sm-7 form-value">
                    <div id="input-Image-[!P::name!]-view" [IF [!DF!]][ELSE]style="display: none;"[/IF]>
                        <img src="/[!DF!]"   class="img-responsive" style="max-height: 200px;"/>
                        <a href="#nogo" class="btn btn-danger pull-right" id="input-Image-[!P::name!]-reset"><i class="fa fa-times"></i>&nbsp;Supprimer</a>
                     </div>
                    <input type="hidden" class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]" value="[!DF!]" />
                    <input id="input-Image-[!P::name!]" type="file" multiple="false" class="file-loading"/>
                    <script>
                        $('#input-Image-[!P::name!]-reset').click(function () {
                            $('#Form_[!P::name!]').val('');
                            $('#input-Image-[!P::name!]-view').css('display', 'none');
                        });
                        //$(document).on('ready', function() {
                        $("#input-Image-[!P::name!]").fileinput({showCaption: false, showPreview: true, language: 'fr', uploadUrl: '/Systeme/Utils/Form/Upload.htm', dropZoneEnabled: false});
                        //});
                        $('#input-Image-[!P::name!]').on('fileuploaded', function(event, data, previewId, index) {
                            console.log('document upload ', data);
                            $('#Form_[!P::name!]').val(data.response.url);
                            $('#input-Image-[!P::name!]-view').css('display', 'initial');
                        });
                    </script>
                </div>
            </div>
        [/CASE]
        [CASE file]
        [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label for="Form_[!P::name!]" class="col-sm-5 control-label">[!P::description!]</label>
<div class="col-sm-7 form-value">
    <input type="hidden" class="ImageInput" id="Form_[!P::name!]" name="Form_[!P::name!]" value="[!DF!]"/>
    <input id="input-Image-[!P::name!]" type="file" multiple=false class="file-loading"/>
    <script>
        //$(document).on('ready', function() {
        $("#input-Image-[!P::name!]").fileinput({showCaption: false, showPreview: true, language: 'fr', uploadUrl: '/Systeme/Utils/Form/Upload.htm', dropZoneEnabled: false});
        //});
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
<div class="col-sm-7 form-value">
    <input type="password" class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]" placeholder="" value="[!DF!]">
</div>
</div>
        [/CASE]
        [CASE text]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label for="Form_[!P::name!]" class="col-sm-5 control-label">[!P::description!]</label>
<div class="col-sm-7 form-value">
    <textarea class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]" >[IF [!Form_[!P::name!]!]][**Form_[!P::name!]**][ELSE][**P::value**][/IF]</textarea>
</div>
</div>
        [/CASE]
        [CASE html]

<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label for="Form_[!P::name!]" class="control-label">[!P::description!]</label>
<div class=" form-value">
                    <textarea id="Form_[!P::name!]" name="Form_[!P::name!]" class="ckeditor form-control">
                        [IF [**Form_[!P::name!]**]][**Form_[!P::name**]!][ELSE][**P::value**][/IF]
                    </textarea>
    <script>
        CKEDITOR.replace( 'Form_[!P::name!]' );
        CKEDITOR.instances['Form_[!P::name!]'].on('change', function() { CKEDITOR.instances['Form_[!P::name!]'].updateElement() });
        CKEDITOR.instances['Form_[!P::name!]'].on('paste', function() { CKEDITOR.instances['Form_[!P::name!]'].updateElement() });
    </script>
</div>
</div>
        [/CASE]
        [CASE function]
        [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!O::[!P::function!]()!]!][/IF]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label for="Form_[!P::name!]" class="col-sm-5 control-label">[!P::description!]</label>
<div class="col-sm-7 form-value">
    <input type="text" class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]" value="[!DF!]"/>
</div>
</div>
        [/CASE]
        [CASE raw]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label for="Form_[!P::name!]" class="control-label">[!P::description!]</label>
<div class=" form-value">
    <textarea class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]" style="height: 400px">[IF [!Form_[!P::name!]!]][**Form_[!P::name!]**][ELSE][**P::value**][/IF]</textarea>
</div>
</div>
        [/CASE]
        [DEFAULT]
        [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[**P::value**]!][/IF]
        [IF [!P::Values!]]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label class="col-sm-5 control-label">[!P::description!]</label>
<div class="col-sm-7 form-value">
    <select class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]">
        [STORPROC [!P::Values!]|C]
        [!T:=[![!C!]:/::!]!]

        [COUNT [!T!]|S]
        [IF [!S!]>1]
            <option value="[!T::0!]" [IF [!DF!]=[!T::0!]]selected="selected"[/IF]>[!T::1!]</option>
        [ELSE]
            <option value="[!C!]" [IF [!DF!]=[!C!]]selected="selected"[/IF]>[!C!]</option>
        [/IF]

    [/STORPROC]
</select>
</div>
        </div>
        [ELSE]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label for="Form_[!P::name!]" class="col-sm-5 control-label">[!P::description!]</label>
<div class="col-sm-7 form-value">
    <input type="text" class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]" placeholder="" value="[IF [!Form_[!P::name!]!]][**Form_[!P::name!]**][ELSE][**P::value**][/IF]">
</div>
</div>
        [/IF]
    [/DEFAULT]
[/SWITCH]
