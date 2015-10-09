<div class="lof-block-wrap">
	<h2>Twitte Feed</h2>
	<ul class="lof-items">
		<li class="lof-module">
			<div class="twitter-ticker" id="twitter-ticker1" style="width:245px; height:195px; margin-top:10px; display: block;">
				<div class="top-bar">
					<div class="twitIcon"><img src="/Skins/Paranature//modules/loftwitter/tmpl/default/assets/images/twitter_64.png" width="64" height="64" alt="Twitter icon" />
					</div>
					<h2 class="tut">Lof Twitter</h2>
				</div>
				<div id="lof_twitterfooter73" class="tweet-container" style="width:300px;height:360px;"></div>
				<div id="scroll"></div>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('#lof_twitterfooter73').lofTwitter({
						username : 'leotheme',
						count : 2,
						itemWidth : 245,
						itemHeight : 195,
						space : 5,
						vertical : true,
						hoverPause : true,
						visible : 2, /*number visible items*/
						auto : 500,
						speed : 1000,
						showFollowButton : false,
						showMode : "scroll",
						showTweetFeed : {
							expandHovercards : true,
							showSource : true
						}
					})
				});
			</script>
		</li>
	</ul>
</div>
