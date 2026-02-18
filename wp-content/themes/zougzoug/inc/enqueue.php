<?php
/**
 * Enqueue scripts & styles
 */

add_action('wp_enqueue_scripts', function () {
	$uri = get_template_directory_uri();
	$ver = ZZ_VERSION;

	// --- CSS ---
	wp_enqueue_style('zz-main', $uri . '/assets/css/main.css', [], $ver);

	// CSS specifique par page
	if (is_front_page()) {
		// main.css suffit pour l'accueil
	} elseif (is_page_template('page-a-propos.php') || is_page('a-propos')) {
		wp_enqueue_style('zz-about', $uri . '/assets/css/about.css', ['zz-main'], $ver);
	} elseif (is_page_template('page-collaborations.php') || is_page('collaborations')) {
		wp_enqueue_style('zz-projets', $uri . '/assets/css/projets.css', ['zz-main'], $ver);
	} elseif (is_page_template('page-contact.php') || is_page('contact')) {
		wp_enqueue_style('zz-contact', $uri . '/assets/css/contact.css', ['zz-main'], $ver);
	} elseif (is_page_template('page-cours.php') || is_page('cours')) {
		wp_enqueue_style('zz-cours', $uri . '/assets/css/cours.css', ['zz-main'], $ver);
	} elseif (is_page_template('page-revendeurs.php') || is_page('revendeurs')) {
		wp_enqueue_style('zz-revendeurs', $uri . '/assets/css/revendeurs.css', ['zz-main'], $ver);
	} elseif (is_page_template('page-mentions-legales.php') || is_page('mentions-legales')) {
		wp_enqueue_style('zz-mentions', $uri . '/assets/css/mentions.css', ['zz-main'], $ver);
	} elseif (is_404()) {
		wp_enqueue_style('zz-404', $uri . '/assets/css/404.css', ['zz-main'], $ver);
	}

	// --- JS ---
	// Main JS (header scroll + burger) — global
	wp_enqueue_script('zz-main', $uri . '/assets/js/main.js', [], $ver, true);

	// Accueil : Swiper + GSAP + home.js
	if (is_front_page()) {
		wp_enqueue_style('zz-swiper', $uri . '/assets/js/vendor/swiper-bundle.min.css', [], '11');
		wp_enqueue_script('zz-swiper', $uri . '/assets/js/vendor/swiper-bundle.min.js', [], '11', true);
		wp_enqueue_script('zz-gsap', $uri . '/assets/js/vendor/gsap.min.js', [], '3', true);
		wp_enqueue_script('zz-scrolltrigger', $uri . '/assets/js/vendor/ScrollTrigger.min.js', ['zz-gsap'], '3', true);
		wp_enqueue_script('zz-home', $uri . '/assets/js/home.js', ['zz-swiper', 'zz-scrolltrigger', 'zz-main'], $ver, true);
	}

	// À propos : GSAP
	if (is_page('a-propos')) {
		wp_enqueue_script('zz-gsap', $uri . '/assets/js/vendor/gsap.min.js', [], '3', true);
		wp_enqueue_script('zz-scrolltrigger', $uri . '/assets/js/vendor/ScrollTrigger.min.js', ['zz-gsap'], '3', true);
		wp_enqueue_script('zz-about', $uri . '/assets/js/about.js', ['zz-scrolltrigger', 'zz-main'], $ver, true);
	}

	// Collaborations
	if (is_page('collaborations')) {
		wp_enqueue_script('zz-gsap', $uri . '/assets/js/vendor/gsap.min.js', [], '3', true);
		wp_enqueue_script('zz-scrolltrigger', $uri . '/assets/js/vendor/ScrollTrigger.min.js', ['zz-gsap'], '3', true);
		wp_enqueue_script('zz-projets', $uri . '/assets/js/projets.js', ['zz-scrolltrigger', 'zz-main'], $ver, true);
		wp_localize_script('zz-projets', 'zzProjets', zz_get_projets_data());
	}

	// Contact
	if (is_page('contact')) {
		wp_enqueue_script('zz-gsap', $uri . '/assets/js/vendor/gsap.min.js', [], '3', true);
		wp_enqueue_script('zz-scrolltrigger', $uri . '/assets/js/vendor/ScrollTrigger.min.js', ['zz-gsap'], '3', true);
		wp_enqueue_script('zz-contact', $uri . '/assets/js/contact.js', ['zz-scrolltrigger', 'zz-main'], $ver, true);
	}

	// Cours
	if (is_page('cours')) {
		wp_enqueue_script('zz-gsap', $uri . '/assets/js/vendor/gsap.min.js', [], '3', true);
		wp_enqueue_script('zz-scrolltrigger', $uri . '/assets/js/vendor/ScrollTrigger.min.js', ['zz-gsap'], '3', true);
		wp_enqueue_script('zz-cours', $uri . '/assets/js/cours.js', ['zz-scrolltrigger', 'zz-main'], $ver, true);
	}

	// Revendeurs
	if (is_page('revendeurs')) {
		wp_enqueue_script('zz-gsap', $uri . '/assets/js/vendor/gsap.min.js', [], '3', true);
		wp_enqueue_script('zz-scrolltrigger', $uri . '/assets/js/vendor/ScrollTrigger.min.js', ['zz-gsap'], '3', true);
		wp_enqueue_script('zz-revendeurs', $uri . '/assets/js/revendeurs.js', ['zz-scrolltrigger', 'zz-main'], $ver, true);
	}
});
