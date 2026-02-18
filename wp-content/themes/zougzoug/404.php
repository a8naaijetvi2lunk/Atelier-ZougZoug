<?php
/**
 * Template : 404 — Page introuvable
 */

get_header();
?>

  <!-- ======================== 404 CONTENT ======================== -->
  <main class="error-page">
    <div class="error-inner">
      <span class="error-code">404</span>
      <h1 class="error-title">Cette page n'existe pas.</h1>
      <p class="error-text">La pièce que vous cherchez a peut-être été tournée, émaillée et rangée ailleurs.</p>
      <a href="<?php echo esc_url(home_url('/')); ?>" class="cta-button">Retour à l'accueil</a>
    </div>
  </main>

<?php get_footer(); ?>
