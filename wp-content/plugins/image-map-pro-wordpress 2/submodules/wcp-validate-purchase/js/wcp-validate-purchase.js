;(function ($, window, document, undefined) {
    $.wcpValidatePurchaseCode = function(code, cb) {
        // var username = 'nickys';
        // var apiKey = 'fyyd39viwu1ljcgt0z1lu5c1b8ebp1zt';
        // url: 'http://marketplace.envato.com/api/edge/' + username + '/'+ apiKey +'/verify-purchase:'+ code +'.json',

        var data = {
            action: 'wcp_validate_purchase_code',
            code: code
        };

        $.ajax({
			type: 'POST',
			url: ajaxurl,
			data: data,
		}).done(function(res) {
            if (res == 'success') {
                cb(true);
            } else {
                console.log(res);
                cb(false);
            }
		});
	}
})(jQuery, window, document);