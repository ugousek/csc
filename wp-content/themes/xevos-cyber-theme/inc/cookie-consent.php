<?php

/**
 * Simple cookie consent banner.
 * Matches Figma design – bottom bar with "ZAMÍTNOUT VŠE" and "POVOLIT VŠE".
 *
 * Stores consent in cookie 'xevos_cookie_consent' for 365 days.
 * Fires GTM consent events when accepted.
 *
 * Can be replaced by Real Cookie Banner / Complianz later.
 *
 * @package Xevos\CyberTheme
 */

defined('ABSPATH') || exit;

add_action('wp_footer', 'xevos_cookie_consent_banner');

function xevos_cookie_consent_banner(): void
{
	// Don't show in admin or if already consented (check is done in JS).
	if (is_admin()) return;
?>
	<div class="xevos-cookie-banner" id="cookie-banner" style="display:none;">
		<div class="xevos-section__container">
			<div class="xevos-cookie-banner__text">
				<span>Používáme cookies pro zlepšení vašich zážitků.</span>
			</div>
			<div class="xevos-cookie-banner__actions">
				<button class="xevos-cookie-btn xevos-cookie-btn--reject" id="cookie-reject">ZAMÍTNOUT VŠE</button>
				<button class="xevos-cookie-btn xevos-cookie-btn--accept" id="cookie-accept">POVOLIT VŠE</button>
			</div>
		</div>
	</div>

	<script>
		(function() {
			var banner = document.getElementById('cookie-banner');
			if (!banner) return;

			// Check if consent already given.
			if (document.cookie.indexOf('xevos_cookie_consent=') !== -1) return;

			// Show banner.
			banner.style.display = '';

			function setCookieConsent(value) {
				var d = new Date();
				d.setTime(d.getTime() + 365 * 24 * 60 * 60 * 1000);
				document.cookie = 'xevos_cookie_consent=' + value + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
				banner.style.display = 'none';

				// Fire GTM consent event if dataLayer exists.
				if (window.dataLayer) {
					window.dataLayer.push({
						event: 'cookie_consent',
						consent_status: value
					});

					if (value === 'accepted') {
						window.dataLayer.push({
							event: 'consent_update',
							analytics_storage: 'granted',
							ad_storage: 'granted'
						});
					}
				}
			}

			document.getElementById('cookie-accept').addEventListener('click', function() {
				setCookieConsent('accepted');
			});
			document.getElementById('cookie-reject').addEventListener('click', function() {
				setCookieConsent('rejected');
			});
		})();
	</script>
<?php
}
