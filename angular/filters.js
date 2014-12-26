'use strict';

/* Filters */
angular.module('cap.filters', []).

//- shared
filter('mysqlDate', [
	function() {
		return function(dateString) {
			if (typeof dateString === "undefined" ) {
				return;
			}
			/* get current timezone offset for this browser
			var tmpDate = new Date()
			var h = '0' + tmpDate.getTimezoneOffset() / 60;
			h = h.substr(-2);
			var m = '0' + tmpDate.getTimezoneOffset() % 60;
			m = m.substr(-2);

			var operator = (tmpDate.getTimezoneOffset() > 0) ? '-' : '+';
			var offset = operator + h + ':' + m;
			*/
			var match = dateString.match(/^(\d\d\d\d)-(\d\d)-(\d\d)\s(\d\d):(\d\d):(\d\d)$/);
			var d = new Date(match[1], match[2]-1, match[3], match[4], match[5], match[6], 0);
			return d;
			/*
			d = d + offset;
			return new Date(d);
			*/
		};
	}
]);
