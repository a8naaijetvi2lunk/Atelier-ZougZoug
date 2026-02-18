<?php
/**
 * Fallback template — redirige vers l'accueil
 */

get_header();
?>

<main style="padding: 160px 40px 100px; max-width: 1200px; margin: 0 auto;">
	<h1>Page en construction</h1>
	<p>Cette page n'a pas encore de template dédié.</p>
	<a href="<?php echo esc_url(home_url('/')); ?>" class="cta-button">Retour à l'accueil</a>
</main>

<?php get_footer(); ?>
