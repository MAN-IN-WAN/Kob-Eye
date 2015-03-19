(function($)
{
	var twitterCount = 1;
	$.fn.lofTwitter = function(options)
	{
		var options = $.extend(
							{
								showMode: 'ticker', /*ticker, scroll*/
								travelocity:0.07,
								vertical: true,
								hoverPause:true,
								visible: 3,
								auto:500,
								speed:1000
							}, options);
		return this.each(function(){
			// Uncomment the line below to test your preloader
			var lofTwitter = $(this);
			options = $.extend(
							{
								lofTwitter: lofTwitter
							}, options);
			$( lofTwitter ).jTweetsAnywhere( options );
			twitterCount++;
		});
	}
	initSlider = function( options ){
			var lofTwitter = options.lofTwitter;
			if(options.showMode == "ticker"){
				var wrapperWidth = ( options.itemWidth + options.space ) * options.count;
				$( lofTwitter ).css({width:wrapperWidth});
				$( lofTwitter ).find("li.jta-tweet-list-item").css({ width:options.itemWidth, height:options.itemHeight });
				$( lofTwitter ).find("ul.jta-tweet-list").liScroll( options );
			}
			else if(options.showMode == "scroll"){
				$( lofTwitter ).jScrollPane( options );
			}
			else{
				$( lofTwitter ).jCarouselLite(options);
			}
	}
	populateTweetFeed = function(options)
		{
			// if a tweet feed is to be displayed, get the tweets and show them
			if (options.tweetDecorator && options._tweetFeedElement)
			{
				getPagedTweets(options, function(tweets, options)
				{
					if (options._tweetFeedConfig._clearBeforePopulate)
					{
						clearTweetFeed(options);
					}

					hideLoadingIndicator(options, function()
					{
						// process the tweets
						$.each(tweets, function(idx, tweet)
						{
							// decorate the tweet and give it to the tweet visualizer
							options.tweetVisualizer(
								options._tweetFeedElement,
								$(options.tweetDecorator(tweet, options)),
								'append',
								options
							);
						});

						if (options._tweetFeedConfig._noData && options.noDataDecorator && !options._tweetFeedConfig._noDataElement)
						{
							options._tweetFeedConfig._noDataElement = $(options.noDataDecorator(options));
							options._tweetFeedElement.append(options._tweetFeedConfig._noDataElement);
						}

						if (options._tweetFeedConfig._clearBeforePopulate)
						{
							options._tweetFeedElement.scrollTop(0);
						}

						addHovercards(options);
						initSlider(options);
					});
				});
			}
		};
})(jQuery);
