/**
 * Method to delete chart
 * 
 * @param int chartId
 * @return bool
 */
function page_delete(pageId)
{
	if (!confirm(global_messages['pages']['delete_confirm'])) {
		return false;
	}
	
	// Send AJAX request
	$.ajax(
		{
			url: '/' + global_language_code + '/page/delete/' + pageId + '.html',
			dataType: "json",
			success: function(response) {
				if (response.status == 'error') {
					alert(response.message);
				} else {
					$('#page_' + pageId).fadeOut("slow", function(){
						$('#page_' + pageId).remove();
					});
				}
			},
			error: function() {
				alert(global_messages['pages']['delete_error']);
			}
		}
	);
}

/**
 * Method to delete chart item
 * 
 * @param int chartId
 * @return bool
 */
function chart_item_delete(itemId)
{
	if (!confirm(global_messages['chart_item']['delete_confirm'])) {
		return false;
	}
	
	// Send AJAX request
	$.ajax(
		{
			url: '/' + global_language_code + '/chart/item_delete/' + itemId + '.html',
			dataType: "json",
			success: function(response) {
				if (response.status == 'error') {
					alert(response.message);
				} else {
					$('#chart_item_' + itemId).fadeOut("slow", function(){
						$('#chart_item_' + itemId).remove();
					});
				}
			},
			error: function() {
				alert(global_messages['chart_item']['delete_error']);
			}
		}
	);
}

/**
 * Method to delete chart
 * 
 * @param int chartId
 * @return bool
 */
function chart_delete(chartId)
{
	if (!confirm(global_messages['chart']['delete_confirm'])) {
		return false;
	}
	
	// Send AJAX request
	$.ajax(
		{
			url: '/' + global_language_code + '/chart/delete/' + chartId + '.html',
			dataType: "json",
			success: function(response) {
				if (response.status == 'error') {
					alert(response.message);
				} else {
					$('#chart_' + chartId).fadeOut("slow", function(){
						$('#chart_' + chartId).remove();
					});
				}
			},
			error: function() {
				alert(global_messages['chart']['delete_error']);
			}
		}
	);
}

/**
 * Method to delete admin
 * 
 * @param int adminId
 * @return bool
 */
function admin_delete(adminId)
{
	if (!confirm(global_messages['admin']['delete_confirm'])) {
		return false;
	}
	
	// Send AJAX request
	$.ajax(
		{
			url: '/' + global_language_code + '/admin/delete/' + adminId + '.html',
			dataType: "json",
			success: function(response) {
				if (response.status == 'error') {
					alert(response.message);
				} else {
					$('#admin_' + adminId).fadeOut("slow", function(){
						$('#admin_' + adminId).remove();
					});
				}
			},
			error: function() {
				alert(global_messages['admin']['delete_error']);
			}
		}
	);
}

/**
 * Method to delete user
 * 
 * @param int userId
 * @return bool
 */
function user_delete(userId)
{
	if (!confirm(global_messages['user']['delete_confirm'])) {
		return false;
	}
	
	// Send AJAX request
	$.ajax(
		{
			url: '/' + global_language_code + '/user/delete/' + userId + '.html',
			dataType: "json",
			success: function(response) {
				if (response.status == 'error') {
					alert(response.message);
				} else if (response.status == 'deleted') {
					$('#user_' + userId).fadeOut("slow", function(){
						$('#user_' + userId).remove();
					});
				} else if (response.status == 'disabled') {
					$('#user_status_' + userId).html(global_messages['user']['status_no']);
					alert(global_messages['user']['disabled_message']);
				}
			},
			error: function() {
				alert(global_messages['user']['delete_error']);
			}
		}
	);
}


$(document).ready(function() {

	if ($('#email').length=0) {
		$('#email').val("");
	}

	$('#email').change(function() {

		if ( $( this ).val() <=2) {
			$('#SearchTransactionForm_user_id').val("");
			$('#SearchUserForm_id').val("");

			$('#email').val("");

		}
	});



});
