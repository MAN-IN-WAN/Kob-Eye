window.addEvent('domready', function(){
	$$('a').each(function (el){
		if (el.rel == "link") {
			var lien = el.href;
			el.addEvent('click',function(e){
				// capture de l�v�nement
				var myEvent = new Event(e);
				// on le eutralise
				myEvent.stop();
				window.open(lien,"");
			});
		}
	});
});
