<!-- block seach mobile -->
<div class="block-search-top nav-item">
	<div class="icon-search">
		Rechercher
	</div>
	<!-- Block search module TOP -->
	<div id="search_block_top" class="item-top">

		<form method="get" action="/Skins/Paranature/index.php?controller=search" id="searchbox" class="form-search">
			<div class="input-append">
				<label for="search_query_top"><!-- image on background --></label>
				<input type="hidden" name="controller" value="search" />
				<input type="hidden" name="orderby" value="position" />
				<input type="hidden" name="orderway" value="desc" />
				<input class="search_query span2 search-query" type="text" id="search_query_top" name="search_query" value="" />
				<input type="submit" name="submit_search" value="Rechercher	" class="button btn" />
			</div>
		</form>
	</div>
	<script type="text/javascript">
		// <![CDATA[
		$('document').ready(function() {
			$("#search_query_top").autocomplete('http://demo4leotheme.com/prestashop/leo_beauty_store/index.php?controller=search', {
				minChars : 3,
				max : 10,
				width : 500,
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
							value : data[i].cname + ' > ' + data[i].pname
						};
					return mytab;
				},
				extraParams : {
					ajaxSearch : 1,
					id_lang : 5
				}
			}).result(function(event, data, formatted) {
				$('#search_query_top').val(data.pname);
				document.location.href = data.product_link;
			})
		});
		// ]]>
	</script>

</div>
<!-- /Block search module TOP -->
