    <div class="property-filter no-print">
        <div class="content">
            <form method="get" action="/annonce">
                <div class="control-group">

                    [IF [!Type!]=]
                        [!Type:=Vente!]
                        [IF [!Sys::CurrentMenu::Titre!]=Location||[!Sys::CurrentMenu::Titre!]=Location saisonnière] [!Type:=[!Sys::CurrentMenu::Titre!]!] [/IF]
                    [/IF]

                    <div class="controls">
                        <label class="search-radio sidebar-search">
                            <input type="radio" name="Type" class="selectType" id="optionsRadios1" value="Location" [IF [!Type!]=Location]checked="checked"[/IF]>
                            Location
                        </label>
                        <label class="search-radio sidebar-search">
                            <input type="radio" name="Type" class="selectType" id="optionsRadios2" value="Vente" [IF [!Type!]=Vente]checked="checked"[/IF]>
                            Vente
                        </label>
                        <br>
                        <label class="search-radio sidebar-search">
                            <input type="radio" name="Type" class="selectType" id="optionsRadios3" value="Location saisonnière" [IF [!Type!]=Location saisonnière]checked="checked"[/IF]>
                            Location saisonnière
                        </label>
                    </div>
                </div>
                <div style="clear:left"></div>
                <div class="location control-group">
                    <div class="controls">
                       <input type="text" name="Reference" id="Reference" placeholder="Référence du bien" value="[!Reference!]" />
                    </div><!-- /.controls -->
                </div>
               <div class="location control-group">
                    <div class="controls">
                        <select name="Ville" id="Ville">
                            <option value="">Ville</option>
                            [STORPROC ParcImmobilier/Ville|ville|||Ordre|DESC]
                                <option value="[!ville::Id!]" [IF [!Ville!]=[!ville::Id!]]selected="selected"[/IF]>[!ville::Nom!]</option>
                            [/STORPROC]
                        </select>
                    </div><!-- /.controls -->
                </div><!-- /.control-group -->

                <div class="type control-group">
                    <div class="controls">
                        <select name="TypeBien" id="TypeBien">
                            <option value="">Type de bien</option>
                            [OBJ ParcImmobilier|Residence|residence]
                            [!options:=[!residence::getProperty(TypeBien)!]!]
                            [STORPROC [!options::Values!]|option]<option value="[!option!]" [IF [!TypeBien!]=[!option!]]selected="selected"[/IF]>[!option!]</option>[/STORPROC]
                        </select>
                    </div><!-- /.controls -->
                </div><!-- /.control-group -->
                <div class="control-group" id="date-location-saison" [IF [!Type!]=Location saisonnière][ELSE]style="display:none;"[/IF]>
                    <label class="control-label" for="datedepart">
                        Période de location
                    </label>
                    <div class="controls">
                        <div class="input-daterange" id="datedepart">
                            <div class="beds control-group">
                                <div class="controls">
                                    <input type="text" name="startAt" id="startAt" placeholder="Du" class="dateHolidayDepart" value="[IF [!Type!]=Location saisonnière][!startAt!][/IF]">
                                </div><!-- /.controls -->
                            </div>
                            <div class="beds control-group">
                                <div class="controls">
                                    <input type="text" name="endAt" id="endAt" class="dateHolidayFin" placeholder="Au" value="[IF [!Type!]=Location saisonnière][!endAt!][/IF]">
                                </div><!-- /.controls -->
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    var datesDisabled = [];
                    var endDate = new Date();
                    endDate.setFullYear(endDate.getFullYear());
                    endDate.setMonth(11);
                    endDate.setDate(31);

                    $('.input-daterange').datepicker({
                        language    : 'fr',
                        startDate   : 'today',
                        datesDisabled: datesDisabled,
                        endDate     : endDate,
                        weekStart   : 6,
                        autoclose   : false,
                        daysOfWeekDisabled : [0,1,2,3,4,5]
                    }).on('changeDate', function (e) {
                        $("#startAt").css('background-color','#fff');
                        $("#endAt").css('background-color','#fff');
                    });
                    $('.selectType').on('click',function () {
                        console.log('test value',$('.selectType:checked').val());
                        switch ($('.selectType:checked').val()){
                            case "Location saisonnière":
                                $('#date-location-saison').css('display','initial');
                            break;
                            default:
                                $('#date-location-saison').css('display','none');
                                $('.dateHolidayFin').val('');
                            break;
                        }
                    });

                </script>



    <div class="beds control-group">
                    <label class="control-label" for="Chambres">
                        Chambres
                    </label>
                    <div class="controls">
                        <select id="Chambres" name="Chambres">
                            <option value="">-</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">+ de  5</option>
                        </select>
                    </div><!-- /.controls -->
                </div><!-- /.control-group -->

                <div class="baths control-group">
                    <label class="control-label" for="SuperficieCarrez">
                        Surface
                    </label>
                    <div class="controls">
                        <select id="SuperficieCarrez" name="SuperficieCarrez">
                            <option value="">-</option>
                            <option value="30"> <  30m²</option>
                            <option value="50"> 30 à 50 m²</option>
                            <option value="75"> 50 à 75 m²</option>
                            <option value="100"> 75 à 100m²</option>
                            <option value="150"> 100 à 150 m²</option>
                            <option value="200"> 150 à 200 m²</option>
                            <option value="300"> + de 200 m²</option>
                        </select>
                    </div><!-- /.controls -->
                </div><!-- /.control-group -->

                <div class="price-from control-group">
                    <label class="control-label" for="prixMin">
                        Price from
                    </label>
                    <div class="controls">
                        <input type="text" id="prixMin" name="prixMin" value="100">
                    </div><!-- /.controls -->
                </div><!-- /.control-group -->

                <div class="price-to control-group">
                    <label class="control-label" for="prixMax">
                        Price to
                    </label>
                    <div class="controls">
                        <input type="text" id="prixMax" name="prixMax"  value="1000000">
                    </div><!-- /.controls -->
                </div><!-- /.control-group -->

                <div class="price-value">
                    <span class="from"></span><!-- /.from -->
                    -
                    <span class="to"></span><!-- /.to -->
                </div><!-- /.price-value -->

                <div class="price-slider">
                </div><!-- /.price-slider -->

                <div class="form-actions">
                    <input type="submit" value="Rechercher !" class="btn btn-primary btn-large">
                </div><!-- /.form-actions -->
            </form>
        </div><!-- /.content -->
    </div><!-- /.property-filter -->
