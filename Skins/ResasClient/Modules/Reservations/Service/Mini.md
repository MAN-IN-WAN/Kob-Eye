<div class="well" style="overflow:hidden">
    <div class="row">
        <div class="col-xs-7">
            <h4>[!S::Titre!] ( [!Utils::getPrice([!S::Tarif!])!] € )</h4>
        </div>
        <div class="col-xs-5">
            [IF [!Service::[!S::Id!]!]!=]
                [!VAL:=[!Service::[!S::Id!]!]!]
            [ELSE]
                [!VAL:=0!]
            [/IF]
            <a class="btn btn-danger pull-right" onclick="on[!S::Id!]Plus()"><span class="glyphicon glyphicon-plus"></span></a>
            <input type="text" class=" pull-right" style="width: 34px;height: 34px;text-align: center;" name="Service[[!S::Id!]]" id="Service-[!S::Id!]" value="[!VAL!]"/>
            <a class="btn btn-danger pull-right" onclick="on[!S::Id!]Moins()"><span class="glyphicon glyphicon-minus"></span></a>
            <script>
                function on[!S::Id!]Plus(){
                    if ($('#Service-[!S::Id!]').val()<10)
                        $('#Service-[!S::Id!]').val(parseInt($('#Service-[!S::Id!]').val())+1);
                }
                function on[!S::Id!]Moins(){
                    if ($('#Service-[!S::Id!]').val()>0)
                        $('#Service-[!S::Id!]').val(parseInt($('#Service-[!S::Id!]').val())-1);
                }
            </script>
        </div>
    </div>
</div>

