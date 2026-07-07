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

		initCookieConsent();
	});

	function initCookieConsent() {
		var widget = document.querySelector('.lnet-cookie-widget');
		var settingsButton = document.querySelector('.lnet-cookie-settings-button');

		if (!widget) {
			return;
		}

		var panel = widget.querySelector('.lnet-cookie-panel');
		var preferences = widget.querySelector('.lnet-cookie-preferences');
		var saveButton = widget.querySelector('[data-lnet-cookie-action="save"]');
		var choiceInputs = widget.querySelectorAll('[data-lnet-cookie-choice]');
		var version = widget.getAttribute('data-consent-version') || '1';
		var key = 'limbenet_cookie_consent';
		var existing = readConsent(key);

		window.limbeNetCookieConsent = {
			get: function () {
				return readConsent(key);
			},
			hasConsent: function (category) {
				var consent = readConsent(key);
				return !!(consent && consent[category]);
			},
			open: function () {
				openPanel(true);
			}
		};

		if (existing && existing.version === version) {
			applyConsent(existing);
			widget.hidden = true;
			if (settingsButton) {
				settingsButton.hidden = false;
			}
		} else {
			openPanel(false);
		}

		document.addEventListener('click', function (event) {
			var actionButton = event.target.closest('[data-lnet-cookie-action]');
			if (!actionButton) {
				return;
			}

			var action = actionButton.getAttribute('data-lnet-cookie-action');
			if ('accept' === action) {
				saveConsent({analytics: true, marketing: true});
			}

			if ('reject' === action) {
				saveConsent({analytics: false, marketing: false});
			}

			if ('manage' === action || 'open-settings' === action) {
				openPanel(true);
			}

			if ('save' === action) {
				saveConsent({
					analytics: getChoice('analytics'),
					marketing: getChoice('marketing')
				});
			}
		});

		function openPanel(showPreferences) {
			var consent = readConsent(key);

			widget.hidden = false;
			if (settingsButton) {
				settingsButton.hidden = true;
			}

			if (preferences) {
				preferences.hidden = !showPreferences;
			}

			if (saveButton) {
				saveButton.hidden = !showPreferences;
			}

			choiceInputs.forEach(function (input) {
				var category = input.getAttribute('data-lnet-cookie-choice');
				input.checked = !!(consent && consent[category]);
			});

			if (showPreferences && panel) {
				panel.focus({preventScroll: true});
			}
		}

		function getChoice(category) {
			var input = widget.querySelector('[data-lnet-cookie-choice="' + category + '"]');
			return !!(input && input.checked);
		}

		function saveConsent(choices) {
			var consent = {
				version: version,
				necessary: true,
				analytics: !!choices.analytics,
				marketing: !!choices.marketing,
				timestamp: new Date().toISOString()
			};

			try {
				window.localStorage.setItem(key, JSON.stringify(consent));
			} catch (error) {
				// The consent cookie below still records the choice if storage is unavailable.
			}
			writeConsentCookie(key, consent);
			applyConsent(consent);
			widget.hidden = true;

			if (settingsButton) {
				settingsButton.hidden = false;
			}
		}
	}

	function readConsent(key) {
		var stored = '';

		try {
			stored = window.localStorage.getItem(key) || '';
		} catch (error) {
			stored = '';
		}

		try {
			return JSON.parse(stored || readCookie(key) || 'null');
		} catch (error) {
			return null;
		}
	}

	function writeConsentCookie(key, consent) {
		var maxAge = 60 * 60 * 24 * 180;
		var secure = 'https:' === window.location.protocol ? '; Secure' : '';
		document.cookie = key + '=' + encodeURIComponent(JSON.stringify(consent)) + '; Max-Age=' + maxAge + '; Path=/; SameSite=Lax' + secure;
	}

	function readCookie(key) {
		var match = document.cookie.match(new RegExp('(?:^|; )' + key + '=([^;]*)'));
		return match ? decodeURIComponent(match[1]) : '';
	}

	function applyConsent(consent) {
		document.documentElement.setAttribute('data-lnet-analytics-consent', consent.analytics ? 'granted' : 'denied');
		document.documentElement.setAttribute('data-lnet-marketing-consent', consent.marketing ? 'granted' : 'denied');
		window.dispatchEvent(new CustomEvent('limbenetCookieConsentUpdated', {detail: consent}));
	}
}());
