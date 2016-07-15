jQuery(function($){
	var portfolio = $('#portfolio');
	portfolio.masonry({
		isAnimated: true,
		itemSelector:'.bloc:not(.hidden)',
		isFitWidth:true,
		columnWidth:160
	});

	$('h1 a').click(function(e){
		var cls = $(this).attr('href').replace('#','');
		portfolio.find('.bloc').removeClass('hidden'); 
		portfolio.find('.bloc:not(.'+cls+')').addClass('hidden');
		portfolio.masonry('reload'); 
		portfolio.find('.'+cls).show(500);
		portfolio.find('.bloc:not(.'+cls+')').hide(500);

		location.hash = cls;
		e.preventDefault(); 
	});

	var bloc = portfolio.find('.bloc:first'); 
	var cssi = {width:bloc.width(),height:bloc.height()};
	var cssf = null; 

	portfolio.find('a.thumb').click(function(e){
		var elem = $(this); 
		var cls = elem.attr('href').replace('#','');
		var fold = portfolio.find('.unfold').removeClass('unfold').css(cssi); 
		var unfold = elem.parent().addClass('unfold').css(cssf); 
		portfolio.masonry('reload'); 
		if(cssf == null){
			cssf = {
				width : unfold.width(),
				height: unfold.height()
			};
		}
		unfold.css(cssi).animate(cssf);
	})

	if(location.hash != ''){
		$('a[href="'+location.hash+'"]').trigger('click');
	}
	
	//animation de la recherche
	//-webkit-transition: width 0.3s;
	//-moz-transition: width 0.3s;
	//transition: width 0.3s;

})