/**
 * Function to refresh captcha image
 * 
 * @param string elementId
 * @param string key
 * @return void
 */
function tools_refresh_captcha(elementId, key)
{
	$('#' + elementId).attr('src', '/captcha/display/' + key + '_' + tools_rand(1, 1000000) + '.jpg');
}

/**
 * Function in_array
 * 
 * @param mixed needle
 * @param array haystack
 * @param bool strict
 * @return bool
 */
function tools_in_array(needle, haystack, strict)
{
	var found = false, key, strict = !!strict;
	
	for (key in haystack) {
		if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
			found = true;
			break;
		}
	}
	
	return found;
}


/**
 * Function to check is value empty
 * 
 * @param mixed mixed_var
 * @returns {Boolean}
 */
function tools_empty(mixed_var) {
	var undef, key, i, len;
	var emptyValues = [undef, null, false, 0, "", "0", "0.0", "0.00"];

	for (i = 0, len = emptyValues.length; i < len; i++) {
		if (mixed_var === emptyValues[i]) {
			return true;
		}
	}

	if (typeof mixed_var === "object") {
		for (key in mixed_var) {
			return false;
		}
		return true;
	}

	return false;
}

/**
 * Function to convert unix time to date
 * 
 * @param mixed time
 * @param string format
 * @return mixed
 */
function tools_unixtime_to_date(time, format)
{
	if (time == null || format == null) {
		return false;
	}
	// Get date
	var date = new Date(time*1000);
	
	// Preplace
	format = format.replace('h', date.getHours() < 10 ? '0' + date.getHours() : date.getHours());
	format = format.replace('m', date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes());
	format = format.replace('s', date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds());
	
	// Return result
	return format;
}

/**
 * Function to generate random integer value from range
 * 
 * @param int min
 * @param int max
 * @return int
 */
function tools_rand(min, max) {
	if(max) {
		return Math.floor(Math.random() * (max - min + 1)) + min;
	} else {
		return Math.floor(Math.random() * (min + 1));
	}
}

/**
 * Function to format seconds into human readable format
 * 
 * @param int seconds
 * @return string
 */
function tools_seconds_format(seconds)
{
	// Return for empty
	if (seconds == null) {
		return '00:00:00';
	}
	
	// Prepare sizes
	var sizes = new Array();
	sizes[0] = 3600;
	sizes[1] = 60;
	sizes[2] = 1;
	
	// Prepare default formated
	var formated = '';
	
	for (i=0; i<=2; i++) {
		if ((seconds / sizes[i]) > 1) {
			var value = parseInt(seconds / sizes[i]);
			formated += (value < 10) ? '0' + String(value) : String(value);
			seconds = seconds % sizes[i];
		} else {
			formated += '00';
		}
		
		if (i != 2) {
			formated += ':';
		}
	}
	
	return formated;
}
