function addToFavourite(id_product) {
	$.ajax({
		url: favorite_products_url_add + '&rand=' + new Date().getTime(),
		type: "POST",
		headers: { "cache-control": "no-cache" },
		data: {
			"id_product": id_product
		}
	});
}
$(document).ready(function(){
	$('#npsfavorite_block_extra_add').click(function(){
		$.ajax({
			url: favorite_products_url_add + '&rand=' + new Date().getTime(),
			type: "POST",
			headers: { "cache-control": "no-cache" },
			data: {
				"id_product": favorite_products_id_product
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#npsfavorite_block_extra_add').slideUp(function() {
			    		$('#npsfavorite_block_extra_added').slideDown("slow");
			    	});

				}
		 	}
		});
	});
	$('#npsfavorite_block_extra_remove').click(function(){
		$.ajax({
			url: favorite_products_url_remove + '&rand=' + new Date().getTime(),
			type: "POST",
			headers: { "cache-control": "no-cache" },
			data: {
				"id_product": favorite_products_id_product
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#npsfavorite_block_extra_remove').slideUp(function() {
			    		$('#npsfavorite_block_extra_removed').slideDown("slow");
			    	});

				}
		 	}
		});
	});
	$('#npsfavorite_block_extra_added').click(function(){
		$.ajax({
			url: favorite_products_url_remove + '&rand=' + new Date().getTime(),
			type: "POST",
			headers: { "cache-control": "no-cache" },
			data: {
				"id_product": favorite_products_id_product
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#npsfavorite_block_extra_added').slideUp(function() {
			    		$('#npsfavorite_block_extra_removed').slideDown("slow");
			    	});

				}
		 	}
		});
	});
	$('#npsfavorite_block_extra_removed').click(function(){
		$.ajax({
			url: favorite_products_url_add + '&rand=' + new Date().getTime(),
			type: "POST",
			headers: { "cache-control": "no-cache" },
			data: {
				"id_product": favorite_products_id_product
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#npsfavorite_block_extra_removed').slideUp(function() {
			    		$('#npsfavorite_block_extra_added').slideDown("slow");
			    	});

				}
		 	}
		});
	});

	$('[rel^=ajax_id_favoriteproduct_]').click(function()
	{
		var idFavoriteProduct =  $(this).attr('rel').replace('ajax_id_favoriteproduct_', '');
		var parent = $(this).parent().parent();

		$.ajax({
			url: favorite_products_url_remove,
			type: "POST",
			data: {
				'id_product': idFavoriteProduct,
				'ajax': true
			},
			success: function(result)
			{
				if (result == '0')
				{
					parent.fadeOut("normal", function()
					{
						parent.remove();
					});
				}
 		 	}
		});
	});
});