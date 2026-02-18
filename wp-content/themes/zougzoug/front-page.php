<?php
/**
 * Template : Accueil
 */

get_header();
$data = zz_get_data('home');
$uri = get_template_directory_uri();
?>

	<!-- ======================== HERO — Diptyque Parallax ======================== -->
	<section class="hero" id="hero">
		<!-- Logo central fixe -->
		<div class="hero-logo">
			<svg viewBox="0 0 344 405" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M108.124 234.549C110.988 231.89 114.153 229.559 117.523 227.616L117.532 227.585C138.517 215.462 163.527 193.891 163.527 159.945V132.487H72.0741V159.126H136.84V159.949C136.84 177.014 125.844 192.018 104.156 204.542L104.147 204.572C99.0572 207.514 94.2857 211.03 89.9639 215.039C73.0345 230.74 65.4822 248.424 65.4822 272.378V339.643H144.419C144.419 339.643 156.489 328.217 156.489 309.614C156.489 292.293 128.55 277.886 131.235 261.697C133.226 249.705 151.373 250.145 160.362 250.816V224.709C133.907 220.343 112.311 232.396 110.281 257.562C108.024 285.534 133.754 299.064 139.303 307.135C139.896 308.063 140.106 308.542 140.106 309.758C140.106 311.266 138.534 313.005 136.526 313.005H92.1684V272.378C92.1684 253.265 98.5464 243.434 108.124 234.553V234.549Z" fill="white"/>
				<path d="M219.833 292.362C212.464 292.362 205.483 294.053 199.254 297.064V266.02C199.254 242.51 213.127 229.272 224.277 220.16C256.869 193.517 278.513 181.864 278.513 152.729C278.513 140.531 270.673 132.557 258.803 132.557C234.012 132.561 232.562 169.946 216.257 169.946C199.952 169.946 205.732 132.483 205.732 132.483C205.732 132.483 182.163 132.483 176.505 132.483C176.505 132.483 167.346 187.202 205.003 189.852C244.074 192.601 248.968 150.484 258.17 150.484C260.742 150.484 261.807 151.879 261.807 154.746C261.807 175.28 230.546 188.218 206.985 206.537C189.602 220.055 172.576 234.867 172.576 269.423V336.763C172.52 337.717 172.48 338.671 172.48 339.639H172.576H199.166H199.262V337.678C200.258 327.215 209.102 319.005 219.841 319.005C231.24 319.005 240.516 328.261 240.516 339.643H267.202C267.202 313.575 245.96 292.371 219.846 292.371L219.833 292.362Z" fill="white"/>
				<g>
					<path d="M92.6224 115.187V107.404C89.0253 112.594 84.8955 114.856 81.163 114.856C76.0991 114.856 71.834 111.265 71.834 105.343C71.834 97.4948 79.2946 95.5643 92.2863 93.8343V90.444C92.2863 85.1886 88.89 82.4607 85.8254 81.9944L77.8977 92.0389L71.7685 85.5852C76.0991 82.5914 81.2285 80.4649 86.4933 80.4649C93.155 80.4649 99.485 83.7898 99.485 91.8385V107.204L105.749 110.93L92.6224 115.187ZM92.2907 95.5643C84.8301 97.0285 79.7661 99.556 79.7661 104.41C79.7661 107.801 82.2981 109.265 84.961 109.265C87.624 109.265 90.2913 107.801 92.2907 105.073V95.5599V95.5643Z" fill="white"/>
					<path d="M121.806 114.851C116.873 114.851 112.411 111.06 112.411 104.21V83.3235H106.081L119.605 71.9499V81.1316H130.799L130.201 83.3279H119.605V102.685C119.605 107.539 122.605 109.936 126.267 109.936C128.332 109.936 130.397 109.204 132.396 108.075C129.799 112.599 125.669 114.86 121.801 114.86L121.806 114.851Z" fill="white"/>
					<path d="M165.517 102.48C164.583 107.801 160.388 114.851 151.526 114.851C143.131 114.851 135.736 108.533 135.736 98.2879C135.736 89.2413 141.533 80.4605 152.259 80.4605C160.855 80.4605 165.12 86.9796 165.452 94.6274H142.131V95.4902C142.131 101.944 145.663 108.263 154.324 108.263C158.388 108.263 161.986 106.533 165.517 102.476V102.48ZM142.398 92.4355H157.59C157.655 91.9038 157.655 91.3722 157.655 90.7708C157.655 86.5134 155.856 82.3213 151.792 82.3213C146.794 82.3213 143.397 87.045 142.398 92.4311V92.4355Z" fill="white"/>
					<path d="M182.241 109.33L188.17 114.119H169.114L175.043 109.33V73.6799L168.577 69.8234L182.237 65.3655V109.335L182.241 109.33Z" fill="white"/>
					<path d="M204.497 109.33L210.425 114.119H191.37L197.298 109.33V88.775L190.833 84.9184L204.492 80.4605V109.33H204.497ZM195.032 70.6208C195.032 67.6925 197.298 65.5005 200.162 65.5005C203.025 65.5005 205.291 67.6968 205.291 70.6208C205.291 73.5449 202.96 75.8109 200.162 75.8109C197.363 75.8109 195.032 73.6146 195.032 70.6208Z" fill="white"/>
					<path d="M243.537 102.48C242.603 107.801 238.408 114.851 229.546 114.851C221.151 114.851 213.756 108.533 213.756 98.2879C213.756 89.2413 219.553 80.4605 230.279 80.4605C238.875 80.4605 243.14 86.9796 243.472 94.6274H220.151V95.4902C220.151 101.944 223.683 108.263 232.344 108.263C236.408 108.263 240.005 106.533 243.537 102.476V102.48ZM220.422 92.4355H235.614C235.679 91.9038 235.679 91.3722 235.679 90.7708C235.679 86.5134 233.881 82.3213 229.816 82.3213C224.818 82.3213 221.422 87.045 220.422 92.4311V92.4355Z" fill="white"/>
					<path d="M272.655 89.7075C270.922 88.178 268.922 87.5113 267.124 87.5113C264.125 87.5113 261.527 89.3066 261.06 92.0346V109.33L267.656 114.119H247.938L253.866 109.33V88.775L247.27 84.8487L260.663 80.4562V89.3023C261.994 83.6503 265.461 80.6566 269.927 80.6566C271.323 80.6566 273.057 80.9224 274.659 81.4541L272.659 89.7032L272.655 89.7075Z" fill="white"/>
				</g>
			</svg>
		</div>

		<!-- Tagline -->
		<p class="hero-tagline"><?php echo nl2br(esc_html($data['hero']['tagline'] ?? "La céramique comme langage commun\nentre design et architecture.")); ?></p>

		<!-- Swiper slider vertical -->
		<div class="hero-slider">
			<div class="swiper" id="heroSwiper">
				<div class="swiper-wrapper">
					<?php if (!empty($data['hero']['slides'])) : ?>
						<?php foreach ($data['hero']['slides'] as $slide) : ?>
							<div class="swiper-slide">
								<div class="hero-half" data-swiper-parallax-y="-20%">
									<span class="hero-bg hero-bg--left" style="background-image: url('<?php echo esc_url($uri . '/assets/img/' . $slide['left']); ?>')"></span>
								</div>
								<div class="hero-half" data-swiper-parallax-y="35%">
									<span class="hero-bg hero-bg--right" style="background-image: url('<?php echo esc_url($uri . '/assets/img/' . $slide['right']); ?>')"></span>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- Spacer -->
	<div class="hero-spacer"></div>

	<!-- ======================== EXPERIENCE — Statement ======================== -->
	<section class="experience" id="parcours">
		<div class="exp-statement">
			<div class="exp-statement-inner">
				<!-- Logo ZZ outline -->
				<div class="statement-logo">
					<svg viewBox="0 0 50 48" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path class="logo-trace" d="M10.0033 23.6486C10.6751 23.0327 11.4176 22.4925 12.2082 22.0422L12.2102 22.0351C17.133 19.2261 23 14.2279 23 6.36224V0H1.54637V6.17241H16.7397V6.36325C16.7397 10.3173 14.1601 13.7938 9.0724 16.6957L9.07035 16.7028C7.87626 17.3843 6.75694 18.1992 5.74309 19.1281C1.77167 22.7661 0 26.8636 0 32.414V48H18.5176C18.5176 48 21.3492 45.3525 21.3492 41.042C21.3492 37.0284 14.795 33.6903 15.4248 29.9392C15.8918 27.1604 20.1489 27.2624 22.2575 27.4179V21.3687C16.0516 20.3569 10.9854 23.1498 10.5092 28.9809C9.97974 35.4623 16.0157 38.5975 17.3173 40.4675C17.4566 40.6826 17.5058 40.7936 17.5058 41.0754C17.5058 41.4247 17.1371 41.8276 16.666 41.8276H6.26025V32.414C6.26025 27.9854 7.75644 25.7074 10.0033 23.6496V23.6486Z" stroke="#1A1A1A" stroke-width="0.3" fill="none"/>
						<path class="logo-trace" d="M36.1645 37.0448C34.4271 37.0448 32.7813 37.4365 31.3125 38.1343V30.9412C31.3125 25.4939 34.5836 22.4264 37.2123 20.3151C44.8969 14.1418 50 11.4419 50 4.69106C50 1.86491 48.1514 0.0171662 45.3528 0.0171662C39.5076 0.0181759 39.1659 8.68037 35.3215 8.68037C31.4772 8.68037 32.84 0 32.84 0C32.84 0 27.2829 0 25.949 0C25.949 0 23.7896 12.6788 32.6681 13.2927C41.8801 13.9298 43.0339 4.17106 45.2036 4.17106C45.8098 4.17106 46.061 4.49416 46.061 5.15854C46.061 9.91624 38.6904 12.914 33.1354 17.1588C29.0368 20.2909 25.0226 23.7228 25.0226 31.7297V47.3326C25.0093 47.5537 25 47.7748 25 47.999H25.0226H31.2919H31.3146V47.5446C31.5493 45.1203 33.6346 43.2181 36.1666 43.2181C38.854 43.2181 41.0412 45.3627 41.0412 48H47.3332C47.3332 41.96 42.3247 37.0468 36.1676 37.0468L36.1645 37.0448Z" stroke="#1A1A1A" stroke-width="0.3" fill="none"/>
					</svg>
				</div>

				<!-- Texte central -->
				<p class="parcours-text"><?php echo esc_html($data['statement']['text'] ?? 'Une poterie qui se vit, se touche, s\'inscrit dans le temps et l\'espace. Des pièces conçues pour un lieu, un usage, une atmosphère.'); ?></p>

				<!-- 5 photos reveal -->
				<?php if (!empty($data['statement']['images'])) : ?>
					<?php foreach ($data['statement']['images'] as $i => $img) : ?>
						<div class="reveal-img reveal-img--<?php echo $i + 1; ?>">
							<img src="<?php echo esc_url($uri . '/assets/img/' . $img['src']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy">
						</div>
					<?php endforeach; ?>
				<?php endif; ?>

				<!-- CTA -->
				<a href="<?php echo esc_url(home_url('/collaborations/')); ?>" class="cta-button cta-projets-btn">Découvrir les collaborations</a>
			</div>
		</div>
	</section>

	<!-- ======================== LUMINAIRES ======================== -->
	<div class="luminaires-wrapper">
		<section class="luminaires">
			<div class="luminaires-visual">
				<div class="luminaires-img luminaires-img--main">
					<img src="<?php echo esc_url($uri . '/assets/img/' . ($data['luminaires']['img_main'] ?? 'luminaire-adele.webp')); ?>" alt="<?php echo esc_attr($data['luminaires']['img_main_alt'] ?? 'Lampe à poser en céramique'); ?>">
				</div>
				<div class="luminaires-img luminaires-img--context">
					<img src="<?php echo esc_url($uri . '/assets/img/' . ($data['luminaires']['img_context'] ?? 'luminaire-padam.webp')); ?>" alt="<?php echo esc_attr($data['luminaires']['img_context_alt'] ?? 'Lampe en céramique en situation'); ?>">
				</div>
			</div>
			<div class="luminaires-content">
				<span class="section-label"><?php echo esc_html($data['luminaires']['label'] ?? 'Luminaires sur mesure'); ?></span>
				<?php if (!empty($data['luminaires']['texts'])) : ?>
					<?php foreach ($data['luminaires']['texts'] as $text) : ?>
						<p class="luminaires-text"><?php echo esc_html($text); ?></p>
					<?php endforeach; ?>
				<?php endif; ?>
				<div class="section-references">
					<em class="section-references-label"><?php echo esc_html($data['luminaires']['ref_label'] ?? 'Projets réalisés avec'); ?></em>
					<span><?php echo esc_html($data['luminaires']['ref_text'] ?? ''); ?></span>
				</div>
				<a href="<?php echo esc_url(home_url('/contact/')); ?>" class="cta-button">Discuter d'un projet</a>
			</div>
		</section>
	</div>

	<!-- ======================== VAISSELLE ======================== -->
	<div class="vaisselle-wrapper">
		<section class="vaisselle">
			<div class="vaisselle-content">
				<span class="section-label"><?php echo esc_html($data['vaisselle']['label'] ?? 'Art de la table'); ?></span>
				<?php if (!empty($data['vaisselle']['texts'])) : ?>
					<?php foreach ($data['vaisselle']['texts'] as $text) : ?>
						<p class="vaisselle-text"><?php echo esc_html($text); ?></p>
					<?php endforeach; ?>
				<?php endif; ?>
				<div class="section-references">
					<em class="section-references-label"><?php echo esc_html($data['vaisselle']['ref_label'] ?? 'Collaborations'); ?></em>
					<span><?php echo esc_html($data['vaisselle']['ref_text'] ?? ''); ?></span>
				</div>
				<a href="<?php echo esc_url(home_url('/contact/')); ?>" class="cta-button">Collaborer avec l'atelier</a>
			</div>
			<div class="vaisselle-visual">
				<div class="vaisselle-img vaisselle-img--main">
					<img src="<?php echo esc_url($uri . '/assets/img/' . ($data['vaisselle']['img_main'] ?? 'vaisselle-assiettes.webp')); ?>" alt="<?php echo esc_attr($data['vaisselle']['img_main_alt'] ?? 'Assiettes en grès'); ?>">
				</div>
				<div class="vaisselle-img vaisselle-img--mood">
					<img src="<?php echo esc_url($uri . '/assets/img/' . ($data['vaisselle']['img_mood'] ?? 'vaisselle-mains.webp')); ?>" alt="<?php echo esc_attr($data['vaisselle']['img_mood_alt'] ?? 'Mains dans une assiette en céramique'); ?>">
				</div>
			</div>
		</section>
	</div>

	<!-- ======================== GALERIE INSTAGRAM ======================== -->
	<section class="insta-gallery">
		<div class="insta-header">
			<a href="https://www.instagram.com/atelier_zougzoug/" target="_blank" rel="noopener" class="insta-link">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5"/></svg>
				<span>@atelier_zougzoug</span>
			</a>
		</div>
		<div class="insta-grid">
			<?php if (!empty($data['instagram']['images'])) : ?>
				<?php foreach ($data['instagram']['images'] as $img) : ?>
					<div class="insta-item">
						<img src="<?php echo esc_url($uri . '/assets/img/' . $img['src']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy">
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</section>

	<!-- ======================== CTA FINAL ======================== -->
	<section class="cta-final">
		<p class="cta-final-text">Un projet sur mesure en céramique ?<br>Parlons-en.</p>
		<a href="<?php echo esc_url(home_url('/contact/')); ?>" class="cta-button cta-button--light">Prendre contact</a>
	</section>

<?php get_footer(); ?>
