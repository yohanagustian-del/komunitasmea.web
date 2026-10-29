/* MEA Router: event GA4 mea_cta_click untuk tautan ber-data-mea-dest. Diam kalau gtag tidak ada. */
(function () {
	function track(e) {
		var t = e.target, a = t && t.closest ? t.closest('a[data-mea-dest]') : null;
		if (!a || typeof window.gtag !== 'function') return;
		try {
			window.gtag('event', 'mea_cta_click', {
				mea_dest: a.getAttribute('data-mea-dest'),
				mea_path: a.getAttribute('data-mea-path'),
				mea_pos: a.getAttribute('data-mea-pos'),
				link_url: a.href
			});
		} catch (err) {}
	}
	document.addEventListener('click', track);
	document.addEventListener('auxclick', function (e) { if (e.button === 1) track(e); });
})();
