(function () {
	'use strict';

	var consentKey = 'limbenet_cookie_consent_v1';
	var consentCookieName = 'limbenet_cookie_consent';

	function writeConsentCookie(choice) {
		var expires = new Date();

		expires.setDate(expires.getDate() + 180);
		document.cookie = consentCookieName + '=' + encodeURIComponent(choice) + '; expires=' + expires.toUTCString() + '; path=/; SameSite=Lax';
	}

	function readConsentCookie() {
		var cookies = document.cookie ? document.cookie.split(';') : [];
		var index;

		for (index = 0; index < cookies.length; index++) {
			var pair = cookies[index].trim().split('=');

			if (pair[0] === consentCookieName) {
				return decodeURIComponent(pair.slice(1).join('='));
			}
		}

		return '';
	}

	function readConsent() {
		var cookieChoice;

		try {
			var stored = JSON.parse(window.localStorage.getItem(consentKey));

			if (stored && stored.choice) {
				return stored;
			}
		} catch (error) {
			// Fall back to the consent cookie when local storage is unavailable.
		}

		cookieChoice = readConsentCookie();

		if ('all' === cookieChoice) {
			return {
				necessary: true,
				analytics: true,
				preferences: true,
				marketing: true,
				choice: cookieChoice
			};
		}

		if (cookieChoice) {
			return {
				necessary: true,
				analytics: false,
				preferences: false,
				marketing: false,
				choice: cookieChoice
			};
		}

		return null;
	}

	function saveConsent(categories, choice) {
		var payload = {
			necessary: true,
			analytics: !!categories.analytics,
			preferences: !!categories.preferences,
			marketing: !!categories.marketing,
			choice: choice,
			updatedAt: new Date().toISOString()
		};

		try {
			window.localStorage.setItem(consentKey, JSON.stringify(payload));
		} catch (error) {
			// The browser may block storage, but the consent cookie can still keep the banner from looping.
		}

		writeConsentCookie(choice);
		if ('function' === typeof window.CustomEvent) {
			window.dispatchEvent(new CustomEvent('limbenet:cookie-consent', { detail: payload }));
		}
		return payload;
	}

	function initCookieConsent() {
		var root = document.querySelector('[data-lnet-cookie-consent]');

		if (!root) {
			return;
		}

		var banner = root.querySelector('[data-lnet-cookie-banner]');
		var modal = root.querySelector('[data-lnet-cookie-modal]');
		var backdrop = root.querySelector('[data-lnet-cookie-backdrop]');
		var categoryInputs = root.querySelectorAll('[data-lnet-cookie-category]');

		function setVisible(element, visible) {
			if (!element) {
				return;
			}

			element.hidden = !visible;
		}

		function setOptionalCategories(categories) {
			categoryInputs.forEach(function (input) {
				var category = input.getAttribute('data-lnet-cookie-category');

				if ('necessary' === category) {
					input.checked = true;
					return;
				}

				input.checked = !!categories[category];
			});
		}

		function getOptionalCategories() {
			var categories = {
				analytics: false,
				preferences: false,
				marketing: false
			};

			categoryInputs.forEach(function (input) {
				var category = input.getAttribute('data-lnet-cookie-category');

				if ('necessary' !== category && Object.prototype.hasOwnProperty.call(categories, category)) {
					categories[category] = input.checked;
				}
			});

			return categories;
		}

		function loadConsentEmbeds(categories) {
			var embeds = document.querySelectorAll('[data-lnet-cookie-embed]');

			embeds.forEach(function (embed) {
				var category = embed.getAttribute('data-lnet-cookie-embed') || 'marketing';
				var frame = embed.querySelector('iframe[data-src]');
				var placeholder = embed.querySelector('[data-lnet-cookie-placeholder]');

				if (!categories[category] || !frame) {
					return;
				}

				frame.setAttribute('src', frame.getAttribute('data-src'));
				frame.removeAttribute('data-src');
				frame.hidden = false;

				if (placeholder) {
					placeholder.hidden = true;
				}
			});
		}

		function openModal() {
			setVisible(root, true);
			setVisible(backdrop, true);
			setVisible(modal, true);
			if (modal) {
				modal.querySelector('[data-lnet-cookie-close]').focus();
			}
		}

		function closeModal() {
			setVisible(backdrop, false);
			setVisible(modal, false);
		}

		function closeWidget() {
			closeModal();
			setVisible(root, false);
		}

		function acceptAll() {
			var payload = saveConsent({ analytics: true, preferences: true, marketing: true }, 'all');

			loadConsentEmbeds(payload);
			closeWidget();
		}

		function declineOptional() {
			var payload = saveConsent({ analytics: false, preferences: false, marketing: false }, 'declined');

			loadConsentEmbeds(payload);
			closeWidget();
		}

		function acceptSelected() {
			var payload = saveConsent(getOptionalCategories(), 'custom');

			loadConsentEmbeds(payload);
			closeWidget();
		}

		var saved = readConsent();
		var hasSavedChoice = !!(saved && saved.choice);

		root.querySelectorAll('[data-lnet-cookie-allow-all]').forEach(function (button) {
			button.addEventListener('click', acceptAll);
		});

		root.querySelectorAll('[data-lnet-cookie-decline]').forEach(function (button) {
			button.addEventListener('click', declineOptional);
		});

		root.querySelectorAll('[data-lnet-cookie-manage]').forEach(function (button) {
			button.addEventListener('click', openModal);
		});

		root.querySelectorAll('[data-lnet-cookie-save]').forEach(function (button) {
			button.addEventListener('click', acceptSelected);
		});

		root.querySelectorAll('[data-lnet-cookie-close], [data-lnet-cookie-dismiss]').forEach(function (button) {
			button.addEventListener('click', function () {
				if (button.hasAttribute('data-lnet-cookie-dismiss')) {
					setVisible(banner, false);
				}
				closeModal();
			});
		});

		if (backdrop) {
			backdrop.addEventListener('click', closeModal);
		}

		document.addEventListener('click', function (event) {
			if (!event.target.closest) {
				return;
			}

			var trigger = event.target.closest('[data-lnet-cookie-open]');

			if (!trigger) {
				return;
			}

			event.preventDefault();
			openModal();
		});

		document.addEventListener('keydown', function (event) {
			if ('Escape' === event.key) {
				closeModal();
			}
		});

		if (hasSavedChoice) {
			setOptionalCategories(saved);
			loadConsentEmbeds(saved);
			setVisible(root, false);
			setVisible(banner, false);
			return;
		}

		setOptionalCategories({ analytics: false, preferences: false, marketing: false });
		setVisible(root, true);
		setVisible(banner, true);
	}

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
}());
