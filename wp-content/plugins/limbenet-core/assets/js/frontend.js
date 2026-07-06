(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var forms = document.querySelectorAll('.lnet-search-form');

		forms.forEach(function (form) {
			form.addEventListener('submit', function () {
				var fields = form.querySelectorAll('input, select');

				fields.forEach(function (field) {
					if (!field.value) {
						field.disabled = true;
					}
				});

				window.setTimeout(function () {
					fields.forEach(function (field) {
						field.disabled = false;
					});
				}, 250);
			});
		});
	});
}());
