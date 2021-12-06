$(function() {
	new MiniBar('.main-sidebar', {
		barType: "default",
		minBarSize: 10,
		hideBars: true,
		alwaysShowBars: true,
	});

	$('#compose-email-global').on('click', function (e) {
	    e.preventDefault();
	    alert('Later will be able to send emails.');
    });
});

/**
 * Posts Specified url with given fields,
 * on response calls callback
 * @param url
 * @param fields
 * @param callback
 */
function runBulkAction(url, fields, callback) {
	axios.post(url, fields).then(function (response) {
		if (typeof callback === 'undefined') {
			return;
		}

		callback({
			success: true,
			message: response.data
		});
	}).catch(function (response) {
		if (typeof callback === 'undefined') {
			return;
		}

		callback({
			success: false,
			message: response.data
		});
	});
}

function getCheckboxValues() {
	let ids = [];

	$('.kv-row-checkbox:checked').each(function () {
		ids.push($(this).attr('value'));
	});

	return ids;
}

/**
 * Verify If Valid Magnet Link
 * @param link
 * @return boolean
 */
function isValidaMagnetLink(link) {
	return /^magnet:\?xt=urn:btih:[a-zA-Z0-9]+/gm.test(link);
}

/**
 * Parses url and get's parameter by name
 * @param name
 */
function extractParameterFromUrlByName(name) {
	let queryInfo = decodeURIComponent(window.location.search).split('?');
	console.log(queryInfo);
	if (queryInfo.length < 2) {
		return null;
	}
	queryInfo = queryInfo['1'].split('&');

	for (let i = 0; i<queryInfo.length; i++) {
		let splited = queryInfo[i].split('=');

		if (splited['0'] === name) {
			return splited['1'];
		}
	}

	return null;
}

function stripScriptTags(rawHtml) {
	return rawHtml.replace(/<script\s+src=".+"><\/script>/gm, '');
}
