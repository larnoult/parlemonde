/*! Digest Notifications - v1.2.1
 * https://github.com/wearerequired/digest
 * Copyright (c) 2015; * Licensed GPLv2+ */
(function () {
	var frequency_period = document.getElementById('digest_frequency_period'),
			frequency_day_wrapper = document.getElementById('digest_frequency_day_wrapper');

	function hideAndSeek() {
		if ('weekly' === ( this.value || frequency_period.value )) {
			frequency_day_wrapper.className = '';
		} else {
			frequency_day_wrapper.className = 'digest-hidden';
		}
	}

	frequency_period.onchange = hideAndSeek;

	hideAndSeek();
})();
