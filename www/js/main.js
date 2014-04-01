// Global variables
var selected_songs = new Array(), selected_albums = new Array();

// Set all handlers
$(document).ready(function() {
	
	// Check or uncheck one album
	$("#album-list input:checkbox").on("change", function (event) {
		var $tr = $(this).closest("tr");
	
		if($tr.find("input:checked").length > 0)
		{
			$tr.removeClass('check_box_not_selected').addClass('check_box_selected');
		}else {
			$tr.removeClass('check_box_selected').addClass('check_box_not_selected');
		}
		
		// Claculate total info for albums
		calculate_albums_total();
	});
	
	//Checkall in albums displayed on page
	$(".album_check_all").on("click", function(even) {
		$(".css-checkbox").each(function() {
			if ($(this).is(':checked')) {
				$(this).prop('checked', false);
			} else {
				$(this).prop('checked', true);
			}
		});
	
		$('#album-list tr.album').each(function() {
			// Find checkbox
			$checkbox = $(this).find("input[type='checkbox'][name^=album_id]");
			
			// Check is checkbox checked
			if ($checkbox.is(':checked')) {
				$(this).addClass('check_box_selected');
			}else {
				$(this).removeClass('check_box_selected');
			}
		});
		
		// Claculate total info for albums
		calculate_albums_total();
		return false;
	});
	
	// Check or uncheck one track
	$("#track-list input:checkbox").on("change", function (event) {
		var $tr = $(this).closest("tr");

		if($tr.find("input:checked").length > 0)
		{
			$tr.removeClass('check_box_not_selected').addClass('check_box_selected');
		}else {
			$tr.removeClass('check_box_selected').addClass('check_box_not_selected');
		}
		
		// Claculate total info for track
		calculate_songs_total();
	});

	// Checkall in tracklist
	$(".track_check_all").on("click", function(even) {
		$(".css-checkbox").each(function() {
			if ($(this).is(':checked')) {
				$(this).prop('checked', false);
			} else {
				$(this).prop('checked', true);
			}
		});

		$('#track-list tr.track').each(function() {
			// Find checkbox
			$checkbox = $(this).find("input[type='checkbox'][name^=song_id]");
			
			// Check is checkbox checked
			if ($checkbox.is(':checked')) {
				$(this).addClass('check_box_selected');
			}else {
				$(this).removeClass('check_box_selected');
			}
		});
		
		// Claculate total info for track
		calculate_songs_total();
		return false;
	});

	// Set buy songs handler
	$("#buy_tracks").on("click",  function(even) {
		// Call add function
		add_to_cart('songs');

		return false;
	});
	
	// Set buy albums handler
	$("#buy_albums").on("click",  function(even) {
		// Call add function
		add_to_cart('albums');

		return false;
	});
	
	// Set login action handler
	$("#login_button").on("click",  function(even) {
		// Display dialog
		$("#login_dialog").dialog({
			height: 350,
			minWidth: 400,
			modal: true,
			resizable: false
		});

		return false;
	});
	
	// Set top-up action handler
	$("#topup_button").on("click",  function(even) {
		// Display dialog
		$("#topup_dialog").dialog({
			height: 350,
			minWidth: 200,
			modal: true,
			resizable: false
		});

		return false;
	});
	
	// Set cart confirm handler
	$("#cart_confirm").on("click",  function(even) {
		// Call cart confirm function
		cart_confirm();

		return false;
	});
	
	// Set album remove handler
	$(".album_remove").on("click",  function(even) {
		// Call add function
		remove_from_cart('album', $(this).attr('albumId'));

		return false;
	});
	
	// Set song remove handler
	$(".song_remove").on("click",  function(even) {
		// Call add function
		remove_from_cart('song', $(this).attr('songId'));

		return false;
	});
});

/**
 * Function to remove album or song from cart
 * 
 * @param string type
 * @param int id
 * @returns bool
 */
function remove_from_cart(type, id)
{
	// Prepare data
	var data = {
		YII_CSRF_TOKEN: $("meta[name='YII_CSRF_TOKEN']").attr('content')
	};
	
	// Add to cart
	$.ajax({
		type: "POST",
		url: "/order/remove_from_cart/" + type + '.' + id + '.html',
		dataType: "json",
		data: data,
		success: function(result) {
			if (result.status == 'success') {
				switch (type) {
					case 'song':
						// Remove song
						$('#song_' + id).hide('slow', function(){ $('#song_' + id).remove(); });
					break;
					
					case 'album':
						// Remove album
						$('#album_' + id).hide('slow', function(){ $('#album_' + id).remove(); });
						
						// Remove songs caption
						$('#songs_album_caption_' + id).hide('slow', function(){ $('#songs_album_caption_' + id).remove(); });
						
						// Remove songs
						$('.songs_album_' + id).hide('slow', function(){ $('#.songs_album_' + id).remove(); });
					break;
				}
			}
		}
	});
	
	return true;
}

