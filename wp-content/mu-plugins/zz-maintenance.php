<?php
/**
 * Plugin Name: ZZ Maintenance Mode
 * Description: Mode maintenance / coming soon pour Atelier ZougZoug
 * Author: Atelier ZougZoug
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

/* =========================================================================
   A. HELPERS
   ========================================================================= */

function zz_maintenance_is_active() {
	return get_option('zz_maintenance_mode', '0') === '1';
}

/* =========================================================================
   B. PAGE ADMIN — Reglages > Maintenance
   ========================================================================= */

add_action('admin_menu', function () {
	add_options_page(
		'Mode Maintenance',
		'Maintenance',
		'manage_options',
		'zz-maintenance',
		'zz_maintenance_admin_page'
	);
});

add_action('admin_init', function () {
	register_setting('zz_maintenance', 'zz_maintenance_mode', [
		'type'              => 'string',
		'sanitize_callback' => function ($val) { return $val === '1' ? '1' : '0'; },
		'default'           => '0',
	]);
});

function zz_maintenance_admin_page() {
	$active = zz_maintenance_is_active();
	?>
	<div class="wrap">
		<h1>Mode Maintenance</h1>
		<form method="post" action="options.php">
			<?php settings_fields('zz_maintenance'); ?>
			<table class="form-table">
				<tr>
					<th scope="row">Activer le mode maintenance</th>
					<td>
						<label>
							<input type="hidden" name="zz_maintenance_mode" value="0">
							<input type="checkbox" name="zz_maintenance_mode" value="1" <?php checked($active); ?>>
							Bloquer l'acc&egrave;s au site pour les visiteurs
						</label>
						<p class="description">
							Les administrateurs connect&eacute;s peuvent toujours naviguer normalement.<br>
							L'indexation (robots.txt, sitemap) est d&eacute;sactiv&eacute;e tant que le mode est actif.
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button('Enregistrer'); ?>
		</form>
		<?php if ($active) : ?>
		<div style="margin-top:20px;padding:12px 16px;background:#fff3cd;border-left:4px solid #ffc107;border-radius:3px;">
			<strong>Le mode maintenance est actif.</strong> Les visiteurs voient la page "Bient&ocirc;t de retour".
		</div>
		<?php endif; ?>
	</div>
	<?php
}

/* =========================================================================
   C. INDICATEUR BARRE ADMIN
   ========================================================================= */

add_action('admin_bar_menu', function ($wp_admin_bar) {
	if (!zz_maintenance_is_active()) return;
	if (!current_user_can('manage_options')) return;

	$wp_admin_bar->add_node([
		'id'    => 'zz-maintenance-indicator',
		'title' => '<span class="zz-maint-badge">Maintenance</span>',
		'href'  => admin_url('options-general.php?page=zz-maintenance'),
		'meta'  => ['class' => 'zz-maintenance-bar-item'],
	]);
}, 100);

// CSS de la pastille (admin + front)
add_action('admin_head', 'zz_maintenance_badge_css');
add_action('wp_head', 'zz_maintenance_badge_css');
function zz_maintenance_badge_css() {
	if (!zz_maintenance_is_active()) return;
	if (!is_user_logged_in()) return;
	?>
	<style>
	.zz-maint-badge {
		background: #dc3232;
		color: #fff !important;
		padding: 2px 10px;
		border-radius: 3px;
		font-size: 12px;
		font-weight: 600;
		letter-spacing: 0.03em;
	}
	#wpadminbar .zz-maintenance-bar-item .ab-item:hover .zz-maint-badge {
		background: #c92c2c;
	}
	</style>
	<?php
}

/* =========================================================================
   D. BLOCAGE SEO — robots.txt, sitemap, meta noindex
   ========================================================================= */

// robots.txt → Disallow all
add_filter('robots_txt', function ($output) {
	if (!zz_maintenance_is_active()) return $output;
	return "User-agent: *\nDisallow: /\n";
}, 999);

