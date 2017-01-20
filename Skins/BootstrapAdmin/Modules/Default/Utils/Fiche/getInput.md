[SWITCH [!P::type!]|=]
        [CASE duration]
        [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
    <label class="col-sm-6 control-label">[!P::description!]</label>
    <div class="col-sm-6 form-value">
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
<label class="col-sm-6 control-label">[!P::description!]</label>
<div class="col-sm-6 form-value">
    <input type="checkbox" name="Form_[!P::name!]" [IF [!DF!]]checked="checked"[/IF] class="switch" value="1" disabled="disabled">
</div>
</div>
        [/CASE]
        [CASE datetime]
        [IF [!Form_[!P::name!]!]>0][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!Utils::getDate(d/m/Y H:i,[!P::value!])!]!][/IF]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label class="col-sm-6 control-label">[!P::description!]</label>
<div class="col-sm-6 form-value">
    [!DF!]
    //<input type="text" class="form-control datetimepicker" id="inputDate" placeholder="Sélectionnez une date" value="[!DF!]"  name="Form_[!P::name!]">
</div>
</div>
        [/CASE]
        [CASE date]
        [IF [!Form_[!P::name!]!]>0][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!Utils::getDate(d/m/Y,[!P::value!])!]!][/IF]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label class="col-sm-6 control-label">[!P::description!]</label>
<div class="col-sm-6 form-value">
    [!DF!]
    //<input type="text" class="form-control datepicker" id="inputDate" placeholder="Sélectionnez une date" value="[!DF!]"  name="Form_[!P::name!]">
</div>
</div>
        [/CASE]
        [CASE fkey]
        [IF [!P::card!]=long]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label class="col-sm-12 control-label">[!P::parentDescription!]</label>
<div class="col-sm-12 form-value">
    <div class="row">
        [STORPROC [!P::objectModule!]/[!P::objectName!]|C]
        [IF [!O::Id!]]
        [COUNT [!P::objectModule!]/[!P::objectName!]/[!C::Id!]/[!O::ObjectType!]/[!O::Id!]|DF]
        [ELSE]
        [!DF:=0!]
        [/IF]
        [IF [!DF!]]
        <div class="col-md-12" style="overflow: hidden;">
            [!C::getFirstSearchOrder()!] [!C::getSecondSearchOrder()!]
            <input type="checkbox" name="Form_[!P::name!][]" [IF [!DF!]]checked="checked"[/IF] class="switch " value="[!C::Id!]">
        </div>
        [/IF]
        [/STORPROC]
    </div>
</div>
</div>
        [ELSE]
        [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label class="col-sm-6 control-label">[!P::parentDescription!]</label>
<div class="col-sm-6 form-value">
    <select class="form-control" id="Form_[!P::name!][]" name="Form_[!P::name!]" disabled="disabled">
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
                <label for="Form_[!P::name!]" class="col-sm-6 control-label">[!P::description!]</label>
                <div class="col-sm-6 form-value">
                    [IF [!DF!]]
                        <a href="/[!DF!]" title="[!P::description!]" id="Form_[!P::name!]_lightbox" data-gallery>
                            <img src="/[!DF!]"   class="img-responsive" style="max-height: 200px;"/>
                        </a>
                    [ELSE]
                    <i>Pas d'image</i>
                    [/IF]
                </div>
            </div>
        [/CASE]
        [CASE password]
        [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label for="Form_[!P::name!]" class="col-sm-6 control-label">[!P::description!]</label>
<div class="col-sm-6 form-value">
    **********
    //                    <input type="password" class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]" placeholder="" value="[!DF!]">
</div>
</div>
        [/CASE]
        [CASE raw]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label for="Form_[!P::name!]" class="control-label">[!P::description!]</label>
<div>
    <pre class="prettyprint">[IF [!Form_[!P::name!]!]][UTIL SPECIALCHARS][!Form_[!P::name!]!][/UTIL][ELSE][UTIL SPECIALCHARS][!P::value!][/UTIL][/IF]</pre>
</div>
</div>
        [/CASE]
        [CASE html]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label for="Form_[!P::name!]" class="control-label">[!P::description!]</label>
<a href="#nogo" class="btn btn-primary pull-right" id="showHtml[!P::name!]">Voir le contenu html</a>
<div style="display: none;" id="html[!P::name!]">
    [IF [**Form_[!P::name!]**]][**Form_[!P::name!]**][ELSE][**P::value**][/IF]
</div>
<script>
    var $t = [];
    $t['[!P::name!]'] = false;
    $('#showHtml[!P::name!]').click(function () {
        if (!$t['[!P::name!]']) {
            $('#html[!P::name!]').css('display', 'initial');
            $('#showHtml[!P::name!]').html('Cacher le contenu html');
            $t['[!P::name!]']=true;
        }else{
            $('#html[!P::name!]').css('display', 'none');
            $('#showHtml[!P::name!]').html('Voir le contenu html');
            $t['[!P::name!]']=false;
        }
    });
</script>
</div>
        [/CASE]
        [DEFAULT]
        [IF [!Form_[!P::name!]!]][!DF:=[**Form_[!P::name!]!]**][ELSE][!DF:=[**P::value**]!][/IF]
        [IF [!P::Values!]]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label class="col-sm-6 control-label">[!P::description!]</label>
<div class="col-sm-6 form-value">
    [!DF!]
    //<select class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]">
    //    [STORPROC [!P::Values!]|C]
    //    <option value="[!C!]" [IF [!DF!]=[!C!]]selected="selected"[/IF]>[!C!]</option>
    //    [/STORPROC]
    //</select>
</div>
        </div>
        [ELSE]
<div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
<label for="Form_[!P::name!]" class="col-sm-6 control-label">[!P::description!]</label>
<div class="col-sm-6 form-value">
    [IF [!Form_[!P::name!]!]][**Form_[!P::name!]**][ELSE][**P::value**][/IF]
    //<input type="text" class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]" placeholder="" value="[!DF!]">
</div>
</div>
        [/IF]
        [/DEFAULT]
        [/SWITCH]