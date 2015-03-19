<!-- page header -->
[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::ObjectType!]|O]
//<a href="/[!Systeme::CurrentMenu::Url!]/Fiche" class="btn btn-large btn-warning pull-right">Nouveau post</a>
//<h1 id="page-header">[!Systeme::CurrentMenu::Titre!]</h1>

 <div class="row-fluid">
        <div class="span12">
            <ul class="breadcrumb">
                <li><a href="#">Home</a> <span class="divider">/</span></li>
                <li><a href="#">Library</a> <span class="divider">/</span></li>
                <li class="active">Data</li>
            </ul>
        </div>
 </div>
<div class="fluid-container">
    <div class="row-fluid">
        <div class="span3">
            <!--
            <div class="navbar">
                <div class="navbar-inner">
                    <a class="brand" href="#">Title</a>
                    <ul class="nav">
                        <li class="active"><a href="#">Home</a></li>
                        <li><a href="#">Link</a></li>
                        <li><a href="#">Link</a></li>
                    </ul>
                </div>
            </div>-->
            <!-- new widget -->
            <div class="jarviswidget" id="widget-id-200" style="margin:0">
                <header>
                        <h2>[!Systeme::CurrentMenu::Titre!]</h2>
                </header>
                <!-- wrap div -->
                <div>
                        <div class="inner-spacer">
                                <!-- TREE JS -->
                                <div id="jstree"><div>
                        </div>
                        <!-- end content-->
                </div>
                <!-- end wrap div -->
            </div>
        </div>
    </div>
</div>
        <div class="span9" id="event_result">
        </div>
    </div>
</div>

[HEADER CSS]Tools/Js/JsTree/themes/default/style.css[/HEADER]
<script src="/Tools/Js/JsTree/jstree.js"></script>
<script type="text/javascript">
	$(document).ready(function (){
            $('#jstree')
                .on('changed.jstree', function (e, data) {
                    var i, j, r = [];
                    for(i = 0, j = data.selected.length; i < j; i++) {
                      r.push(data.instance.get_node(data.selected[i]).id);
                    }
                    $('#event_result').html('Selected: ' + r.join(', '));
                    //ajax request to display the form
                    for(i = 0, j = data.selected.length; i < j; i++) {
                        $.ajax({
                            dataType: "html",
                            url: "/[!Systeme::CurrentMenu::Url!]/"+data.instance.get_node(data.selected[i]).id+"/FormOnly.htm"
                        }).done(function(data) {
                            $( '#event_result' ).html( data );
/*                            $("#event_result").find("script").each(function(i) {
                                eval($(this).text());
                            });*/
                         });
                    }
                }).jstree({
                    "core" : {
                        "animation" : 1,
                        "check_callback" : true,
                        "themes" : {
                            "name" : false,
                            "stripes" : true,
                            "dots" : false
                        },
                        'data' : {
                          'url' : function (node) {
                            console.log(node);
                            return parseInt(node.id)>0 ? '/[!I::Module!]/[!I::ObjectType!]/'+node.id+'/getJsonTree.json' : '/[!I::Module!]/[!I::ObjectType!]/getJsonTree.json';
                          }/*,
                          'data' : function (node) {
                            return { 'id' : node.aaData };
                          }*/
                        },
                            "plugins" : [
    //			  "contextmenu", "dnd", "search",
                              "state"//, "types"
    //			  , "wholerow"
                            ]
                      }
                });
	});
</script>