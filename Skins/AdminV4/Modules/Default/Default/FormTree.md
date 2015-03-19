<!-- page header -->
[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::ObjectType!]|O]
<a href="/[!Systeme::CurrentMenu::Url!]/Fiche" class="btn btn-large btn-warning pull-right">Nouveau post</a>
<h1 id="page-header">[!Systeme::CurrentMenu::Titre!]</h1>

<div class="fluid-container">

	<!-- widget grid -->
	<section id="widget-grid" class="">

		<!-- row-fluid -->

		<div class="row-fluid">
			<article class="span12">
				<!-- new widget -->
				<div class="jarviswidget" id="widget-id-0">
					<header>
						<h2>[!Systeme::CurrentMenu::Titre!]</h2>
					</header>
					<!-- wrap div -->
					<div>

						<div class="jarviswidget-editbox">
							<div>
								<label>Title:</label>
								<input type="text" />
							</div>
							<div>
								<label>Styles:</label>
								<span data-widget-setstyle="purple" class="purple-btn"></span>
								<span data-widget-setstyle="navyblue" class="navyblue-btn"></span>
								<span data-widget-setstyle="green" class="green-btn"></span>
								<span data-widget-setstyle="yellow" class="yellow-btn"></span>
								<span data-widget-setstyle="orange" class="orange-btn"></span>
								<span data-widget-setstyle="pink" class="pink-btn"></span>
								<span data-widget-setstyle="red" class="red-btn"></span>
								<span data-widget-setstyle="darkgrey" class="darkgrey-btn"></span>
								<span data-widget-setstyle="black" class="black-btn"></span>
							</div>
						</div>

						<div class="inner-spacer">
							<!-- TREE JS -->
							<div id="jstree"><div>
						</div>
						<!-- end content-->
					</div>
					<!-- end wrap div -->
				</div>
				<!-- end widget -->
			</article>

		</div>

		<!-- end row-fluid -->
	</section>
	<!-- end widget grid -->
</div>

[HEADER CSS]Tools/Js/JsTree/themes/default/style.css[/HEADER]
<script src="/Tools/Js/JsTree/jstree.js"></script>
<script type="text/javascript">
	$(document).ready(function (){ $('#jstree').jstree({
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