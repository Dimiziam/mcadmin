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
				} else {
					$('#user_' + userId).fadeOut("slow", function(){
						$('#user_' + userId).remove();
					});
				}
			},
			error: function() {
				alert(global_messages['user']['delete_error']);
			}
		}
	);
}