/* MEA Router: event GA4 mea_cta_click untuk tautan ber-data-mea-dest. Lewat gtag (Site Kit); tanpa gtag, ke dataLayer (GTM). Diam kalau keduanya tidak ada. */
(function () {
	function track(e) {
		var t = e.target, a = t && t.closest ? t.closest('a[data-mea-dest]') : null;
		if (!a) return;
		var p = {
			mea_dest: a.getAttribute('data-mea-dest'),
			mea_path: a.getAttribute('data-mea-path'),
			mea_pos: a.getAttribute('data-mea-pos'),
			link_url: a.href
		};
		try {
			if (typeof window.gtag === 'function') {
				window.gtag('event', 'mea_cta_click', p);
			} else if (window.dataLayer && window.dataLayer.push) {
				p.event = 'mea_cta_click';
				window.dataLayer.push(p);
			}
		} catch (err) {}
	}
	document.addEventListener('click', track);
	document.addEventListener('auxclick', function (e) { if (e.button === 1) track(e); });
})();
