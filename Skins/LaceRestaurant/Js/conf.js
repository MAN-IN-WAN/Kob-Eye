/***************************************************/
//SLIDER
$(document).ready(function() {
	$("body.index #slider1").slider({
		auto: 5000,
		height:0.5
	});
	$("body.index #slider2").slider({
		auto: 5000,
		height:0.397,
		fx:true,
		navipuce:true
	});
	$("body.photos #slider1").slider({
		auto: 5000,
		fx:true,
		height:0.5,
		fullscreen:true,
		nav:true
	});
	$("body.photos #slider2").slider({
		height:0.5,
		nav:true
	});
	$("body.restaurant .slider").slider({
		height:0.5,
		displayminimum:true,
		nav:true
	});
	$("body.bonsplansdetails .slider").slider({
		height:1,
		sizefix:150,
		displayminimum:true,
		nav:true
	});
	$("body.produit #slider").slider({
		height:0.8,
		nav:true
	});
	$("body .vidz .slider").slider({
		height:1,
		nav:true,
		sizefix:106
	});
});
