<!-- block seach mobile -->
<div class="block-search-top nav-item">
	<div class="icon-search">
		Rechercher
	</div>
	<!-- Block search module TOP -->
	<div id="search_block_top" class="item-top">

		<form method="get" action="/Search" id="searchbox" class="form-search">
			<div class="input-append">
				<label for="search_query_top"><!-- image on background --></label>
				<input type="hidden" name="controller" value="search" />
				<input type="hidden" name="orderby" value="position" />
				<input type="hidden" name="orderway" value="desc" />
				<input class="search_query span2 search-query" type="text" id="search_query_top" name="search" value="" />
				<input type="submit" name="submit_search" value="Rechercher	" class="button btn" style="margin-top:0;"/>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		// <![CDATA[
		$('document').ready(function() {
			$("#search_query_top").autocomplete('/Search/Json.json', {
				minChars : 2,
				max : 10,
				width : 300,
				selectFirst : false,
				scroll : false,
				dataType : "json",
				formatItem : function(data, i, max, value, term) {
					return value;
				},
				parse : function(data) {
					var mytab = new Array();
					for (var i = 0; i < data.length; i++)
						mytab[mytab.length] = {
							data : data[i],
							value : '<a href="'+data[i].value+'"><img src="'+data[i].image+'" />'  + data[i].label+'</a>'
						};
					return mytab;
				},
				extraParams : {
					ajaxSearch : 1,
					id_lang : 5
				}
			}).result(function(event, data, formatted) {
				//$('#search_query_top').val(data.pname);
				document.location.href = data.value;
			})
		});
		// ]]>
	</script>

</div>
<!-- /Block search module TOP -->
