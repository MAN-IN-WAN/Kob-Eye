[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::ObjectType!]|O]



<div id="grid"></div>
<div id="popup"></div>
<script>
    var wnd;
    var detailsTemplate;
    $(document).ready(function() {
        /** popup **/
        wnd = $("#popup")
            .kendoWindow({
                title: "Supprimer un élément",
                modal: true,
                visible: false,
                resizable: false,
                width: 450
            }).data("kendoWindow");

        detailsTemplate = kendo.template($("#deletePopup").html());        
        
        var productsDataSource = new kendo.data.DataSource({
            type: "odata",
            transport: {
                read: "/[!I::Module!]/[!I::ObjectType!]/getJsonDatatable.json",
                dataType: "json"
            },
            schema: {
                model: {
                    fields: {
                        Id: { type: "number" }
                        [STORPROC [!O::getElementsByAttribute(list,,1)!]|E]
                                 ,
                                [SWITCH [!E::type!]|=]
                                        [CASE int]
                                                 [IF [!E::listDescr!]][!E::listDescr!][ELSE][!E::name!][/IF]:{type : "number"}
                                        [/CASE]
                                        [CASE image]
                                                 [IF [!E::listDescr!]][!E::listDescr!][ELSE][!E::name!][/IF]:{type: "picture"}
                                        [/CASE]
                                        [CASE date]
                                                 [IF [!E::listDescr!]][!E::listDescr!][ELSE][!E::name!][/IF]:{type: "date"}
                                        [/CASE]
                                        [DEFAULT]
                                                 [IF [!E::listDescr!]][!E::listDescr!][ELSE][!E::name!][/IF]:{type: "string"}
                                        [/DEFAULT]
                                [/SWITCH]
                        [/STORPROC]
                    }
                }
            },
            pageSize: 20,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true
        });
        
        $("#grid").kendoGrid({
            dataSource: productsDataSource,
            /*height: 800,*/
            filterable: true,
            sortable: true,
            pageable: true,
            columns: [
                {
                    field: "Id",
                    type: "number",                                                     
                    filterable: false,
                    width:'10',
                    template: function(dataItem) {
                        return "<em>"+dataItem.Id+"</em>";
                    }
                }
                [STORPROC [!O::getElementsByAttribute(list,,1)!]|E]
                         ,
                        [SWITCH [!E::type!]|=]
                                [CASE price]
                                         {
                                                field: "[!E::name!]",
                                                title : "[IF [!E::listDescr!]][!E::listDescr!][ELSE][IF [!E::description!]][!E::description!][ELSE][!E::name!][/IF][/IF]",
                                                width: '[IF [!E::listWidth!]>0][!E::listWidth!][ELSE]70px[/IF]',
                                                format: '{0:c}',
                                                template: function(dataItem) {
                                                    return dataItem.[!E::name!]+' €';
                                                }
                                        }
                                [/CASE]
                                [CASE int]
                                         {
                                                field: "[!E::name!]",
                                                title : "[IF [!E::listDescr!]][!E::listDescr!][ELSE][IF [!E::description!]][!E::description!][ELSE][!E::name!][/IF][/IF]",
                                                width: '[IF [!E::listWidth!]>0][!E::listWidth!][ELSE]70px[/IF]',
                                                template: function(dataItem) {
                                                    if (parseInt(dataItem.[!E::name!])>0) {
                                                        return "<span class='badge badge-success'>"+parseInt(dataItem.[!E::name!])+"</span>";
                                                    }else 
                                                        return "<span class='badge badge-info'>0</span>";
                                                }
                                        }
                                [/CASE]
                                [CASE boolean]
                                         {
                                                field: "[!E::name!]",
                                                title : "[IF [!E::listDescr!]][!E::listDescr!][ELSE][IF [!E::description!]][!E::description!][ELSE][!E::name!][/IF][/IF]",
                                                width: '[IF [!E::listWidth!]>0][!E::listWidth!][ELSE]70px[/IF]',
                                                template: function(dataItem) {
                                                    return "<div class='badge "+((dataItem.[!E::name!])?"badge-success":"badge-danger")+"'><span class='glyphicon "+((dataItem.[!E::name!])?"glyphicon-ok":"glyphicon-remove")+"'></span></div>";
                                                }
                                        }
                                [/CASE]
                                [CASE image]
                                         {
                                                field: "[!E::name!]",
                                                title : "[IF [!E::listDescr!]][!E::listDescr!][ELSE][IF [!E::description!]][!E::description!][ELSE][!E::name!][/IF][/IF]",
                                                width: '[IF [!E::listWidth!]>0][!E::listWidth!][ELSE]100px[/IF]',
                                                template: "<img src='/#: [!E::name!] #.mini.100x50.jpg' />"
                                        }
                                [/CASE]
                                [CASE date]
                                         {
                                                field: "[!E::name!]",
                                                title : "[IF [!E::listDescr!]][!E::listDescr!][ELSE][IF [!E::description!]][!E::description!][ELSE][!E::name!][/IF][/IF]",
                                                width: '[IF [!E::listWidth!]>0][!E::listWidth!][ELSE]50px[/IF]',
                                                template: "<strong>#: [!E::name!] # </strong>"
                                        }
                                [/CASE]
                                [DEFAULT]
                                         {
                                                field: "[!E::name!]",
                                                title : "[IF [!E::listDescr!]][!E::listDescr!][ELSE][IF [!E::description!]][!E::description!][ELSE][!E::name!][/IF][/IF]",
                                                width: '[IF [!E::listWidth!]>0][!E::listWidth!][ELSE]100%[/IF]'
                                        }
                                [/DEFAULT]
                        [/SWITCH]
                [/STORPROC]
                ,{
                    /*command: [
                        {
                            name: "details",
                            click: function(e) {
                                // e.target is the DOM element representing the button
                                var tr = $(e.target).closest("tr"); // get the current table row (tr)
                                // get the data bound to the current table row
                                var data = this.dataItem(tr);
                                window.location.href = data.url;
                            },
                            className: 'btn btn-success'
                        }
                    ],*/
                    width: '80px',
                    template: '<a class="btn btn-success" href="#= url #">Détails</a>'
                }
                ,{
                    /*command: [
                        {
                            name: "delete",
                            click: function(e) {
                                // e.target is the DOM element representing the button
                                var tr = $(e.target).closest("tr"); // get the current table row (tr)
                                // get the data bound to the current table row
                                var data = this.dataItem(tr);
                                e.preventDefault();
                                //affichage popup
                                wnd.content(detailsTemplate(data));
                                wnd.center().open();
                            }
                        }
                    ],*/
                    width: '80px',
                    template: '<a class="btn btn-danger kob-delete" data-title="#= title #" data-url="#= url #" onclick="javascript:popupdelete(this)">Delete</a>'

                }
            ]
        });
        
    });
       /* delete buttons */
    function popupdelete (el) {
        var data = {
          title : $(el).attr('data-title')  ,
          url : $(el).attr('data-url')  
        };
        //affichage popup
        wnd.content(detailsTemplate(data));
        wnd.center().open();
    }
</script>

<!-- Template Delete popup -->
<script type="text/x-kendo-template" id="deletePopup">
    <div id="details-container">
        <h5>Suppression de l'élément #= title #. Êtes-vous sur de vouloir supprimer cet élément ?</h5>
        <a class="btn btn-danger">Supprimer</a>
        <a class="btn btn-info">Annuler</a>
    </div>
</script>
