/* Hero Komunitas MEA: sorot ruangan saat hover jalur + video lazy (desktop saja). */
(function () {
	var f = document.getElementById('hero-frame');
	if (!f) return;

	document.querySelectorAll('.path[data-f]').forEach(function (a) {
		var c = 'focus-' + a.getAttribute('data-f');
		['mouseenter', 'focus'].forEach(function (e) {
			a.addEventListener(e, function () { f.classList.add(c); });
		});
		['mouseleave', 'blur'].forEach(function (e) {
			a.addEventListener(e, function () { f.classList.remove(c); });
		});
	});

	var mm = window.matchMedia, conn = navigator.connection;
	if (!mm || mm('(prefers-reduced-motion: reduce)').matches || !mm('(min-width: 821px)').matches) return;
	if (conn && conn.saveData) return;

	function addVideo() {
		var v = document.createElement('video');
		v.muted = true;
		v.loop = true;
		v.playsInline = true;
		v.autoplay = true;
		v.setAttribute('muted', '');
		v.setAttribute('playsinline', '');
		v.setAttribute('aria-hidden', 'true');
		v.setAttribute('tabindex', '-1');
		[['webm', 'video/webm'], ['mp4', 'video/mp4']].forEach(function (s) {
			var src = f.getAttribute('data-' + s[0]);
			if (!src) return;
			var el = document.createElement('source');
			el.src = src;
			el.type = s[1];
			v.appendChild(el);
		});
		v.addEventListener('playing', function () { v.classList.add('on'); });
		f.insertBefore(v, f.querySelector('.spot'));
		var p = v.play && v.play();
		if (p && p.catch) p.catch(function () {});

		// Hemat CPU: jeda saat hero tidak terlihat.
		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (en) {
				var q = en[0].isIntersecting ? v.play() : v.pause();
				if (q && q.catch) q.catch(function () {});
			}).observe(f);
		}
	}

	function idle() {
		(window.requestIdleCallback || function (cb) { setTimeout(cb, 300); })(addVideo);
	}
	if (document.readyState === 'complete') idle();
	else window.addEventListener('load', idle);
})();