// Sitemap → 503
add_action('init', function () {
	if (!zz_maintenance_is_active()) return;

	$path = wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
	if ($path && rtrim($path, '/') === '/sitemap.xml') {
		status_header(503);
		header('Retry-After: 3600');
		header('Content-Type: text/plain; charset=UTF-8');
		echo 'Site en maintenance.';
		exit;
	}
}, -10);

// Meta noindex (securite supplementaire si bot ignore robots.txt)
add_action('wp_head', function () {
	if (!zz_maintenance_is_active()) return;
	echo '<meta name="robots" content="noindex, nofollow">' . "\n";
}, 1);

/* =========================================================================
   E. INTERCEPTION DES PAGES — Afficher la maintenance
   ========================================================================= */

add_action('template_redirect', function () {
	if (!zz_maintenance_is_active()) return;

	// Admins connectes : acces libre
	if (is_user_logged_in() && current_user_can('manage_options')) return;

	// Ne pas bloquer wp-login, wp-admin, cron, ajax, REST, robots.txt
	$uri = $_SERVER['REQUEST_URI'] ?? '';
	if (
		strpos($uri, 'wp-login') !== false ||
		strpos($uri, 'wp-admin') !== false ||
		strpos($uri, 'wp-cron') !== false ||
		strpos($uri, 'admin-ajax') !== false ||
		strpos($uri, 'robots.txt') !== false ||
		(defined('REST_REQUEST') && REST_REQUEST)
	) {
		return;
	}

	// HTTP 503 Service Unavailable + Retry-After
	status_header(503);
	header('Retry-After: 3600');
	header('Content-Type: text/html; charset=UTF-8');

	zz_maintenance_render_page();
	exit;
}, 0);

/* =========================================================================
   F. HTML/CSS — Page maintenance inline
   ========================================================================= */

