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