$(document).ready( function(){

	$("#productsview a").click( function(){
		if( $(this).attr("rel") == "view-grid" ){
			$("#product_list").addClass("view-grid").removeClass("view-list");
			$(".icon-th").addClass("active");
			$(".icon-th-list").removeClass("active");
		} else {
			$("#product_list").addClass("view-list").removeClass("view-grid");
			$(".icon-th-list").addClass("active");
			$(".icon-th").removeClass("active");
		}
		return false;
	} );
} );


function LeoWishlistCart(id, action, id_product, id_product_attribute, quantity)
{ 
	$.ajax({
		type: 'GET',
		url:	baseDir + 'modules/blockwishlist/cart.php',
		async: true,
		cache: false,
		data: 'action=' + action + '&id_product=' + id_product + '&quantity=' + quantity + '&token=' + static_token + '&id_product_attribute=' + id_product_attribute,
		success: function(data)
		{ 
			if (action == 'add') {
				
				if( $("#wishlistwraning").length <= 0 ) {
				   var html = '';
				   html +=  '<div id="wishlistwraning"><div class="container">';
				   html +=  ' ';
				   html +=  '</div></div>';
				   $("body").append( html );	
				} 
				$("#wishlistwraning .container").html( ' <div class="alert-content"> <button type="button" class="close" data-dismiss="alert">&times;</button><div class="alert">' + data + '</div></div>' );		
				if( $("#wishlistwraning .cart_block_product_name").length > 0 ) {
					$("#wishlistwraning").hide().empty();
				}else {
					$("#wishlistwraning").show();
				}
				
   			}
		
			if($('#' + id).length != 0)
			{ 
				$('#' + id).slideUp('normal');
				document.getElementById(id).innerHTML = data;
				$('#' + id).slideDown('normal');
			}
		}
	});
}