function zz_maintenance_render_page() {
	// Chemin vers les fonts du theme
	$fonts_uri = content_url('/themes/zougzoug/assets/fonts');

	// Charger les images du hero depuis home.json
	$images = [];
	$json_path = get_theme_file_path('data/home.json');
	if (file_exists($json_path)) {
		$data = json_decode(file_get_contents($json_path), true);
		if (!empty($data['hero']['slides'])) {
			foreach ($data['hero']['slides'] as $slide) {
				foreach (['left', 'right'] as $side) {
					if (!empty($slide[$side])) {
						$url = wp_get_attachment_url((int) $slide[$side]);
						if ($url) {
							$images[] = $url;
						}
					}
				}
			}
		}
	}

	$img_count = count($images);
	// Duree par image en secondes, crossfade inclus
	$duration_per = 5;
	$total_duration = $img_count > 0 ? $img_count * $duration_per : 0;
	// Pourcentage visible + fondu pour chaque image
	$fade_pct = $img_count > 0 ? round(100 / $img_count, 4) : 0;
	$hold_pct = $img_count > 0 ? round($fade_pct * 0.7, 4) : 0;
	?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<title>Atelier ZougZoug — Bient&ocirc;t de retour</title>
	<style>
		@font-face {
			font-family: 'General Sans';
			src: url('<?php echo esc_url($fonts_uri); ?>/GeneralSans-Regular.woff2') format('woff2');
			font-weight: 400;
			font-display: swap;
		}
		@font-face {
			font-family: 'General Sans';
			src: url('<?php echo esc_url($fonts_uri); ?>/GeneralSans-Medium.woff2') format('woff2');
			font-weight: 500;
			font-display: swap;
		}
		@font-face {
			font-family: 'General Sans';
			src: url('<?php echo esc_url($fonts_uri); ?>/GeneralSans-Semibold.woff2') format('woff2');
			font-weight: 600;
			font-display: swap;
		}

		*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

		body {
			font-family: 'General Sans', -apple-system, BlinkMacSystemFont, sans-serif;
			background: #1A1A1A;
			color: #FFFFFF;
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			-webkit-font-smoothing: antialiased;
			overflow: hidden;
		}

		/* --- Slideshow fond --- */
		.maintenance-bg {
			position: fixed;
			inset: 0;
			z-index: 0;
		}

		.maintenance-bg img {
			position: absolute;
			inset: 0;
			width: 100%;
			height: 100%;
			object-fit: cover;
			opacity: 0;
			animation: zz-fade <?php echo $total_duration; ?>s infinite;
			transform: scale(1.05);
		}

		<?php if ($img_count > 0) : ?>
		<?php for ($i = 0; $i < $img_count; $i++) : ?>
		.maintenance-bg img:nth-child(<?php echo $i + 1; ?>) {
			animation-delay: <?php echo $i * $duration_per; ?>s;
		}
		<?php endfor; ?>
		<?php endif; ?>

		@keyframes zz-fade {
			0%                        { opacity: 0; transform: scale(1.05); }
			<?php echo round($fade_pct * 0.1, 2); ?>%  { opacity: 1; transform: scale(1.02); }
			<?php echo $hold_pct; ?>% { opacity: 1; transform: scale(1); }
			<?php echo $fade_pct; ?>% { opacity: 0; transform: scale(1); }
			100%                      { opacity: 0; transform: scale(1.05); }
		}

		/* --- Overlay noir --- */
		.maintenance-overlay {
			position: fixed;
			inset: 0;
			z-index: 1;
			background: rgba(0, 0, 0, 0.65);
		}

		/* --- Contenu --- */
		.maintenance {
			position: relative;
			z-index: 2;
			text-align: center;
			padding: 40px 24px;
			max-width: 520px;
			width: 100%;
		}

		.maintenance-logo {
			width: 200px;
			height: auto;
			margin: 0 auto 48px;
			display: block;
		}

		/* --- Animation trace → fill — cale sur le rythme du slideshow --- */
		.maintenance-logo path {
			fill: transparent;
			stroke: rgba(255, 255, 255, 0.85);
			stroke-width: 0.8;
			stroke-linecap: round;
			stroke-linejoin: round;
		}

		/* Monogramme ZZ */
		.maintenance-logo .zz-mono path {
			stroke-dasharray: 3000;
			stroke-dashoffset: 3000;
			animation: zz-trace-mono <?php echo $duration_per; ?>s cubic-bezier(0.4, 0, 0.2, 1) infinite;
		}

		/* Texte "atelier" — fondu synchro */
		.maintenance-logo .zz-text path {
			fill: white;
			stroke: none;
			animation: zz-text-fade <?php echo $duration_per; ?>s cubic-bezier(0.4, 0, 0.2, 1) infinite;
		}

		@keyframes zz-trace-mono {
			0%   { stroke-dashoffset: 3000; fill: transparent; stroke-opacity: 0; }
			3%   { stroke-opacity: 0.8; }
			32%  { stroke-dashoffset: 0; fill: transparent; stroke-opacity: 0.6; }
			36%  { fill: rgba(255,255,255,0.4); stroke-opacity: 0.3; }
			42%  { fill: rgba(255,255,255,1); stroke-opacity: 0; }
			75%  { fill: rgba(255,255,255,1); }
			88%  { fill: transparent; }
			100% { fill: transparent; stroke-dashoffset: 3000; stroke-opacity: 0; }
		}

		@keyframes zz-text-fade {
			0%   { opacity: 0; }
			32%  { opacity: 0; }
			42%  { opacity: 1; }
			75%  { opacity: 1; }
			88%  { opacity: 0; }
			100% { opacity: 0; }
		}

		.maintenance-title {
			font-size: 11px;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 0.2em;
			margin-bottom: 8px;
			opacity: 0.7;
		}

		.maintenance-subtitle {
			font-size: 14px;
			font-weight: 400;
			opacity: 0.4;
			margin-bottom: 48px;
		}

		.maintenance-separator {
			width: 40px;
			height: 1px;
			background: rgba(255, 255, 255, 0.15);
			margin: 0 auto 48px;
		}

		.maintenance-heading {
			font-size: 32px;
			font-weight: 500;
			line-height: 1.3;
			margin-bottom: 16px;
			letter-spacing: -0.01em;
		}

		.maintenance-text {
			font-size: 16px;
			font-weight: 400;
			line-height: 1.7;
			opacity: 0.5;
			margin-bottom: 48px;
		}

		.maintenance-links {
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 32px;
		}

		.maintenance-links a {
			display: flex;
			align-items: center;
			gap: 8px;
			color: #FFFFFF;
			text-decoration: none;
			font-size: 14px;
			font-weight: 500;
			opacity: 0.6;
			transition: opacity 0.3s ease;
		}

		.maintenance-links a:hover {
			opacity: 1;
		}

		.maintenance-links svg {
			width: 18px;
			height: 18px;
			flex-shrink: 0;
		}

		@media (max-width: 480px) {
			.maintenance-logo { width: 140px; margin-bottom: 36px; }
			.maintenance-heading { font-size: 26px; }
			.maintenance-text { font-size: 15px; }
			.maintenance-links { flex-direction: column; gap: 20px; }
		}
	</style>
</head>
<body>
	<?php if ($img_count > 0) : ?>
	<div class="maintenance-bg">
		<?php foreach ($images as $img_url) : ?>
		<img src="<?php echo esc_url($img_url); ?>" alt="" loading="eager">
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	<div class="maintenance-overlay"></div>

	<main class="maintenance">
		<!-- Logo monogramme ZZ — animation trace → fill -->
		<svg class="maintenance-logo" viewBox="0 0 344 405" fill="none" xmlns="http://www.w3.org/2000/svg">
			<g class="zz-mono">
				<path d="M108.124 234.549C110.988 231.89 114.153 229.559 117.523 227.616L117.532 227.585C138.517 215.462 163.527 193.891 163.527 159.945V132.487H72.0741V159.126H136.84V159.949C136.84 177.014 125.844 192.018 104.156 204.542L104.147 204.572C99.0572 207.514 94.2857 211.03 89.9639 215.039C73.0345 230.74 65.4822 248.424 65.4822 272.378V339.643H144.419C144.419 339.643 156.489 328.217 156.489 309.614C156.489 292.293 128.55 277.886 131.235 261.697C133.226 249.705 151.373 250.145 160.362 250.816V224.709C133.907 220.343 112.311 232.396 110.281 257.562C108.024 285.534 133.754 299.064 139.303 307.135C139.896 308.063 140.106 308.542 140.106 309.758C140.106 311.266 138.534 313.005 136.526 313.005H92.1684V272.378C92.1684 253.265 98.5464 243.434 108.124 234.553V234.549Z"/>
				<path d="M219.833 292.362C212.464 292.362 205.483 294.053 199.254 297.064V266.02C199.254 242.51 213.127 229.272 224.277 220.16C256.869 193.517 278.513 181.864 278.513 152.729C278.513 140.531 270.673 132.557 258.803 132.557C234.012 132.561 232.562 169.946 216.257 169.946C199.952 169.946 205.732 132.483 205.732 132.483C205.732 132.483 182.163 132.483 176.505 132.483C176.505 132.483 167.346 187.202 205.003 189.852C244.074 192.601 248.968 150.484 258.17 150.484C260.742 150.484 261.807 151.879 261.807 154.746C261.807 175.28 230.546 188.218 206.985 206.537C189.602 220.055 172.576 234.867 172.576 269.423V336.763C172.52 337.717 172.48 338.671 172.48 339.639H172.576H199.166H199.262V337.678C200.258 327.215 209.102 319.005 219.841 319.005C231.24 319.005 240.516 328.261 240.516 339.643H267.202C267.202 313.575 245.96 292.371 219.846 292.371L219.833 292.362Z"/>
			</g>
			<g class="zz-text">
				<path d="M92.6224 115.187V107.404C89.0253 112.594 84.8955 114.856 81.163 114.856C76.0991 114.856 71.834 111.265 71.834 105.343C71.834 97.4948 79.2946 95.5643 92.2863 93.8343V90.444C92.2863 85.1886 88.89 82.4607 85.8254 81.9944L77.8977 92.0389L71.7685 85.5852C76.0991 82.5914 81.2285 80.4649 86.4933 80.4649C93.155 80.4649 99.485 83.7898 99.485 91.8385V107.204L105.749 110.93L92.6224 115.187ZM92.2907 95.5643C84.8301 97.0285 79.7661 99.556 79.7661 104.41C79.7661 107.801 82.2981 109.265 84.961 109.265C87.624 109.265 90.2913 107.801 92.2907 105.073V95.5599V95.5643Z"/>
				<path d="M121.806 114.851C116.873 114.851 112.411 111.06 112.411 104.21V83.3235H106.081L119.605 71.9499V81.1316H130.799L130.201 83.3279H119.605V102.685C119.605 107.539 122.605 109.936 126.267 109.936C128.332 109.936 130.397 109.204 132.396 108.075C129.799 112.599 125.669 114.86 121.801 114.86L121.806 114.851Z"/>
				<path d="M165.517 102.48C164.583 107.801 160.388 114.851 151.526 114.851C143.131 114.851 135.736 108.533 135.736 98.2879C135.736 89.2413 141.533 80.4605 152.259 80.4605C160.855 80.4605 165.12 86.9796 165.452 94.6274H142.131V95.4902C142.131 101.944 145.663 108.263 154.324 108.263C158.388 108.263 161.986 106.533 165.517 102.476V102.48ZM142.398 92.4355H157.59C157.655 91.9038 157.655 91.3722 157.655 90.7708C157.655 86.5134 155.856 82.3213 151.792 82.3213C146.794 82.3213 143.397 87.045 142.398 92.4311V92.4355Z"/>
				<path d="M182.241 109.33L188.17 114.119H169.114L175.043 109.33V73.6799L168.577 69.8234L182.237 65.3655V109.335L182.241 109.33Z"/>
				<path d="M204.497 109.33L210.425 114.119H191.37L197.298 109.33V88.775L190.833 84.9184L204.492 80.4605V109.33H204.497ZM195.032 70.6208C195.032 67.6925 197.298 65.5005 200.162 65.5005C203.025 65.5005 205.291 67.6968 205.291 70.6208C205.291 73.5449 202.96 75.8109 200.162 75.8109C197.363 75.8109 195.032 73.6146 195.032 70.6208Z"/>
				<path d="M243.537 102.48C242.603 107.801 238.408 114.851 229.546 114.851C221.151 114.851 213.756 108.533 213.756 98.2879C213.756 89.2413 219.553 80.4605 230.279 80.4605C238.875 80.4605 243.14 86.9796 243.472 94.6274H220.151V95.4902C220.151 101.944 223.683 108.263 232.344 108.263C236.408 108.263 240.005 106.533 243.537 102.476V102.48ZM220.422 92.4355H235.614C235.679 91.9038 235.679 91.3722 235.679 90.7708C235.679 86.5134 233.881 82.3213 229.816 82.3213C224.818 82.3213 221.422 87.045 220.422 92.4311V92.4355Z"/>
				<path d="M272.655 89.7075C270.922 88.178 268.922 87.5113 267.124 87.5113C264.125 87.5113 261.527 89.3066 261.06 92.0346V109.33L267.656 114.119H247.938L253.866 109.33V88.775L247.27 84.8487L260.663 80.4562V89.3023C261.994 83.6503 265.461 80.6566 269.927 80.6566C271.323 80.6566 273.057 80.9224 274.659 81.4541L272.659 89.7032L272.655 89.7075Z"/>
			</g>
		</svg>

		<p class="maintenance-title">Atelier ZougZoug</p>
		<p class="maintenance-subtitle">Charlotte Auroux &mdash; C&eacute;ramiste, Brioude</p>

		<div class="maintenance-separator"></div>

		<h1 class="maintenance-heading">Bient&ocirc;t de retour</h1>
		<p class="maintenance-text">
			En attendant, retrouvez-moi sur Instagram<br>
			ou &eacute;crivez-moi directement par email.
		</p>

		<div class="maintenance-links">
			<a href="https://www.instagram.com/atelier_zougzoug/" target="_blank" rel="noopener">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5"/></svg>
				Instagram
			</a>
			<a href="mailto:atelierzougzoug@gmail.com">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 7l-10 7L2 7"/></svg>
				Email
			</a>
		</div>
	</main>
</body>
</html>
	<?php
}
