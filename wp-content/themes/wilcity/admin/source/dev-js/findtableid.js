;(function($){
	'use strict';

	function findTableID(){
		let $restaurantName = $('#restaurant-name');
		if ( !$restaurantName.length ){
			return false;
		}

		let aCaching = [], xhr = null;
		$restaurantName.autocomplete({
			length: 2,
			select: function (event, ui) {
				//Set Restaurant ID field when clicked
				$('#restaurant-id').val(ui.item.id);
			},
			source: ((request, response)=> {
				if ( xhr !== null && xhr.status !== 200 ){
					xhr.abort();
				}

				if ( typeof aCaching[request.term] !== 'undefined' ){
					response(aCaching[request.term]);
					return false;
				}

				xhr = $.ajax({
					type: 'POST',
					url: ajaxurl,
					minLength: 2,
					data: {
						action: 'wiloke_find_open_table_id',
						term: request.term
					},
					success: (result=>{
						if ( !result.success ){
							response([{
								label: result.data.msg,
								value: result.data.msg,
								id: ''
							}]);
						}else{
							let aParseResponse = [],
								oResponse = jQuery.parseJSON(result.data.data);
							if ( typeof result.data.data.restaurants ){
								if (typeof oResponse.restaurants !== 'undefined' && oResponse.restaurants.length > 0) {
									oResponse.restaurants.filter(function (restaurant) {
										aParseResponse.push({
											label  : restaurant.name,
											value  : restaurant.name,
											id     : restaurant.id,
											address: restaurant.address
										});
									});
								}

								aCaching[request.term] = aParseResponse;
								response(aParseResponse);
							}
						}
					})
				});
			})
		})
	}

	$(document).ready(function () {
		findTableID();
	});

})(jQuery);