/**
 * Function to confirm cart
 * 
 * @return void
 */
function cart_confirm()
{
	if (!confirm(cart_confirm_message)) {
		return false;
	}
	
	// Prepare data
	var data = {
		YII_CSRF_TOKEN: $("meta[name='YII_CSRF_TOKEN']").attr('content')
	};
	
	// Confirm cart
	$.ajax({
		type: "POST",
		url: "/order/cart_confirm/",
		dataType: "json",
		data: data,
		success: function(result) {
			if (result.status == 'success') {
				// Cleare cart
				$('#cart_songs_number').html('0');
				$('#cart_container').html('<b>' + cart_is_empty + '</b>');
				
				alert(cart_confirmed_message);
			} else {
				alert(cart_error_confirm_message + ': ' + result.message);
			}
		}
	});
}

/**
 * Function to add albums or songs to cart
 * 
 * @param string type
 * @returns bool
 */
function add_to_cart(type)
{
	// Prepare data
	var data = {
		YII_CSRF_TOKEN: $("meta[name='YII_CSRF_TOKEN']").attr('content')
	};
	switch (type) {
		case 'songs':
			if (tools_empty(selected_songs)) {
				return false;
			}
			
			// Fill data
			data['song_ids'] = selected_songs;
		break;
		
		case 'albums':
			if (tools_empty(selected_albums)) {
				return false;
			}
			
			// Fill data
			data['album_ids'] = selected_albums;
		break;
	}
	
	// Add to cart
	$.ajax({
		type: "POST",
		url: "/order/add_to_cart/" + type + '.html',
		dataType: "json",
		data: data,
		success: function(result) {
			if (result.status == 'success') {
				// Display total numbers in cart
				$('#cart_songs_number').html(result.total_number);
				
				alert(cart_added_message.replace('#added_number#', result.added_number));
			} else {
				alert(cart_error_add_message);
			}
		}
	});
	
	return true;
}

/**
 * Function to claculate total info for albums
 * 
 * @return void
 */
function calculate_albums_total() {
	var $total_price = 0;
	var $total_time = 0;
	var $total_size = 0;
	
	// Cleare selected albums array
	selected_albums = new Array();

	// Process traks
	$('#album-list tr.album').each(function() {
		// Find checkbox
		$checkbox = $(this).find("input[type='checkbox'][name^=album_id]");
		
		// Check is checkbox checked
		if ($checkbox.is(':checked')) {
			$price = parseFloat($(this).find("input[name^=price]").val());
			$size = parseFloat($(this).find("input[name^=size]").val());
			$time = parseFloat($(this).find("input[name^=time]").val());
			
			// Store selected album id
			selected_albums[selected_albums.length] = $checkbox.val();

			// Calculate total values
			$total_price = $total_price + $price;
			$total_size = $total_size + $size;
			$total_time = $total_time + $time;
		}
	});

	// Display total info
	$('#total_price').html($total_price.toFixed(2));
	$('#total_size').html(($total_size / (1024 * 1024)).toFixed(2));
	$('#total_time').html(($total_time / 60).toFixed(2));
}

/**
 * Function to claculate total info for track
 * 
 * @return void
 */
function calculate_songs_total() {
	var $total_price=0;
	var $total_time=0;
	var $total_size=0;
	
	// Cleare selected tracks array
	selected_songs = new Array();

	// Process traks
	$('#track-list tr.track').each(function() {
		// Find checkbox
		$checkbox = $(this).find("input[type='checkbox'][name^=song_id]");
		
		// Check is checkbox checked
		if ($checkbox.is(':checked')) {
			$pr = $(this).find("input[name^=price]");
			$sz = $(this).find("input[name^=size]");
			$tm = $(this).find("input[name^=time]");

			selected_songs[selected_songs.length] = $checkbox.val();

			$total_price =$total_price + parseFloat($pr.val());
			$total_size =$total_size + parseFloat($sz.val());
			$total_time =$total_time + parseFloat($tm.val());
		}
	});

	// Display total info
	$('#total_price').html($total_price.toFixed(2));
	$('#total_size').html(($total_size/(1024*1024)).toFixed(2));
	$('#total_time').html(($total_time/60).toFixed(2));
}