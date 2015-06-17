<!-- Block currencies module -->
<script type="text/javascript">
	$(document).ready(function() {
		$("#setCurrency").mouseover(function() {
			$(this).addClass("countries_hover");
			$(".currencies_ul").addClass("currencies_ul_hover");
		});
		$("#setCurrency").mouseout(function() {
			$(this).removeClass("countries_hover");
			$(".currencies_ul").removeClass("currencies_ul_hover");
		});

		$('ul#first-currencies li:not(.selected)').css('opacity', 0.3);
		$('ul#first-currencies li:not(.selected)').hover(function() {
			$(this).css('opacity', 1);
		}, function() {
			$(this).css('opacity', 0.3);
		});
	}); 
</script>

<div id="currencies_block_top" class="nav-item">
	<form id="setCurrency" action="/Skins/Paranature/Js/index.php?" method="post">
		<div class="item-top">
			<input type="hidden" name="id_currency" id="id_currency" value=""/>
			<input type="hidden" name="SubmitCurrency" value="" />
			<span> $ </span>
		</div>
		<div class="nav-item-content hide">
			<ul id="first-currencies" class="currencies_ul">
				<li class="selected">
					<a href="javascript:setCurrency(1);" title="Dollar">Dollar $</a>
				</li>
			</ul>
		</div>
	</form>
</div>
<!-- /Block currencies module -->
