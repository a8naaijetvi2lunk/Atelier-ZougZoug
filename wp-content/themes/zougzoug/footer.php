	<!-- ======================== FOOTER ======================== -->
	<footer class="site-footer">
		<div class="footer-inner">

			<!-- Colonne 1 : Logo + baseline -->
			<div class="footer-brand">
				<a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo" aria-label="Atelier ZougZoug — Accueil">
					<svg width="50" height="48" viewBox="0 0 50 48" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M10.0033 23.6486C10.6751 23.0327 11.4176 22.4925 12.2082 22.0422L12.2102 22.0351C17.133 19.2261 23 14.2279 23 6.36224V0H1.54637V6.17241H16.7397V6.36325C16.7397 10.3173 14.1601 13.7938 9.0724 16.6957L9.07035 16.7028C7.87626 17.3843 6.75694 18.1992 5.74309 19.1281C1.77167 22.7661 0 26.8636 0 32.414V48H18.5176C18.5176 48 21.3492 45.3525 21.3492 41.042C21.3492 37.0284 14.795 33.6903 15.4248 29.9392C15.8918 27.1604 20.1489 27.2624 22.2575 27.4179V21.3687C16.0516 20.3569 10.9854 23.1498 10.5092 28.9809C9.97974 35.4623 16.0157 38.5975 17.3173 40.4675C17.4566 40.6826 17.5058 40.7936 17.5058 41.0754C17.5058 41.4247 17.1371 41.8276 16.666 41.8276H6.26025V32.414C6.26025 27.9854 7.75644 25.7074 10.0033 23.6496V23.6486Z" fill="#FFFFFF"/>
						<path d="M36.1645 37.0448C34.4271 37.0448 32.7813 37.4365 31.3125 38.1343V30.9412C31.3125 25.4939 34.5836 22.4264 37.2123 20.3151C44.8969 14.1418 50 11.4419 50 4.69106C50 1.86491 48.1514 0.0171662 45.3528 0.0171662C39.5076 0.0181759 39.1659 8.68037 35.3215 8.68037C31.4772 8.68037 32.84 0 32.84 0C32.84 0 27.2829 0 25.949 0C25.949 0 23.7896 12.6788 32.6681 13.2927C41.8801 13.9298 43.0339 4.17106 45.2036 4.17106C45.8098 4.17106 46.061 4.49416 46.061 5.15854C46.061 9.91624 38.6904 12.914 33.1354 17.1588C29.0368 20.2909 25.0226 23.7228 25.0226 31.7297V47.3326C25.0093 47.5537 25 47.7748 25 47.999H25.0226H31.2919H31.3146V47.5446C31.5493 45.1203 33.6346 43.2181 36.1666 43.2181C38.854 43.2181 41.0412 45.3627 41.0412 48H47.3332C47.3332 41.96 42.3247 37.0468 36.1676 37.0468L36.1645 37.0448Z" fill="#FFFFFF"/>
					</svg>
				</a>
				<p class="footer-baseline">Céramiste — Brioude, Auvergne</p>
			</div>

			<!-- Colonne 2 : Navigation -->
			<div class="footer-col">
				<span class="footer-col-title">Navigation</span>
				<nav class="footer-nav">
					<a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a>
					<a href="<?php echo esc_url(home_url('/a-propos/')); ?>">À propos</a>
					<a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a>
				</nav>
			</div>

			<!-- Colonne 3 : Services -->
			<div class="footer-col">
				<span class="footer-col-title">Services</span>
				<nav class="footer-nav">
					<a href="<?php echo esc_url(home_url('/cours/')); ?>">Cours de céramique</a>
					<a href="<?php echo esc_url(home_url('/revendeurs/')); ?>">Revendeurs & Évènements</a>
				</nav>
			</div>

			<!-- Colonne 4 : Contact -->
			<div class="footer-col">
				<span class="footer-col-title">Contact</span>
				<div class="footer-contact">
					<a href="mailto:atelierzougzoug@gmail.com">atelierzougzoug@gmail.com</a>
					<a href="tel:+33660199818">06 60 19 98 18</a>
					<p>6 rue de la Terrasse<br>43100 Brioude</p>
				</div>
				<div class="footer-social">
					<a href="https://www.instagram.com/atelier_zougzoug/" target="_blank" rel="noopener" aria-label="Instagram">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5"/></svg>
					</a>
				</div>
			</div>

		</div>

		<div class="footer-bottom">
			<p>&copy; <?php echo date('Y'); ?> Atelier ZougZoug — Charlotte Auroux. Tous droits réservés.</p>
		</div>
	</footer>

	<?php wp_footer(); ?>
</body>
</html>
