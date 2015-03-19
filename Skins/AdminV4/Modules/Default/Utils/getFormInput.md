[SWITCH [!El::type!]|=]
        [CASE bbcode]
                <div class="control-group" >
                <label>[!El::description!]</label>
                <textarea class="span12 ckeditorbbcode" rows="2" name="[!El::name!]">[!El::value!]</textarea>
                </div>
        [/CASE]
        [CASE html]
                <div class="control-group" >
                        <label>[!El::description!]</label>
                        <textarea class="span12 ckeditorfull" rows="2" name="[!El::name!]">[!El::value!]</textarea>
                </div>
        [/CASE]
        [DEFAULT]
                <div class="control-group [IF [!Error_[!El::name!]!]]error[/IF]">
                        <label class="control-label" for="[!El::name!]">[!El::description!]</label>
                        <div class="controls">
                                [SWITCH [!El::type!]|=]
                                        [CASE image]
                                        <span class="btn btn-success fileinput-button uploadfile-block">
                                                <i class="glyphicon glyphicon-plus"></i>
                                                <span>Select file...</span>
                                                <!-- The file input field used as target for the file upload widget -->
                                                <input class="kefileupload" type="file" id="[!Pref!][!P::ObjectType!]-[!El::name!]" name="files" data-url="/Systeme/FileUpload.json">
                                        </span>
                                        <br>
                                        <br>
                                        <!-- The global progress bar -->
                                        <div class="progress" id="[!Pref!][!P::ObjectType!]-[!El::name!]-progress" >
                                                <div class="bar"></div>
                                        </div>
                                        <input type="text" name="[!El::name!]" id="[!Pref!][!P::ObjectType!]-[!El::name!]-input" value="[!El::value!]" class="pull-right upload-text span12"/>
                                        <!-- The container for the uploaded files -->
                                        <div class="files" id="[!Pref!][!P::ObjectType!]-[!El::name!]-files">
                                                [IF [!El::value!]]
                                                        <img src="/[!El::value!].mini.250x120.jpg" />
                                                [/IF]
                                        </div>
                                        [/CASE]
                                        [CASE file]
                                        <span class="btn btn-success fileinput-button uploadfile-block">
                                                <i class="glyphicon glyphicon-plus"></i>
                                                <span>Select file...</span>
                                                <!-- The file input field used as target for the file upload widget -->
                                                <input class="kefileupload" type="file" id="[!Pref!][!P::ObjectType!]-[!El::name!]" name="files" data-url="/Systeme/FileUpload.json">
                                        </span>
                                        <br>
                                        <br>
                                        <!-- The global progress bar -->
                                        <div class="progress" id="[!Pref!][!P::ObjectType!]-[!El::name!]-progress" class="progress">
                                                <div class="bar"></div>
                                        </div>
                                        <input type="text" name="[!El::name!]" id="[!Pref!][!P::ObjectType!]-[!El::name!]-input" value="[!El::value!]" class="pull-right upload-text span12"/>
                                        [/CASE]
                                        [CASE boolean]
                                                <div class="make-switch switch" >
                                                        <input type="checkbox" value="1" name="[!El::name!]" [IF  [!El::value!]]checked="checked"[/IF]>
                                                </div>
                                        [/CASE]
                                        [CASE text]
                                                <textarea class="span12" rows="2" name="[!El::name!]">[!El::value!]</textarea>
                                        [/CASE]
                                        [CASE fkey]
                                                //Affichage des liaisons
                                                [OBJ [!El::objectModule!]|[!El::objectName!]|Pa]
                                                <div class="dataItem" data-src="/[!El::objectModule!]/[!El::objectName!]/[!P::ObjectType!]/[!P::Id!]/getJsonDatatable.json" data-module="[!El::objectModule!]" data-objectclass="[!El::objectName!]" data-interface="getJsonDatatable.json"  data-var="listdep_[!El::objectName!]" data-icon="[!Pa::getIcone()!]" data-title="[!Pa::getDescription()!]" data-form="/[!P::getUrl()!]/[!Pa::ObjectType!]" data-description="[!El::description!]" data-key="[!El::name!]"></div>
                                        [/CASE]
                                        [CASE date]
                                                <div class="input-append date" id="datepicker-js" data-date="[DATE d/m/Y][!P::[!El::name!]!][/DATE]" >
                                                        <input class="datepicker-input" size="16" type="text" name="[!El::name!]Date" value="[DATE d/m/Y][!P::[!El::name!]!][/DATE]" placeholder="Select a date" data-date-format="dd/mm/yyyy"/>
                                                        <span class="add-on"><i class="cus-calendar-2"></i></span>
                                                </div>
                                                <div class="space"></div>
                                                <div class="input-append bootstrap-timepicker-component">
                                                        <input    type="text" class="timepicker-input" name="[!El::name!]Time" value="[DATE H:i:s][!P::[!El::name!]!][/DATE]" title="[!P::[!El::name!]!]"/>
                                                        <span class="add-on"><i class="cus-clock"></i></span>
                                                </div>
                                        [/CASE]
                                        [DEFAULT]
                                                <input type="text" class="span12"  name="[!El::name!]" value="[!El::value!]" />
                                        [/DEFAULT]
                                [/SWITCH]
                        </div>
                </div>
        [/DEFAULT]
[/SWITCH]
